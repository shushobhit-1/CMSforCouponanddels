<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Store;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DealController extends Controller
{
    public function index(Request $request)
    {
        $query = Deal::with(['store', 'category', 'creator'])
                    ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('featured', $request->featured);
        }

        $deals = $query->paginate(20);
        $stores = Store::active()->get();
        $categories = Category::active()->get();

        return view('admin.deals.index', compact('deals', 'stores', 'categories'));
    }

    public function create()
    {
        $stores = Store::active()->get();
        $categories = Category::active()->get();
        $users = User::active()->get();

        return view('admin.deals.create', compact('stores', 'categories', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'required|exists:categories,id',
            'deal_type' => 'required|in:percentage,fixed,free_shipping,special',
            'original_price' => 'required|numeric|min:0',
            'deal_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,expired,featured',
            'featured' => 'boolean',
            'popular' => 'boolean',
            'affiliate_link' => 'required|url',
            'affiliate_network' => 'required|string|max:100',
            'commission_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'terms_conditions' => 'nullable|string',
            'restrictions' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'unlimited_stock' => 'boolean',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'popup_enabled' => 'boolean',
            'popup_delay' => 'nullable|integer|min:0',
            'popup_animation' => 'nullable|string|max:100',
            'popup_position' => 'nullable|string|max:100',
            'popup_style' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['published_at'] = $validated['published_at'] ?? now();

        $deal = Deal::create($validated);

        // Handle media uploads
        if ($request->hasFile('image')) {
            $deal->addMediaFromRequest('image')
                 ->toMediaCollection('deals', 'public');
        }

        if ($request->hasFile('banner')) {
            $deal->addMediaFromRequest('banner')
                 ->toMediaCollection('banners', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $deal->addMedia($image)
                     ->toMediaCollection('gallery', 'public');
            }
        }

        return redirect()->route('admin.deals.index')
                        ->with('success', 'Deal created successfully!');
    }

    public function show(Deal $deal)
    {
        $deal->load(['store', 'category', 'creator', 'updater', 'favorites', 'clicks', 'conversions', 'reviews']);
        
        return view('admin.deals.show', compact('deal'));
    }

    public function edit(Deal $deal)
    {
        $stores = Store::active()->get();
        $categories = Category::active()->get();
        $users = User::active()->get();

        return view('admin.deals.edit', compact('deal', 'stores', 'categories', 'users'));
    }

    public function update(Request $request, Deal $deal)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'required|exists:categories,id',
            'deal_type' => 'required|in:percentage,fixed,free_shipping,special',
            'original_price' => 'required|numeric|min:0',
            'deal_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive,expired,featured',
            'featured' => 'boolean',
            'popular' => 'boolean',
            'affiliate_link' => 'required|url',
            'affiliate_network' => 'required|string|max:100',
            'commission_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'terms_conditions' => 'nullable|string',
            'restrictions' => 'nullable|string',
            'stock_quantity' => 'nullable|integer|min:0',
            'unlimited_stock' => 'boolean',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'popup_enabled' => 'boolean',
            'popup_delay' => 'nullable|integer|min:0',
            'popup_animation' => 'nullable|string|max:100',
            'popup_position' => 'nullable|string|max:100',
            'popup_style' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
        ]);

        $validated['updated_by'] = Auth::id();

        $deal->update($validated);

        // Handle media uploads
        if ($request->hasFile('image')) {
            $deal->clearMediaCollection('deals');
            $deal->addMediaFromRequest('image')
                 ->toMediaCollection('deals', 'public');
        }

        if ($request->hasFile('banner')) {
            $deal->clearMediaCollection('banners');
            $deal->addMediaFromRequest('banner')
                 ->toMediaCollection('banners', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            $deal->clearMediaCollection('gallery');
            foreach ($request->file('gallery') as $image) {
                $deal->addMedia($image)
                     ->toMediaCollection('gallery', 'public');
            }
        }

        return redirect()->route('admin.deals.index')
                        ->with('success', 'Deal updated successfully!');
    }

    public function destroy(Deal $deal)
    {
        $deal->delete();

        return redirect()->route('admin.deals.index')
                        ->with('success', 'Deal deleted successfully!');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $dealIds = $request->input('deal_ids', []);

        if (empty($dealIds)) {
            return back()->with('error', 'Please select deals to perform action.');
        }

        switch ($action) {
            case 'activate':
                Deal::whereIn('id', $dealIds)->update(['status' => 'active']);
                $message = 'Selected deals activated successfully!';
                break;

            case 'deactivate':
                Deal::whereIn('id', $dealIds)->update(['status' => 'inactive']);
                $message = 'Selected deals deactivated successfully!';
                break;

            case 'feature':
                Deal::whereIn('id', $dealIds)->update(['featured' => true]);
                $message = 'Selected deals featured successfully!';
                break;

            case 'unfeature':
                Deal::whereIn('id', $dealIds)->update(['featured' => false]);
                $message = 'Selected deals unfeatured successfully!';
                break;

            case 'delete':
                Deal::whereIn('id', $dealIds)->delete();
                $message = 'Selected deals deleted successfully!';
                break;

            default:
                return back()->with('error', 'Invalid action selected.');
        }

        return back()->with('success', $message);
    }

    public function toggleStatus(Deal $deal)
    {
        $deal->update([
            'status' => $deal->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'status' => $deal->status,
            'message' => "Deal {$deal->status} successfully!"
        ]);
    }

    public function toggleFeatured(Deal $deal)
    {
        $deal->update(['featured' => !$deal->featured]);

        return response()->json([
            'success' => true,
            'featured' => $deal->featured,
            'message' => $deal->featured ? 'Deal featured successfully!' : 'Deal unfeatured successfully!'
        ]);
    }

    public function duplicate(Deal $deal)
    {
        $newDeal = $deal->replicate();
        $newDeal->title = $deal->title . ' (Copy)';
        $newDeal->status = 'inactive';
        $newDeal->featured = false;
        $newDeal->popular = false;
        $newDeal->created_by = Auth::id();
        $newDeal->save();

        // Copy media
        foreach ($deal->getMedia() as $media) {
            $newDeal->addMedia($media->getPath())
                    ->toMediaCollection($media->collection_name, 'public');
        }

        return redirect()->route('admin.deals.edit', $newDeal)
                        ->with('success', 'Deal duplicated successfully! You can now edit the copy.');
    }

    public function analytics(Deal $deal)
    {
        $deal->load(['clicks', 'conversions']);

        $clickData = $deal->clicks()
                         ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();

        $conversionData = $deal->conversions()
                              ->selectRaw('DATE(created_at) as date, COUNT(*) as conversions')
                              ->groupBy('date')
                              ->orderBy('date')
                              ->get();

        $revenueData = $deal->conversions()
                           ->selectRaw('DATE(created_at) as date, SUM(commission_earned) as revenue')
                           ->groupBy('date')
                           ->orderBy('date')
                           ->get();

        return view('admin.deals.analytics', compact('deal', 'clickData', 'conversionData', 'revenueData'));
    }

    public function export(Request $request)
    {
        $query = Deal::with(['store', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $deals = $query->get();

        $filename = 'deals_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($deals) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Title', 'Store', 'Category', 'Deal Type', 'Original Price',
                'Deal Price', 'Discount', 'Status', 'Featured', 'Start Date',
                'End Date', 'Clicks', 'Conversions', 'Revenue', 'Created At'
            ]);

            foreach ($deals as $deal) {
                fputcsv($file, [
                    $deal->id,
                    $deal->title,
                    $deal->store->name ?? '',
                    $deal->category->name ?? '',
                    $deal->deal_type,
                    $deal->original_price,
                    $deal->deal_price,
                    $deal->discount_text,
                    $deal->status,
                    $deal->featured ? 'Yes' : 'No',
                    $deal->start_date->format('Y-m-d'),
                    $deal->end_date->format('Y-m-d'),
                    $deal->click_count,
                    $deal->conversion_count,
                    $deal->conversions->sum('commission_earned'),
                    $deal->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}