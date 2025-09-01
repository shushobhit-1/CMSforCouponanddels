<?php

namespace App\Services;

use App\Models\Affiliate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CuelinksAdapter implements AffiliateAdapterInterface
{
    protected $baseUrl = 'https://api.cuelinks.com';

    public function testConnection(Affiliate $affiliate): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/profile');

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Cuelinks connection test failed: ' . $e->getMessage());
            return false;
        }
    }

    public function syncProducts(Affiliate $affiliate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/products', [
                'limit' => 100,
                'page' => 1
            ]);

            if (!$response->successful()) {
                throw new \Exception('API request failed: ' . $response->body());
            }

            $data = $response->json();
            $syncedCount = 0;

            foreach ($data['results'] ?? [] as $productData) {
                $this->createOrUpdateProduct($productData, $affiliate);
                $syncedCount++;
            }

            return [
                'success' => true,
                'synced_count' => $syncedCount,
                'total_available' => $data['count'] ?? 0
            ];
        } catch (\Exception $e) {
            Log::error('Cuelinks product sync failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function generateAffiliateLink(string $originalUrl, Affiliate $affiliate, array $params = []): string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/api/v2/link-convert', [
                'url' => $originalUrl,
                'sub_id' => $params['sub_id'] ?? 'coupondeals_' . time()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['short_url'] ?? $originalUrl;
            }

            return $originalUrl;
        } catch (\Exception $e) {
            Log::error('Cuelinks link generation failed: ' . $e->getMessage());
            return $originalUrl;
        }
    }

    public function getCommissionRates(Affiliate $affiliate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/advertiser-rates');

            if ($response->successful()) {
                return $response->json()['results'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Cuelinks commission rates fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getClickStatistics(Affiliate $affiliate, string $startDate, string $endDate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/reports/clicks', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            if ($response->successful()) {
                return $response->json()['results'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Cuelinks statistics fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getProductDetails(Affiliate $affiliate, string $productId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/products/' . $productId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Cuelinks product details fetch failed: ' . $e->getMessage());
            return null;
        }
    }

    public function searchProducts(Affiliate $affiliate, array $filters = []): array
    {
        try {
            $params = array_merge([
                'limit' => 50,
                'page' => 1
            ], $filters);

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/products', $params);

            if ($response->successful()) {
                return $response->json()['results'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Cuelinks product search failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getCategories(Affiliate $affiliate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/categories');

            if ($response->successful()) {
                return $response->json()['results'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Cuelinks categories fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getConversions(Affiliate $affiliate, string $startDate, string $endDate): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $affiliate->api_key,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/api/v2/reports/conversions', [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            if ($response->successful()) {
                return $response->json()['results'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Cuelinks conversions fetch failed: ' . $e->getMessage());
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
                'title' => $productData['title'],
                'description' => $productData['description'] ?? '',
                'price' => $productData['price'] ?? 0,
                'discount_price' => $productData['sale_price'] ?? null,
                'affiliate_url' => $this->generateAffiliateLink($productData['link'], $affiliate),
                'image_url' => $productData['image'] ?? null,
                'brand' => $productData['brand'] ?? null,
                'category_id' => $this->mapCategory($productData['category'] ?? null),
                'is_active' => true,
                'external_data' => $productData
            ]
        );

        // Handle product images
        if (!empty($productData['image'])) {
            try {
                $product->addMediaFromUrl($productData['image'])
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