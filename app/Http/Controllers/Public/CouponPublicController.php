<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class CouponPublicController extends Controller
{
    /**
     * Display a listing of coupons
     */
    public function index(Request $request)
    {
        $query = Coupon::with(['store', 'category'])
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });

        // Filter by store
        if ($request->filled('store')) {
            $query->whereHas('store', function ($q) use ($request) {
                $q->where('slug', $request->store);
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort options
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'popularity':
                $query->orderBy('clicks_count', 'desc');
                break;
            case 'expiry':
                $query->orderByRaw('expires_at IS NULL, expires_at ASC');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'discount':
                $query->orderBy('discount_percentage', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $coupons = $query->paginate(20);

        // Get filter options
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('public.coupons.index', compact('coupons', 'stores', 'categories'));
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon)
    {
        // Increment view count
        $coupon->increment('views_count');

        // Get related coupons
        $relatedCoupons = Coupon::with(['store', 'category'])
            ->where('id', '!=', $coupon->id)
            ->where('is_active', true)
            ->where(function ($query) use ($coupon) {
                $query->where('store_id', $coupon->store_id)
                      ->orWhere('category_id', $coupon->category_id);
            })
            ->limit(6)
            ->get();

        // Store breadcrumbs
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Coupons', 'url' => route('coupons.index')],
            ['name' => $coupon->title, 'url' => null]
        ];

        return view('public.coupons.show', compact('coupon', 'relatedCoupons', 'breadcrumbs'));
    }

    /**
     * Reveal coupon code (AJAX)
     */
    public function reveal(Request $request, Coupon $coupon)
    {
        // Increment click count
        $coupon->increment('clicks_count');

        // Track the click
        \App\Models\CouponClick::create([
            'coupon_id' => $coupon->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer')
        ]);

        return response()->json([
            'success' => true,
            'code' => $coupon->code,
            'affiliate_url' => $coupon->affiliate_url,
            'redirect_url' => $coupon->store->website_url ?? $coupon->affiliate_url
        ]);
    }
}