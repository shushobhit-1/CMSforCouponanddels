<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Favorite;
use App\Models\CouponClick;
use App\Models\AffiliateClick;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function trackCouponClick(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string',
            'user_agent' => 'nullable|string',
            'referrer' => 'nullable|string',
        ]);
        
        // Find coupon
        $coupon = Coupon::where('code', $request->coupon_code)->first();
        
        if ($coupon) {
            // Track the click
            CouponClick::create([
                'coupon_id' => $coupon->id,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->user_agent,
                'referrer' => $request->referrer,
                'clicked_at' => now(),
            ]);
            
            // Increment coupon clicks count
            $coupon->increment('clicks_count');
        }
        
        return response()->json(['success' => true]);
    }
    
    public function trackAffiliateClick(Request $request): JsonResponse
    {
        $request->validate([
            'affiliate_url' => 'required|url',
            'user_agent' => 'nullable|string',
            'referrer' => 'nullable|string',
        ]);
        
        // Track affiliate click
        AffiliateClick::create([
            'user_id' => auth()->id(),
            'affiliate_url' => $request->affiliate_url,
            'ip_address' => $request->ip(),
            'user_agent' => $request->user_agent,
            'referrer' => $request->referrer,
            'clicked_at' => now(),
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function toggleFavorite(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:coupon,deal,product,store',
            'id' => 'required|integer',
        ]);
        
        $user = auth()->user();
        $type = $request->type;
        $id = $request->id;
        
        // Map type to model
        $modelMap = [
            'coupon' => Coupon::class,
            'deal' => Deal::class,
            'product' => Product::class,
            'store' => Store::class,
        ];
        
        $model = $modelMap[$type];
        $item = $model::findOrFail($id);
        
        // Check if already favorited
        $existing = Favorite::where('user_id', $user->id)
            ->where('favoritable_type', $model)
            ->where('favoritable_id', $id)
            ->first();
            
        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'favoritable_type' => $model,
                'favoritable_id' => $id,
            ]);
            $favorited = true;
        }
        
        return response()->json([
            'success' => true,
            'favorited' => $favorited,
        ]);
    }
    
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        
        $suggestions = collect();
        
        // Search coupons
        $coupons = Coupon::active()
            ->where('title', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'title', 'slug'])
            ->map(function ($coupon) {
                return [
                    'type' => 'coupon',
                    'id' => $coupon->id,
                    'title' => $coupon->title,
                    'url' => route('coupons.show', $coupon->slug),
                ];
            });
            
        // Search stores
        $stores = Store::active()
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'name', 'slug'])
            ->map(function ($store) {
                return [
                    'type' => 'store',
                    'id' => $store->id,
                    'title' => $store->name,
                    'url' => route('stores.show', $store->slug),
                ];
            });
            
        $suggestions = $suggestions->merge($coupons)->merge($stores);
        
        return response()->json($suggestions->take(10)->values());
    }
    
    public function newsletter(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);
        
        // Save to newsletter subscribers (you'd need to create this table)
        // NewsletterSubscriber::create(['email' => $request->email]);
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to newsletter!',
        ]);
    }
}