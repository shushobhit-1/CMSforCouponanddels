<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CouponVoteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API Routes
Route::prefix('v1')->group(function () {
    
    // Coupon Voting System
    Route::prefix('coupons')->group(function () {
        Route::post('{coupon}/vote', [CouponVoteController::class, 'vote']);
        Route::get('{coupon}/votes', [CouponVoteController::class, 'getVoteStats']);
        Route::get('votes/overall', [CouponVoteController::class, 'getOverallStats']);
    });
    
    // Coupon Clicks and Tracking
    Route::post('coupons/{coupon}/click', function (Request $request, $couponId) {
        // Track coupon click
        $coupon = \App\Models\Coupon::findOrFail($couponId);
        \App\Models\CouponClick::trackClick($coupon, $request, auth()->id());
        
        return response()->json([
            'success' => true,
            'message' => 'Click tracked successfully'
        ]);
    });
    
    // Deal Clicks and Tracking
    Route::post('deals/{deal}/click', function (Request $request, $dealId) {
        // Track deal click
        $deal = \App\Models\Deal::findOrFail($dealId);
        $deal->incrementClick();
        
        return response()->json([
            'success' => true,
            'message' => 'Click tracked successfully'
        ]);
    });
    
    // Product Clicks and Tracking
    Route::post('products/{product}/click', function (Request $request, $productId) {
        // Track product click
        $product = \App\Models\Product::findOrFail($productId);
        $product->incrementClick();
        
        return response()->json([
            'success' => true,
            'message' => 'Click tracked successfully'
        ]);
    });
    
    // Search API
    Route::get('search', function (Request $request) {
        $query = $request->get('q');
        $type = $request->get('type', 'all'); // all, coupons, deals, products, stores
        
        $results = [];
        
        if ($type === 'all' || $type === 'coupons') {
            $results['coupons'] = \App\Models\Coupon::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->active()
                ->with(['store', 'category'])
                ->take(5)
                ->get();
        }
        
        if ($type === 'all' || $type === 'deals') {
            $results['deals'] = \App\Models\Deal::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->active()
                ->with(['store', 'category'])
                ->take(5)
                ->get();
        }
        
        if ($type === 'all' || $type === 'products') {
            $results['products'] = \App\Models\Product::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->active()
                ->with(['store', 'category'])
                ->take(5)
                ->get();
        }
        
        if ($type === 'all' || $type === 'stores') {
            $results['stores'] = \App\Models\Store::where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->active()
                ->take(5)
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'query' => $query,
            'results' => $results
        ]);
    });
    
    // Categories API
    Route::get('categories', function () {
        $categories = \App\Models\Category::withCount(['coupons', 'deals', 'products'])
            ->active()
            ->get();
            
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    });
    
    // Stores API
    Route::get('stores', function (Request $request) {
        $stores = \App\Models\Store::withCount(['coupons', 'deals', 'products'])
            ->when($request->get('featured'), function($query) {
                return $query->featured();
            })
            ->when($request->get('verified'), function($query) {
                return $query->verified();
            })
            ->active()
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'stores' => $stores
        ]);
    });
    
    // Trending API
    Route::get('trending', function (Request $request) {
        $type = $request->get('type', 'coupons');
        $limit = $request->get('limit', 10);
        
        switch ($type) {
            case 'coupons':
                $items = \App\Models\Coupon::withCount(['clicks', 'conversions', 'upvotes', 'likes'])
                    ->active()
                    ->orderBy('clicks_count', 'desc')
                    ->take($limit)
                    ->get();
                break;
                
            case 'deals':
                $items = \App\Models\Deal::withCount(['clicks', 'conversions'])
                    ->active()
                    ->orderBy('clicks_count', 'desc')
                    ->take($limit)
                    ->get();
                break;
                
            case 'products':
                $items = \App\Models\Product::withCount(['clicks', 'conversions'])
                    ->active()
                    ->orderBy('clicks_count', 'desc')
                    ->take($limit)
                    ->get();
                break;
                
            default:
                $items = collect();
        }
        
        return response()->json([
            'success' => true,
            'type' => $type,
            'items' => $items
        ]);
    });
});

