<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductPublicController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $query = Product::with(['store', 'category'])
            ->where('is_active', true);

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
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by featured
        if ($request->filled('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by brand
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filter by rating
        if ($request->filled('min_rating')) {
            $query->where('rating', '>=', $request->min_rating);
        }

        // Sort options
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'popularity':
                $query->orderBy('clicks_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'discount':
                $query->orderBy('discount_percentage', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate(20);

        // Get filter options
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Product::whereNotNull('brand')->distinct()->pluck('brand')->sort();

        return view('public.products.index', compact('products', 'stores', 'categories', 'brands'));
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        // Increment view count
        $product->increment('views_count');

        // Get related products
        $relatedProducts = Product::with(['store', 'category'])
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where(function ($query) use ($product) {
                $query->where('store_id', $product->store_id)
                      ->orWhere('category_id', $product->category_id)
                      ->orWhere('brand', $product->brand);
            })
            ->limit(6)
            ->get();

        // Store breadcrumbs
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Products', 'url' => route('products.index')],
            ['name' => $product->title, 'url' => null]
        ];

        return view('public.products.show', compact('product', 'relatedProducts', 'breadcrumbs'));
    }

    /**
     * Track product click and redirect
     */
    public function track(Request $request, Product $product)
    {
        // Increment click count
        $product->increment('clicks_count');

        // Track the click
        \App\Models\AffiliateClick::create([
            'affiliate_url' => $product->affiliate_url,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer')
        ]);

        return redirect($product->affiliate_url);
    }
}