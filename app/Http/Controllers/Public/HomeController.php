<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Deal;
use App\Models\Product;
use App\Models\Store;
use App\Models\Slider;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache homepage data for better performance
        $homepageData = Cache::remember('homepage_data', 3600, function () {
            return [
                'featured_coupons' => Coupon::active()->featured()->with(['store', 'category'])->limit(8)->get(),
                'featured_deals' => Deal::active()->featured()->with(['store', 'category'])->limit(8)->get(),
                'featured_products' => Product::active()->featured()->with(['store', 'category'])->limit(8)->get(),
                'featured_stores' => Store::active()->featured()->with(['categories'])->limit(6)->get(),
                'popular_categories' => Category::active()->featured()->withCount(['coupons', 'deals', 'products'])->limit(8)->get(),
                'sliders' => Slider::active()->ordered()->get(),
                'recent_coupons' => Coupon::active()->with(['store', 'category'])->latest()->limit(12)->get(),
                'expiring_coupons' => Coupon::active()->expiring(7)->with(['store', 'category'])->limit(8)->get(),
            ];
        });

        // Get site settings
        $settings = Setting::getSettings();

        return view('public.home', compact('homepageData', 'settings'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all');
        $category = $request->get('category');
        $store = $request->get('store');
        $sort = $request->get('sort', 'relevance');

        $results = [
            'coupons' => collect(),
            'deals' => collect(),
            'products' => collect(),
            'stores' => collect(),
            'categories' => collect()
        ];

        if ($query) {
            // Search coupons
            if ($type === 'all' || $type === 'coupons') {
                $couponQuery = Coupon::active()->search($query);
                if ($category) $couponQuery->byCategory($category);
                if ($store) $couponQuery->byStore($store);
                $results['coupons'] = $this->applySorting($couponQuery, $sort)->with(['store', 'category'])->paginate(20);
            }

            // Search deals
            if ($type === 'all' || $type === 'deals') {
                $dealQuery = Deal::active()->search($query);
                if ($category) $dealQuery->byCategory($category);
                if ($store) $dealQuery->byStore($store);
                $results['deals'] = $this->applySorting($dealQuery, $sort)->with(['store', 'category'])->paginate(20);
            }

            // Search products
            if ($type === 'all' || $type === 'products') {
                $productQuery = Product::active()->search($query);
                if ($category) $productQuery->byCategory($category);
                if ($store) $productQuery->byStore($store);
                $results['products'] = $this->applySorting($productQuery, $sort)->with(['store', 'category'])->paginate(20);
            }

            // Search stores
            if ($type === 'all' || $type === 'stores') {
                $storeQuery = Store::active()->search($query);
                if ($category) $storeQuery->whereHas('categories', function($q) use ($category) {
                    $q->where('id', $category);
                });
                $results['stores'] = $this->applySorting($storeQuery, $sort)->with(['categories'])->paginate(20);
            }

            // Search categories
            if ($type === 'all' || $type === 'categories') {
                $categoryQuery = Category::active()->search($query);
                $results['categories'] = $categoryQuery->withCount(['coupons', 'deals', 'products'])->paginate(20);
            }
        }

        // Get filters
        $filters = [
            'categories' => Category::active()->withCount(['coupons', 'deals', 'products'])->get(),
            'stores' => Store::active()->withCount(['coupons', 'deals', 'products'])->get(),
        ];

        return view('public.search', compact('results', 'filters', 'query', 'type', 'category', 'store', 'sort'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->active()->firstOrFail();
        
        // Get subcategories
        $subcategories = $category->children()->active()->withCount(['coupons', 'deals', 'products'])->get();
        
        // Get content for this category
        $content = [
            'coupons' => $category->coupons()->active()->with(['store'])->latest()->paginate(20),
            'deals' => $category->deals()->active()->with(['store'])->latest()->paginate(20),
            'products' => $category->products()->active()->with(['store'])->latest()->paginate(20),
        ];

        // Get related categories
        $relatedCategories = Category::active()
            ->where('id', '!=', $category->id)
            ->where(function($q) use ($category) {
                $q->where('parent_id', $category->parent_id)
                  ->orWhere('parent_id', $category->id);
            })
            ->withCount(['coupons', 'deals', 'products'])
            ->limit(6)
            ->get();

        return view('public.category', compact('category', 'subcategories', 'content', 'relatedCategories'));
    }

    public function store($slug)
    {
        $store = Store::where('slug', $slug)->active()->firstOrFail();
        
        // Get store content
        $content = [
            'coupons' => $store->coupons()->active()->with(['category'])->latest()->paginate(20),
            'deals' => $store->deals()->active()->with(['category'])->latest()->paginate(20),
            'products' => $store->products()->active()->with(['category'])->latest()->paginate(20),
        ];

        // Get store statistics
        $stats = [
            'total_coupons' => $store->coupons()->count(),
            'active_coupons' => $store->coupons()->active()->count(),
            'total_deals' => $store->deals()->count(),
            'active_deals' => $store->deals()->active()->count(),
            'total_products' => $store->products()->count(),
            'active_products' => $store->products()->active()->count(),
            'total_clicks' => $store->clicks()->count(),
            'total_conversions' => $store->conversions()->count(),
        ];

        // Get similar stores
        $similarStores = Store::active()
            ->where('id', '!=', $store->id)
            ->whereHas('categories', function($q) use ($store) {
                $q->whereIn('category_id', $store->categories->pluck('id'));
            })
            ->with(['categories'])
            ->limit(6)
            ->get();

        return view('public.store', compact('store', 'content', 'stats', 'similarStores'));
    }

    public function coupons(Request $request)
    {
        $query = Coupon::active()->with(['store', 'category']);

        // Apply filters
        if ($request->category) {
            $query->byCategory($request->category);
        }
        if ($request->store) {
            $query->byStore($request->store);
        }
        if ($request->featured) {
            $query->featured();
        }
        if ($request->expiring) {
            $query->expiring(7);
        }

        // Apply sorting
        $coupons = $this->applySorting($query, $request->get('sort', 'newest'))->paginate(24);

        // Get filters
        $filters = [
            'categories' => Category::active()->withCount('coupons')->get(),
            'stores' => Store::active()->withCount('coupons')->get(),
        ];

        return view('public.coupons', compact('coupons', 'filters'));
    }

    public function deals(Request $request)
    {
        $query = Deal::active()->with(['store', 'category']);

        // Apply filters
        if ($request->category) {
            $query->byCategory($request->category);
        }
        if ($request->store) {
            $query->byStore($request->store);
        }
        if ($request->featured) {
            $query->featured();
        }
        if ($request->expiring) {
            $query->expiring(7);
        }

        // Apply sorting
        $deals = $this->applySorting($query, $request->get('sort', 'newest'))->paginate(24);

        // Get filters
        $filters = [
            'categories' => Category::active()->withCount('deals')->get(),
            'stores' => Store::active()->withCount('deals')->get(),
        ];

        return view('public.deals', compact('deals', 'filters'));
    }

    public function products(Request $request)
    {
        $query = Product::active()->with(['store', 'category']);

        // Apply filters
        if ($request->category) {
            $query->byCategory($request->category);
        }
        if ($request->store) {
            $query->byStore($request->store);
        }
        if ($request->featured) {
            $query->featured();
        }
        if ($request->brand) {
            $query->byBrand($request->brand);
        }
        if ($request->price_min || $request->price_max) {
            $query->byPriceRange($request->price_min ?: 0, $request->price_max ?: 999999);
        }

        // Apply sorting
        $products = $this->applySorting($query, $request->get('sort', 'newest'))->paginate(24);

        // Get filters
        $filters = [
            'categories' => Category::active()->withCount('products')->get(),
            'stores' => Store::active()->withCount('products')->get(),
            'brands' => Product::active()->distinct()->pluck('brand')->filter()->values(),
        ];

        return view('public.products', compact('products', 'filters'));
    }

    public function stores(Request $request)
    {
        $query = Store::active()->with(['categories']);

        // Apply filters
        if ($request->category) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('id', $request->category);
            });
        }
        if ($request->featured) {
            $query->featured();
        }
        if ($request->verified) {
            $query->verified();
        }

        // Apply sorting
        $stores = $this->applySorting($query, $request->get('sort', 'popular'))->paginate(24);

        // Get filters
        $filters = [
            'categories' => Category::active()->withCount('stores')->get(),
        ];

        return view('public.stores', compact('stores', 'filters'));
    }

    public function about()
    {
        $settings = Setting::getSettings();
        return view('public.about', compact('settings'));
    }

    public function contact()
    {
        $settings = Setting::getSettings();
        return view('public.contact', compact('settings'));
    }

    public function privacy()
    {
        $settings = Setting::getSettings();
        return view('public.privacy', compact('settings'));
    }

    public function terms()
    {
        $settings = Setting::getSettings();
        return view('public.terms', compact('settings'));
    }

    public function sitemap()
    {
        $content = [
            'categories' => Category::active()->get(),
            'stores' => Store::active()->get(),
            'coupons' => Coupon::active()->get(),
            'deals' => Deal::active()->get(),
            'products' => Product::active()->get(),
        ];

        return response()->view('public.sitemap', compact('content'))
                        ->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        $content = view('public.robots')->render();
        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    // Helper methods
    private function applySorting($query, $sort)
    {
        return match($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'popular' => $query->orderBy('clicks_count', 'desc'),
            'rating' => $query->orderBy('rating', 'desc'),
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'discount' => $query->orderBy('discount_value', 'desc'),
            'expiring' => $query->orderBy('end_date', 'asc'),
            default => $query->latest()
        };
    }
}