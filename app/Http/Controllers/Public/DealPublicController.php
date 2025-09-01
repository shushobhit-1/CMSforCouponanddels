<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Store;
use App\Models\Category;
use Illuminate\Http\Request;

class DealPublicController extends Controller
{
    /**
     * Display a listing of deals
     */
    public function index(Request $request)
    {
        $query = Deal::with(['store', 'category'])
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
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by featured
        if ($request->filled('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
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
            case 'price_low':
                $query->orderBy('deal_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('deal_price', 'desc');
                break;
            default:
                $query->orderBy($sortBy, $sortOrder);
        }

        $deals = $query->paginate(20);

        // Get filter options
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('public.deals.index', compact('deals', 'stores', 'categories'));
    }

    /**
     * Display the specified deal
     */
    public function show(Deal $deal)
    {
        // Increment view count
        $deal->increment('views_count');

        // Get related deals
        $relatedDeals = Deal::with(['store', 'category'])
            ->where('id', '!=', $deal->id)
            ->where('is_active', true)
            ->where(function ($query) use ($deal) {
                $query->where('store_id', $deal->store_id)
                      ->orWhere('category_id', $deal->category_id);
            })
            ->limit(6)
            ->get();

        // Store breadcrumbs
        $breadcrumbs = [
            ['name' => 'Home', 'url' => route('home')],
            ['name' => 'Deals', 'url' => route('deals.index')],
            ['name' => $deal->title, 'url' => null]
        ];

        return view('public.deals.show', compact('deal', 'relatedDeals', 'breadcrumbs'));
    }

    /**
     * Track deal click and redirect
     */
    public function track(Request $request, Deal $deal)
    {
        // Increment click count
        $deal->increment('clicks_count');

        // Track the click
        \App\Models\AffiliateClick::create([
            'affiliate_url' => $deal->affiliate_url,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer')
        ]);

        return redirect($deal->affiliate_url);
    }
}