<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    const CACHE_TTL = 3600; // 1 hour
    const LONG_CACHE_TTL = 86400; // 24 hours

    public function getCoupons($filters = [])
    {
        $cacheKey = 'coupons:' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            $query = \App\Models\Coupon::with(['store', 'category'])
                ->where('is_active', true);
                
            // Apply filters
            if (!empty($filters['store'])) {
                $query->where('store_id', $filters['store']);
            }
            
            if (!empty($filters['category'])) {
                $query->where('category_id', $filters['category']);
            }
            
            return $query->paginate(20);
        });
    }

    public function getDeals($filters = [])
    {
        $cacheKey = 'deals:' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
            $query = \App\Models\Deal::with(['store', 'category'])
                ->where('is_active', true);
                
            // Apply filters
            if (!empty($filters['store'])) {
                $query->where('store_id', $filters['store']);
            }
            
            if (!empty($filters['category'])) {
                $query->where('category_id', $filters['category']);
            }
            
            return $query->paginate(20);
        });
    }

    public function getPopularStores()
    {
        return Cache::remember('popular_stores', self::LONG_CACHE_TTL, function () {
            return \App\Models\Store::withCount(['coupons', 'deals'])
                ->where('is_active', true)
                ->orderBy('coupons_count', 'desc')
                ->limit(12)
                ->get();
        });
    }

    public function getFeaturedCategories()
    {
        return Cache::remember('featured_categories', self::LONG_CACHE_TTL, function () {
            return \App\Models\Category::where('is_featured', true)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(8)
                ->get();
        });
    }

    public function getHomepageStats()
    {
        return Cache::remember('homepage_stats', 1800, function () { // 30 minutes
            return [
                'total_coupons' => \App\Models\Coupon::where('is_active', true)->count(),
                'total_deals' => \App\Models\Deal::where('is_active', true)->count(),
                'total_stores' => \App\Models\Store::where('is_active', true)->count(),
                'total_savings' => \App\Models\AffiliateClick::sum('conversion_value') ?? 0
            ];
        });
    }

    public function clearCouponCache($couponId = null)
    {
        if ($couponId) {
            Cache::forget("coupon:{$couponId}");
        }
        
        // Clear all coupon-related caches
        $this->clearCachePattern('coupons:*');
        Cache::forget('homepage_stats');
        Cache::forget('popular_stores');
    }

    public function clearDealCache($dealId = null)
    {
        if ($dealId) {
            Cache::forget("deal:{$dealId}");
        }
        
        // Clear all deal-related caches
        $this->clearCachePattern('deals:*');
        Cache::forget('homepage_stats');
    }

    public function clearStoreCache($storeId = null)
    {
        if ($storeId) {
            Cache::forget("store:{$storeId}");
        }
        
        Cache::forget('popular_stores');
        Cache::forget('homepage_stats');
    }

    public function clearAllCache()
    {
        Cache::flush();
    }

    private function clearCachePattern($pattern)
    {
        try {
            $keys = Redis::keys($pattern);
            if (!empty($keys)) {
                Redis::del($keys);
            }
        } catch (\Exception $e) {
            // Fallback: clear specific cache groups
            Cache::tags(['coupons', 'deals', 'stores'])->flush();
        }
    }

    public function optimizeImages()
    {
        // Image optimization logic
        $images = \App\Models\Media::where('mime_type', 'like', 'image/%')
            ->whereNull('optimized_at')
            ->limit(50)
            ->get();

        foreach ($images as $image) {
            $this->optimizeImage($image);
        }

        return $images->count();
    }

    private function optimizeImage($media)
    {
        try {
            $path = $media->getPath();
            
            if (!file_exists($path)) {
                return false;
            }

            // Use Intervention Image for optimization
            $img = \Intervention\Image\Facades\Image::make($path);
            
            // Optimize based on image type
            if ($media->mime_type === 'image/jpeg') {
                $img->save($path, 85); // 85% quality for JPEG
            } else {
                $img->save($path, 90); // 90% quality for others
            }

            // Generate WebP version
            $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path);
            $img->encode('webp', 80)->save($webpPath);

            // Mark as optimized
            $media->update(['optimized_at' => now()]);

            return true;
        } catch (\Exception $e) {
            \Log::error("Image optimization failed: " . $e->getMessage());
            return false;
        }
    }

    public function preloadCriticalData()
    {
        // Preload critical data that will be needed on most pages
        $this->getFeaturedCategories();
        $this->getPopularStores();
        $this->getHomepageStats();

        // Preload recent coupons
        Cache::remember('recent_coupons', 1800, function () {
            return \App\Models\Coupon::with(['store', 'category'])
                ->where('is_active', true)
                ->latest()
                ->limit(20)
                ->get();
        });

        // Preload hot deals
        Cache::remember('hot_deals', 1800, function () {
            return \App\Models\Deal::with(['store', 'category'])
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()
                ->limit(12)
                ->get();
        });
    }
}