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
        // Get featured content
        $featuredCoupons = Coupon::active()
            ->featured()
            ->with(['store', 'category'])
            ->limit(8)
            ->get();
            
        $featuredDeals = Deal::active()
            ->featured()
            ->with(['store', 'category'])
            ->limit(6)
            ->get();
            
        $featuredProducts = Product::active()
            ->featured()
            ->with(['store', 'category'])
            ->limit(8)
            ->get();
            
        $popularStores = Store::active()
            ->withCount(['coupons', 'deals', 'products'])
            ->orderByDesc('coupons_count')
            ->limit(12)
            ->get();
            
        $categories = Category::active()
            ->withCount(['coupons', 'deals', 'products'])
            ->orderByDesc('coupons_count')
            ->limit(8)
            ->get();
            
        // Get statistics
        $stats = [
            'total_coupons' => Coupon::active()->count(),
            'total_deals' => Deal::active()->count(),
            'total_products' => Product::active()->count(),
            'total_stores' => Store::active()->count(),
            'money_saved' => 50000, // This would be calculated from actual savings
        ];
        
        $homeSlider = Slider::where('slug', 'home-hero')->where('is_active', true)->first();

        return view('public.home', compact(
            'featuredCoupons',
            'featuredDeals', 
            'featuredProducts',
            'popularStores',
            'categories',
            'stats',
            'homeSlider'
        ));
    }
}