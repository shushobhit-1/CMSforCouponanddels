<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Store;
use App\Models\CouponClick;
use App\Models\AffiliateClick;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    /**
     * Track coupon click
     */
    public function trackCouponClick(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id'
        ]);

        $coupon = Coupon::find($request->coupon_id);
        
        // Increment click count
        $coupon->increment('clicks_count');

        // Record click
        CouponClick::create([
            'coupon_id' => $request->coupon_id,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer')
        ]);

        return response()->json([
            'success' => true,
            'code' => $coupon->code,
            'affiliate_url' => $coupon->affiliate_url
        ]);
    }

    /**
     * Track affiliate click
     */
    public function trackAffiliateClick(Request $request)
    {
        $request->validate([
            'affiliate_url' => 'required|url'
        ]);

        // Record click
        AffiliateClick::create([
            'affiliate_url' => $request->affiliate_url,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer')
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => $request->affiliate_url
        ]);
    }

    /**
     * Toggle favorite
     */
    public function toggleFavorite(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add favorites'
            ], 401);
        }

        $request->validate([
            'type' => 'required|in:store,coupon,deal,product',
            'id' => 'required|integer'
        ]);

        $modelClass = 'App\\Models\\' . ucfirst($request->type);
        $user = Auth::user();

        // Check if item exists
        if (!$modelClass::find($request->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }

        $favorite = Favorite::where([
            'user_id' => $user->id,
            'favoriteable_type' => $modelClass,
            'favoriteable_id' => $request->id
        ])->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
            $message = 'Removed from favorites';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favoriteable_type' => $modelClass,
                'favoriteable_id' => $request->id
            ]);
            $isFavorited = true;
            $message = 'Added to favorites';
        }

        return response()->json([
            'success' => true,
            'favorited' => $isFavorited,
            'message' => $message
        ]);
    }

    /**
     * Search suggestions
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = [];

        // Search coupons
        $coupons = Coupon::where('title', 'like', "%{$query}%")
                         ->where('is_active', true)
                         ->limit(5)
                         ->get(['id', 'title', 'slug']);

        foreach ($coupons as $coupon) {
            $suggestions[] = [
                'type' => 'coupon',
                'title' => $coupon->title,
                'url' => route('coupons.show', $coupon->slug)
            ];
        }

        // Search deals
        $deals = Deal::where('title', 'like', "%{$query}%")
                    ->where('is_active', true)
                    ->limit(5)
                    ->get(['id', 'title', 'slug']);

        foreach ($deals as $deal) {
            $suggestions[] = [
                'type' => 'deal',
                'title' => $deal->title,
                'url' => route('deals.show', $deal->slug)
            ];
        }

        // Search products
        $products = Product::where('title', 'like', "%{$query}%")
                          ->where('is_active', true)
                          ->limit(5)
                          ->get(['id', 'title', 'slug']);

        foreach ($products as $product) {
            $suggestions[] = [
                'type' => 'product',
                'title' => $product->title,
                'url' => route('products.show', $product->slug)
            ];
        }

        // Search stores
        $stores = Store::where('name', 'like', "%{$query}%")
                      ->where('is_active', true)
                      ->limit(5)
                      ->get(['id', 'name', 'slug']);

        foreach ($stores as $store) {
            $suggestions[] = [
                'type' => 'store',
                'title' => $store->name,
                'url' => route('stores.show', $store->slug)
            ];
        }

        return response()->json($suggestions);
    }

    /**
     * Newsletter subscription
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Here you can integrate with your newsletter service
        // For now, we'll just return success
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to newsletter'
        ]);
    }

    /**
     * Get popular searches
     */
    public function popularSearches()
    {
        // This would typically be based on search analytics
        $popularSearches = [
            'Amazon',
            'Flipkart',
            'Fashion',
            'Electronics',
            'Mobile',
            'Laptops',
            'Home & Kitchen',
            'Books'
        ];

        return response()->json($popularSearches);
    }
}