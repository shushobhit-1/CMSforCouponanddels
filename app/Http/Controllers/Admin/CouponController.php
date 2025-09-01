<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Store;
use App\Models\Category;
use App\Models\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of coupons
     */
    public function index(Request $request)
    {
        $query = Coupon::with(['store', 'category', 'creator']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $coupons = $query->paginate(20);
        $stores = Store::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.coupons.index', compact('coupons', 'stores', 'categories'));
    }

    /**
     * Show the form for creating a new coupon
     */
    public function create()
    {
        $stores = Store::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();
        $affiliates = Affiliate::active()->orderBy('name')->get();

        return view('admin.coupons.create', compact('stores', 'categories', 'affiliates'));
    }

    /**
     * Store a newly created coupon
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'nullable|exists:categories,id',
            'code' => 'nullable|string|max:100',
            'type' => 'required|in:percentage,fixed,free_shipping,free_delivery',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'affiliate_link' => 'required|url',
            'tracking_id' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'coupon_popup_settings' => 'nullable|array',
            'button_text' => 'nullable|string|max:100',
            'button_color' => 'nullable|string|max:7',
            'button_hover_effect' => 'nullable|string|max:50',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $coupon = Coupon::create([
            'title' => $request->title,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'store_id' => $request->store_id,
            'category_id' => $request->category_id,
            'code' => $request->code,
            'type' => $request->type,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'currency' => $request->currency,
            'minimum_purchase' => $request->minimum_purchase,
            'maximum_discount' => $request->maximum_discount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'affiliate_link' => $request->affiliate_link,
            'tracking_id' => $request->tracking_id,
            'is_featured' => $request->boolean('is_featured'),
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => $request->boolean('is_active'),
            'is_verified' => $request->boolean('is_verified'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'coupon_popup_settings' => $request->coupon_popup_settings,
            'button_text' => $request->button_text,
            'button_color' => $request->button_color,
            'button_hover_effect' => $request->button_hover_effect,
            'created_by' => auth()->id(),
            'status' => 'active'
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $coupon->addMedia($image)
                    ->toMediaCollection('coupon_images');
            }
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    /**
     * Display the specified coupon
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['store', 'category', 'creator', 'favorites', 'clicks', 'conversions']);
        
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Show the form for editing the specified coupon
     */
    public function edit(Coupon $coupon)
    {
        $stores = Store::active()->orderBy('name')->get();
        $categories = Category::active()->orderBy('name')->get();
        $affiliates = Affiliate::active()->orderBy('name')->get();

        return view('admin.coupons.edit', compact('coupon', 'stores', 'categories', 'affiliates'));
    }

    /**
     * Update the specified coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'nullable|exists:categories,id',
            'code' => 'nullable|string|max:100',
            'type' => 'required|in:percentage,fixed,free_shipping,free_delivery',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'affiliate_link' => 'required|url',
            'tracking_id' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'coupon_popup_settings' => 'nullable|array',
            'button_text' => 'nullable|string|max:100',
            'button_color' => 'nullable|string|max:7',
            'button_hover_effect' => 'nullable|string|max:50',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $coupon->update([
            'title' => $request->title,
            'description' => $request->description,
            'short_description' => $request->short_description,
            'store_id' => $request->store_id,
            'category_id' => $request->category_id,
            'code' => $request->code,
            'type' => $request->type,
            'discount_percentage' => $request->discount_percentage,
            'discount_amount' => $request->discount_amount,
            'currency' => $request->currency,
            'minimum_purchase' => $request->minimum_purchase,
            'maximum_discount' => $request->maximum_discount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'affiliate_link' => $request->affiliate_link,
            'tracking_id' => $request->tracking_id,
            'is_featured' => $request->boolean('is_featured'),
            'is_popular' => $request->boolean('is_popular'),
            'is_active' => $request->boolean('is_active'),
            'is_verified' => $request->boolean('is_verified'),
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'coupon_popup_settings' => $request->coupon_popup_settings,
            'button_text' => $request->button_text,
            'button_color' => $request->button_color,
            'button_hover_effect' => $request->button_hover_effect
        ]);

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $coupon->addMedia($image)
                    ->toMediaCollection('coupon_images');
            }
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    /**
     * Remove the specified coupon
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully!');
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update([
            'is_active' => !$coupon->is_active
        ]);

        $status = $coupon->is_active ? 'activated' : 'deactivated';
        return response()->json([
            'success' => true,
            'message' => "Coupon {$status} successfully!",
            'is_active' => $coupon->is_active
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Coupon $coupon)
    {
        $coupon->update([
            'is_featured' => !$coupon->is_featured
        ]);

        $status = $coupon->is_featured ? 'featured' : 'unfeatured';
        return response()->json([
            'success' => true,
            'message' => "Coupon {$status} successfully!",
            'is_featured' => $coupon->is_featured
        ]);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'coupon_ids' => 'required|array|min:1',
            'coupon_ids.*' => 'exists:coupons,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data'
            ], 400);
        }

        $couponIds = $request->coupon_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                Coupon::whereIn('id', $couponIds)->update(['is_active' => true]);
                $message = 'Coupons activated successfully!';
                break;
            case 'deactivate':
                Coupon::whereIn('id', $couponIds)->update(['is_active' => false]);
                $message = 'Coupons deactivated successfully!';
                break;
            case 'feature':
                Coupon::whereIn('id', $couponIds)->update(['is_featured' => true]);
                $message = 'Coupons featured successfully!';
                break;
            case 'unfeature':
                Coupon::whereIn('id', $couponIds)->update(['is_featured' => false]);
                $message = 'Coupons unfeatured successfully!';
                break;
            case 'delete':
                Coupon::whereIn('id', $couponIds)->delete();
                $message = 'Coupons deleted successfully!';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Delete coupon image
     */
    public function deleteImage(Coupon $coupon, Media $media)
    {
        if ($media->model_id === $coupon->id) {
            $media->delete();
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized action'
        ], 403);
    }

    /**
     * Get coupon statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::active()->count(),
            'featured' => Coupon::featured()->count(),
            'expiring_soon' => Coupon::expiring(7)->count(),
            'new_this_month' => Coupon::where('created_at', '>=', now()->startOfMonth())->count(),
            'total_clicks' => Coupon::sum('click_count'),
            'total_conversions' => Coupon::sum('conversion_count'),
            'total_revenue' => Coupon::sum('revenue')
        ];

        return response()->json($stats);
    }

    /**
     * Export coupons
     */
    public function export(Request $request)
    {
        $query = Coupon::with(['store', 'category']);

        // Apply filters
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $coupons = $query->get();

        // Generate CSV
        $filename = 'coupons_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($coupons) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'ID', 'Title', 'Code', 'Type', 'Discount', 'Store', 'Category',
                'Start Date', 'End Date', 'Status', 'Featured', 'Clicks', 'Conversions'
            ]);

            // Data
            foreach ($coupons as $coupon) {
                fputcsv($file, [
                    $coupon->id,
                    $coupon->title,
                    $coupon->code,
                    $coupon->type,
                    $coupon->discount_text,
                    $coupon->store_name,
                    $coupon->category_name,
                    $coupon->start_date->format('Y-m-d'),
                    $coupon->end_date->format('Y-m-d'),
                    $coupon->status,
                    $coupon->is_featured ? 'Yes' : 'No',
                    $coupon->click_count,
                    $coupon->conversion_count
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}