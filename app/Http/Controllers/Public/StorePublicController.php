<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Product;
use Illuminate\Http\Request;

class StorePublicController extends Controller
{
    /**
     * Display a listing of stores
     */
    public function index(Request $request)
    {
        $query = Store::where('is_active', true);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by featured
        if ($request->filled('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        // Sort options
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        switch ($sortBy) {
            case 'popularity':
                $query->withCount(['coupons', 'deals', 'products'])
                      ->orderByRaw('(coupons_count + deals_count + products_count) DESC');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $stores = $query->paginate(20);

        // Add counts for each store
        $stores->getCollection()->transform(function ($store) {
            $store->coupons_count = $store->coupons()->where('is_active', true)->count();
            $store->deals_count = $store->deals()->where('is_active', true)->count();
            $store->products_count = $store->products()->where('is_active', true)->count();
            return $store;
        });

        return view('public.stores.index', compact('stores'));
    }

    /**
     * Display the specified store
     */
    public function show(Store $store, Request $request)
    {
        $tab = $request->get('tab', 'coupons');

        // Get store coupons
        $coupons = $store->coupons()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12, ['*'], 'coupons_page');

        // Get store deals
        $deals = $store->deals()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12, ['*'], 'deals_page');

        // Get store products
        $products = $store->products()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12, ['*'], 'products_page');

        // Store statistics
        $stats = [
            'total_coupons' => $store->coupons()->where('is_active', true)->count(),
            'total_deals' => $store->deals()->where('is_active', true)->count(),
            'total_products' => $store->products()->where('is_active', true)->count(),
            'avg_discount' => $store->coupons()
                ->where('is_active', true)
                ->whereNotNull('discount_percentage')
                ->avg('discount_percentage'),
        ];

        // Store breadcrumbs
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Stores', 'url' => route('stores.index')],
            ['name' => $store->name, 'url' => null]
        ];

        return view('public.stores.show', compact(
            'store', 'coupons', 'deals', 'products', 'stats', 'breadcrumbs', 'tab'
        ));
    }
}