// Protected API Routes (require authentication)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    
    // User-specific voting routes
    Route::prefix('user')->group(function () {
        Route::get('votes/history', [CouponVoteController::class, 'getUserVoteHistory']);
        Route::delete('coupons/{coupon}/vote', [CouponVoteController::class, 'removeVote']);
    });
    
    // Favorites API
    Route::prefix('favorites')->group(function () {
        Route::get('/', function () {
            $user = auth()->user();
            $favorites = $user->favorites()->with(['favorable.store', 'favorable.category'])->get();
            
            return response()->json([
                'success' => true,
                'favorites' => $favorites
            ]);
        });
        
        Route::post('toggle', function (Request $request) {
            $user = auth()->user();
            $favorableType = $request->get('favorable_type');
            $favorableId = $request->get('favorable_id');
            
            $favorable = null;
            switch ($favorableType) {
                case 'coupon':
                    $favorable = \App\Models\Coupon::find($favorableId);
                    break;
                case 'deal':
                    $favorable = \App\Models\Deal::find($favorableId);
                    break;
                case 'product':
                    $favorable = \App\Models\Product::find($favorableId);
                    break;
                case 'store':
                    $favorable = \App\Models\Store::find($favorableId);
                    break;
            }
            
            if (!$favorable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }
            
            $result = $user->toggleFavorite($favorable);
            
            return response()->json([
                'success' => true,
                'action' => $result ? 'added' : 'removed',
                'message' => $result ? 'Added to favorites' : 'Removed from favorites'
            ]);
        });
    });
    
    // User Profile API
    Route::prefix('profile')->group(function () {
        Route::get('/', function () {
            $user = auth()->user();
            return response()->json([
                'success' => true,
                'user' => $user->load(['roles', 'permissions'])
            ]);
        });
        
        Route::put('/', function (Request $request) {
            $user = auth()->user();
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'timezone' => 'nullable|string|max:50',
                'preferences' => 'nullable|array'
            ]);
            
            $user->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user->fresh()
            ]);
        });
    });
    
    // Notifications API
    Route::prefix('notifications')->group(function () {
        Route::get('/', function () {
            $user = auth()->user();
            $notifications = $user->notifications()->paginate(20);
            
            return response()->json([
                'success' => true,
                'notifications' => $notifications
            ]);
        });
        
        Route::post('{id}/read', function ($id) {
            $user = auth()->user();
            $notification = $user->notifications()->findOrFail($id);
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        });
        
        Route::post('read-all', function () {
            $user = auth()->user();
            $user->unreadNotifications->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        });
    });
});

// Admin API Routes (require admin role)
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('v1/admin')->group(function () {
    
    // Admin voting analytics
    Route::prefix('votes')->group(function () {
        Route::get('analytics', [CouponVoteController::class, 'getAdminAnalytics']);
        Route::get('export', [CouponVoteController::class, 'exportVotingData']);
    });
    
    // Admin dashboard statistics
    Route::get('dashboard/stats', function () {
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_coupons' => \App\Models\Coupon::count(),
            'total_deals' => \App\Models\Deal::count(),
            'total_products' => \App\Models\Product::count(),
            'total_stores' => \App\Models\Store::count(),
            'total_clicks' => \App\Models\CouponClick::count() + \App\Models\Deal::sum('click_count') + \App\Models\Product::sum('click_count'),
            'total_conversions' => \App\Models\CouponConversion::count() + \App\Models\Deal::sum('conversion_count') + \App\Models\Product::sum('conversion_count'),
            'total_revenue' => \App\Models\CouponConversion::sum('commission_amount') + \App\Models\Deal::sum('revenue') + \App\Models\Product::sum('revenue'),
            'today_clicks' => \App\Models\CouponClick::today()->count(),
            'today_conversions' => \App\Models\CouponConversion::today()->count(),
            'this_week_clicks' => \App\Models\CouponClick::thisWeek()->count(),
            'this_month_clicks' => \App\Models\CouponClick::thisMonth()->count()
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    });
    
    // Admin user management
    Route::prefix('users')->group(function () {
        Route::get('/', function (Request $request) {
            $users = \App\Models\User::with(['roles', 'permissions'])
                ->when($request->get('search'), function($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->paginate(20);
                
            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        });
        
        Route::get('{user}', function ($userId) {
            $user = \App\Models\User::with(['roles', 'permissions', 'favorites', 'notifications'])->findOrFail($userId);
            
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        });
        
        Route::put('{user}/roles', function (Request $request, $userId) {
            $user = \App\Models\User::findOrFail($userId);
            $roleIds = $request->get('role_ids', []);
            
            $user->syncRoles($roleIds);
            
            return response()->json([
                'success' => true,
                'message' => 'User roles updated successfully'
            ]);
        });
    });
});

// Health Check API
Route::get('health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'environment' => config('app.env')
    ]);
});