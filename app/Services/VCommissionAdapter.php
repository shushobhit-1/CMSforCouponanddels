<?php

namespace App\Services;

use App\Models\Affiliate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VCommissionAdapter implements AffiliateAdapterInterface
{
    protected $baseUrl = 'https://api.vcommission.com/v2.0';

    public function testConnection(Affiliate $affiliate): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/user/profile');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('VCommission connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    public function syncProducts(Affiliate $affiliate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/products', [
                'limit' => 100,
                'offset' => 0,
                'status' => 'active'
            ]);

            if (!$response->successful()) {
                throw new \Exception('API request failed: ' . $response->body());
            }

            $data = $response->json();
            $syncedCount = 0;

            foreach ($data['products'] ?? [] as $productData) {
                $this->createOrUpdateProduct($productData, $affiliate);
                $syncedCount++;
            }

            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'total_available' => $data['total'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('VCommission product sync failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateAffiliateLink(string $originalUrl, Affiliate $affiliate, array $params = []): string
    {
        $trackingParams = array_merge([
            'utm_source' => 'coupondeals',
            'utm_medium' => 'affiliate',
            'utm_campaign' => 'deals',
            'aff_id' => $affiliate->publisher_id,
        ], $params);

        $separator = strpos($originalUrl, '?') !== false ? '&' : '?';
        return $originalUrl . $separator . http_build_query($trackingParams);
    }

    public function getCommissionRates(Affiliate $affiliate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/commission-rates');

            if ($response->successful()) {
                return $response->json()['rates'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('VCommission commission rates fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getClickStatistics(Affiliate $affiliate, string $startDate, string $endDate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/statistics/clicks', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'affiliate_id' => $affiliate->publisher_id
            ]);

            if ($response->successful()) {
                return $response->json()['statistics'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('VCommission statistics fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getProductDetails(Affiliate $affiliate, string $productId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/products/' . $productId);

            if ($response->successful()) {
                return $response->json()['product'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('VCommission product details fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    public function searchProducts(Affiliate $affiliate, array $filters = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/products/search', array_merge([
                'limit' => 50,
                'offset' => 0
            ], $filters));

            if ($response->successful()) {
                return $response->json()['products'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('VCommission product search failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getCategories(Affiliate $affiliate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/categories');

            if ($response->successful()) {
                return $response->json()['categories'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('VCommission categories fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getConversions(Affiliate $affiliate, string $startDate, string $endDate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/conversions', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'affiliate_id' => $affiliate->publisher_id
            ]);

            if ($response->successful()) {
                return $response->json()['conversions'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('VCommission conversions fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    protected function createOrUpdateProduct(array $productData, Affiliate $affiliate): void
    {
        $product = \App\Models\Product::updateOrCreate(
            [
                'external_id' => $productData['id'],
                'affiliate_id' => $affiliate->id
            ],
            [
                'title' => $productData['name'],
                'description' => $productData['description'] ?? '',
                'price' => $productData['price'] ?? 0,
                'discount_price' => $productData['sale_price'] ?? null,
                'affiliate_url' => $this->generateAffiliateLink($productData['url'], $affiliate),
                'image_url' => $productData['image_url'] ?? null,
                'brand' => $productData['brand'] ?? null,
                'category_id' => $this->mapCategory($productData['category'] ?? null),
                'is_active' => $productData['status'] === 'active',
                'external_data' => $productData
            ]
        );

        // Handle product images if available
        if (!empty($productData['image_url'])) {
            try {
                $product->addMediaFromUrl($productData['image_url'])
                       ->toMediaCollection('images');
            } catch (\Exception $e) {
                Log::warning('Failed to add product image: ' . $e->getMessage());
            }
        }
    }

    protected function mapCategory(?string $categoryName): ?int
    {
        if (!$categoryName) {
            return null;
        }

        $category = \App\Models\Category::where('name', 'like', '%' . $categoryName . '%')->first();
        
        if (!$category) {
            $category = \App\Models\Category::create([
                'name' => $categoryName,
                'slug' => \Str::slug($categoryName),
                'is_active' => true
            ]);
        }

        return $category->id;
    }
}