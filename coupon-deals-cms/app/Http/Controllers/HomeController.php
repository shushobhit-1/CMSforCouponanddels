<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get featured content with error handling
            $featuredCoupons = collect([]);
            $featuredDeals = collect([]);
            $featuredProducts = collect([]);
            $popularStores = collect([]);
            $categories = collect([]);
            $sliders = collect([]);
            
            // Try to get coupons
            try {
                $featuredCoupons = Coupon::active()
                    ->featured()
                    ->with(['store', 'category'])
                    ->limit(8)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching featured coupons: ' . $e->getMessage());
            }
            
            // Try to get deals
            try {
                $featuredDeals = Deal::active()
                    ->featured()
                    ->with(['store', 'category'])
                    ->limit(6)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching featured deals: ' . $e->getMessage());
            }
            
            // Try to get products
            try {
                $featuredProducts = Product::active()
                    ->featured()
                    ->with(['store', 'category'])
                    ->limit(8)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching featured products: ' . $e->getMessage());
            }
            
            // Try to get stores
            try {
                $popularStores = Store::active()
                    ->withCount(['coupons', 'deals', 'products'])
                    ->orderByDesc('coupons_count')
                    ->limit(12)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching popular stores: ' . $e->getMessage());
            }
            
            // Try to get categories
            try {
                $categories = Category::active()
                    ->withCount(['coupons', 'deals', 'products'])
                    ->orderByDesc('coupons_count')
                    ->limit(8)
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching categories: ' . $e->getMessage());
            }
            
            // Get statistics
            $stats = [
                'total_coupons' => 0,
                'total_deals' => 0,
                'total_products' => 0,
                'total_stores' => 0,
                'money_saved' => 50000,
            ];
            
            // Try to get stats
            try {
                $stats['total_coupons'] = Coupon::active()->count();
                $stats['total_deals'] = Deal::active()->count();
                $stats['total_products'] = Product::active()->count();
                $stats['total_stores'] = Store::active()->count();
            } catch (\Exception $e) {
                \Log::error('Error fetching stats: ' . $e->getMessage());
            }
            
            // Try to get sliders
            try {
                $sliders = Slider::where('is_active', true)->get();
            } catch (\Exception $e) {
                \Log::error('Error fetching sliders: ' . $e->getMessage());
            }

            return view('public.home', compact(
                'featuredCoupons',
                'featuredDeals', 
                'featuredProducts',
                'popularStores',
                'categories',
                'stats',
                'sliders'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in HomeController index: ' . $e->getMessage());
            return response('Application Error: ' . $e->getMessage(), 500);
        }
    }
}