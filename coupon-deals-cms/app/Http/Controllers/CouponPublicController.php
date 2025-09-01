<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class CouponPublicController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::active()->with(['store', 'category']);
        
        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // Filter by store
        if ($request->filled('store')) {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('slug', $request->store);
            });
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('store', function ($sq) use ($search) {
                      $sq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filter by featured
        if ($request->filter === 'featured') {
            $query->featured();
        }
        
        // Filter by expiring
        if ($request->filter === 'expiring') {
            $query->where('expires_at', '>', now())
                  ->where('expires_at', '<=', now()->addDays(3));
        }
        
        // Filter by new
        if ($request->filter === 'new') {
            $query->where('created_at', '>=', now()->subDays(7));
        }
        
        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderByDesc('clicks_count');
                break;
            case 'expiring':
                $query->orderBy('expires_at');
                break;
            case 'discount':
                $query->orderByDesc('discount_percentage');
                break;
            default:
                $query->latest();
        }
        
        $coupons = $query->paginate(12)->withQueryString();
        
        // Get filter data
        $stores = Store::active()->orderBy('name')->get(['id', 'name', 'slug']);
        $categories = Category::active()->orderBy('name')->get(['id', 'name', 'slug']);
        
        return view('public.coupons.index', compact('coupons', 'stores', 'categories'));
    }

    public function show(Coupon $coupon)
    {
        abort_unless($coupon->is_active, 404);
        
        // Increment view count
        $coupon->increment('views_count');
        
        // Get related coupons
        $relatedCoupons = Coupon::active()
            ->where('id', '!=', $coupon->id)
            ->where(function ($query) use ($coupon) {
                $query->where('store_id', $coupon->store_id)
                      ->orWhere('category_id', $coupon->category_id);
            })
            ->limit(6)
            ->get();
            
        return view('public.coupons.show', compact('coupon', 'relatedCoupons'));
    }
}

