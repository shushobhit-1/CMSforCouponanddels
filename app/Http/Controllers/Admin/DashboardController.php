<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{
    User, Coupon, Deal, Product, Store, Category,
    CouponClick, CouponConversion, StoreClick, StoreConversion
};
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get current date and previous month for comparisons
        $now = Carbon::now();
        $previousMonth = $now->copy()->subMonth();
        
        // User Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $newUsersThisMonth = User::where('created_at', '>=', $now->startOfMonth())->count();
        $newUsersLastMonth = User::where('created_at', '>=', $previousMonth->startOfMonth())
            ->where('created_at', '<', $now->startOfMonth())->count();
        $userGrowth = $newUsersLastMonth > 0 ? 
            (($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100 : 0;

        // Content Statistics
        $totalCoupons = Coupon::count();
        $activeCoupons = Coupon::active()->count();
        $expiringCoupons = Coupon::expiringSoon(7)->count();
        
        $totalDeals = Deal::count();
        $activeDeals = Deal::active()->count();
        
        $totalProducts = Product::count();
        $activeProducts = Product::active()->count();
        
        $totalStores = Store::count();
        $activeStores = Store::active()->count();
        $verifiedStores = Store::verified()->count();
        
        $totalCategories = Category::count();

        // Performance Statistics
        $totalClicks = CouponClick::count() + StoreClick::count();
        $totalConversions = CouponConversion::count() + StoreConversion::count();
        $conversionRate = $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0;
        
        // Revenue Statistics
        $totalRevenue = CouponConversion::sum('revenue') + StoreConversion::sum('revenue');
        $monthlyRevenue = CouponConversion::where('created_at', '>=', $now->startOfMonth())->sum('revenue') +
                         StoreConversion::where('created_at', '>=', $now->startOfMonth())->sum('revenue');
        $previousMonthRevenue = CouponConversion::where('created_at', '>=', $previousMonth->startOfMonth())
            ->where('created_at', '<', $now->startOfMonth())->sum('revenue') +
            StoreConversion::where('created_at', '>=', $previousMonth->startOfMonth())
            ->where('created_at', '<', $now->startOfMonth())->sum('revenue');
        $revenueGrowth = $previousMonthRevenue > 0 ? 
            (($monthlyRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 : 0;

        // Recent Activity
        $recentCoupons = Coupon::with('store', 'category')
            ->latest()
            ->take(5)
            ->get();
            
        $recentDeals = Deal::with('store', 'category')
            ->latest()
            ->take(5)
            ->get();
            
        $recentProducts = Product::with('store', 'category')
            ->latest()
            ->take(5)
            ->get();
            
        $recentStores = Store::latest()
            ->take(5)
            ->get();
            
        $recentUsers = User::latest()
            ->take(5)
            ->get();

        // Top Performing Items
        $topCoupons = Coupon::with('store')
            ->orderBy('click_count', 'desc')
            ->take(10)
            ->get();
            
        $topStores = Store::orderBy('click_count', 'desc')
            ->take(10)
            ->get();
            
        $topCategories = Category::withCount(['coupons', 'deals', 'products'])
            ->orderBy('coupons_count', 'desc')
            ->take(10)
            ->get();

        // Chart Data
        $monthlyStats = $this->getMonthlyStats();
        $dailyStats = $this->getDailyStats();
        $categoryStats = $this->getCategoryStats();
        $storePerformance = $this->getStorePerformance();

        // System Health
        $systemHealth = $this->getSystemHealth();

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'newUsersThisMonth', 'userGrowth',
            'totalCoupons', 'activeCoupons', 'expiringCoupons',
            'totalDeals', 'activeDeals',
            'totalProducts', 'activeProducts',
            'totalStores', 'activeStores', 'verifiedStores',
            'totalCategories',
            'totalClicks', 'totalConversions', 'conversionRate',
            'totalRevenue', 'monthlyRevenue', 'revenueGrowth',
            'recentCoupons', 'recentDeals', 'recentProducts', 'recentStores', 'recentUsers',
            'topCoupons', 'topStores', 'topCategories',
            'monthlyStats', 'dailyStats', 'categoryStats', 'storePerformance',
            'systemHealth'
        ));
    }

    private function getMonthlyStats()
    {
        $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push([
                'month' => $date->format('M Y'),
                'users' => User::where('created_at', '>=', $date->startOfMonth())
                    ->where('created_at', '<', $date->endOfMonth())->count(),
                'coupons' => Coupon::where('created_at', '>=', $date->startOfMonth())
                    ->where('created_at', '<', $date->endOfMonth())->count(),
                'deals' => Deal::where('created_at', '>=', $date->startOfMonth())
                    ->where('created_at', '<', $date->endOfMonth())->count(),
                'revenue' => CouponConversion::where('created_at', '>=', $date->startOfMonth())
                    ->where('created_at', '<', $date->endOfMonth())->sum('revenue') +
                    StoreConversion::where('created_at', '>=', $date->startOfMonth())
                    ->where('created_at', '<', $date->endOfMonth())->sum('revenue')
            ]);
        }
        return $months;
    }

    private function getDailyStats()
    {
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push([
                'date' => $date->format('M d'),
                'users' => User::where('created_at', '>=', $date->startOfDay())
                    ->where('created_at', '<', $date->endOfDay())->count(),
                'clicks' => CouponClick::where('created_at', '>=', $date->startOfDay())
                    ->where('created_at', '<', $date->endOfDay())->count() +
                    StoreClick::where('created_at', '>=', $date->startOfDay())
                    ->where('created_at', '<', $date->endOfDay())->count(),
                'conversions' => CouponConversion::where('created_at', '>=', $date->startOfDay())
                    ->where('created_at', '<', $date->endOfDay())->count() +
                    StoreConversion::where('created_at', '>=', $date->startOfDay())
                    ->where('created_at', '<', $date->endOfDay())->count()
            ]);
        }
        return $days;
    }

    private function getCategoryStats()
    {
        return Category::withCount(['coupons', 'deals', 'products'])
            ->orderBy('coupons_count', 'desc')
            ->take(15)
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'coupons' => $category->coupons_count,
                    'deals' => $category->deals_count,
                    'products' => $category->products_count,
                    'total' => $category->coupons_count + $category->deals_count + $category->products_count
                ];
            });
    }

    private function getStorePerformance()
    {
        return Store::withCount(['coupons', 'deals', 'products'])
            ->orderBy('click_count', 'desc')
            ->take(15)
            ->get()
            ->map(function ($store) {
                return [
                    'name' => $store->name,
                    'coupons' => $store->coupons_count,
                    'deals' => $store->deals_count,
                    'products' => $store->products_count,
                    'clicks' => $store->click_count,
                    'conversions' => $store->conversion_count,
                    'revenue' => $store->revenue,
                    'rating' => $store->rating
                ];
            });
    }

    private function getSystemHealth()
    {
        $diskUsage = disk_free_space('/') / disk_total_space('/') * 100;
        $memoryUsage = memory_get_usage(true) / 1024 / 1024; // MB
        
        return [
            'disk_usage' => round(100 - $diskUsage, 2),
            'memory_usage' => round($memoryUsage, 2),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_connections' => DB::connection()->getPdo() ? 'Connected' : 'Disconnected',
            'queue_status' => 'Active', // You can implement actual queue status check
            'cache_status' => 'Active', // You can implement actual cache status check
            'storage_status' => 'Active' // You can implement actual storage status check
        ];
    }

    public function analytics()
    {
        // Detailed analytics page
        return view('admin.analytics');
    }

    public function reports()
    {
        // Reports generation page
        return view('admin.reports');
    }

    public function system()
    {
        // System monitoring page
        return view('admin.system');
    }
}