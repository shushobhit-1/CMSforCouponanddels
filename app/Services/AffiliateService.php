<?php

namespace App\Services;

use App\Models\Affiliate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AffiliateService
{
    protected $adapters = [];

    public function __construct()
    {
        $this->initializeAdapters();
    }

    protected function initializeAdapters()
    {
        $this->adapters = [
            'vcommission' => new VCommissionAdapter(),
            'cuelinks' => new CuelinksAdapter(),
            'optimisemedia' => new OptimiseMediaAdapter(),
            'inrdeals' => new INRDealsAdapter(),
            'amazon' => new AmazonAdapter(),
            'flipkart' => new FlipkartAdapter(),
        ];
    }

    public function getAdapter(string $network): ?AffiliateAdapterInterface
    {
        return $this->adapters[$network] ?? null;
    }

    public function syncProducts(Affiliate $affiliate): array
    {
        $adapter = $this->getAdapter($affiliate->slug);
        
        if (!$adapter) {
            throw new \Exception("Adapter not found for {$affiliate->name}");
        }

        try {
            return $adapter->syncProducts($affiliate);
        } catch (\Exception $e) {
            Log::error("Failed to sync products from {$affiliate->name}: " . $e->getMessage());
            throw $e;
        }
    }

    public function testConnection(Affiliate $affiliate): bool
    {
        $adapter = $this->getAdapter($affiliate->slug);
        
        if (!$adapter) {
            return false;
        }

        try {
            return $adapter->testConnection($affiliate);
        } catch (\Exception $e) {
            Log::error("Connection test failed for {$affiliate->name}: " . $e->getMessage());
            return false;
        }
    }

    public function generateAffiliateLink(string $originalUrl, Affiliate $affiliate, array $params = []): string
    {
        $adapter = $this->getAdapter($affiliate->slug);
        
        if (!$adapter) {
            return $originalUrl;
        }

        try {
            return $adapter->generateAffiliateLink($originalUrl, $affiliate, $params);
        } catch (\Exception $e) {
            Log::error("Failed to generate affiliate link for {$affiliate->name}: " . $e->getMessage());
            return $originalUrl;
        }
    }

    public function getCommissionRates(Affiliate $affiliate): array
    {
        $adapter = $this->getAdapter($affiliate->slug);
        
        if (!$adapter) {
            return [];
        }

        try {
            return $adapter->getCommissionRates($affiliate);
        } catch (\Exception $e) {
            Log::error("Failed to get commission rates for {$affiliate->name}: " . $e->getMessage());
            return [];
        }
    }

    public function getClickStatistics(Affiliate $affiliate, string $startDate, string $endDate): array
    {
        $adapter = $this->getAdapter($affiliate->slug);
        
        if (!$adapter) {
            return [];
        }

        try {
            return $adapter->getClickStatistics($affiliate, $startDate, $endDate);
        } catch (\Exception $e) {
            Log::error("Failed to get statistics for {$affiliate->name}: " . $e->getMessage());
            return [];
        }
    }

    public function getAllNetworks(): array
    {
        return array_keys($this->adapters);
    }

    public function syncAllActiveNetworks(): array
    {
        $results = [];
        $activeAffiliates = Affiliate::where('is_active', true)->get();

        foreach ($activeAffiliates as $affiliate) {
            try {
                $results[$affiliate->slug] = $this->syncProducts($affiliate);
            } catch (\Exception $e) {
                $results[$affiliate->slug] = ['error' => $e->getMessage()];
            }
        }

        return $results;
    }
}