<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store', 'category', 'creator'])
                       ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
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

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('current_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('current_price', '<=', $request->max_price);
        }

        $products = $query->paginate(20);
        $stores = Store::active()->get();
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'stores', 'categories'));
    }

    public function create()
    {
        $stores = Store::active()->get();
        $categories = Category::active()->get();
        $users = User::active()->get();

        return view('admin.products.create', compact('stores', 'categories', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'original_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'availability' => 'required|in:in_stock,out_of_stock,pre_order,discontinued',
            'stock_quantity' => 'nullable|integer|min:0',
            'unlimited_stock' => 'boolean',
            'min_order_quantity' => 'nullable|integer|min:1',
            'max_order_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:0',
            'dimensions.width' => 'nullable|numeric|min:0',
            'dimensions.height' => 'nullable|numeric|min:0',
            'color' => 'nullable|array',
            'size' => 'nullable|array',
            'material' => 'nullable|string|max:100',
            'warranty' => 'nullable|string|max:200',
            'shipping_info' => 'nullable|string',
            'return_policy' => 'nullable|string',
            'affiliate_link' => 'required|url',
            'affiliate_network' => 'required|string|max:100',
            'commission_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'tracking_id' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,featured,popular',
            'featured' => 'boolean',
            'popular' => 'boolean',
            'trending' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'popup_enabled' => 'boolean',
            'popup_delay' => 'nullable|integer|min:0',
            'popup_animation' => 'nullable|string|max:100',
            'popup_position' => 'nullable|string|max:100',
            'popup_style' => 'nullable|string|max:100',
            'button_text' => 'nullable|string|max:100',
            'button_color' => 'nullable|string|max:7',
            'button_hover_effect' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'featured_until' => 'nullable|date|after:now',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['published_at'] = $validated['published_at'] ?? now();

        $product = Product::create($validated);

        // Handle media uploads
        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')
                    ->toMediaCollection('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $product->addMedia($image)
                        ->toMediaCollection('gallery', 'public');
            }
        }

        return redirect()->route('admin.products.index')
                        ->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        $product->load(['store', 'category', 'creator', 'updater', 'favorites', 'clicks', 'conversions', 'reviews', 'relatedProducts']);
        
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $stores = Store::active()->get();
        $categories = Category::active()->get();
        $users = User::active()->get();

        return view('admin.products.edit', compact('product', 'stores', 'categories', 'users'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'store_id' => 'required|exists:stores,id',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'original_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'availability' => 'required|in:in_stock,out_of_stock,pre_order,discontinued',
            'stock_quantity' => 'nullable|integer|min:0',
            'unlimited_stock' => 'boolean',
            'min_order_quantity' => 'nullable|integer|min:1',
            'max_order_quantity' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:0',
            'dimensions.width' => 'nullable|numeric|min:0',
            'dimensions.height' => 'nullable|numeric|min:0',
            'color' => 'nullable|array',
            'size' => 'nullable|array',
            'material' => 'nullable|string|max:100',
            'warranty' => 'nullable|string|max:200',
            'shipping_info' => 'nullable|string',
            'return_policy' => 'nullable|string',
            'affiliate_link' => 'required|url',
            'affiliate_network' => 'required|string|max:100',
            'commission_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'tracking_id' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,featured,popular',
            'featured' => 'boolean',
            'popular' => 'boolean',
            'trending' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'popup_enabled' => 'boolean',
            'popup_delay' => 'nullable|integer|min:0',
            'popup_animation' => 'nullable|string|max:100',
            'popup_position' => 'nullable|string|max:100',
            'popup_style' => 'nullable|string|max:100',
            'button_text' => 'nullable|string|max:100',
            'button_color' => 'nullable|string|max:7',
            'button_hover_effect' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'featured_until' => 'nullable|date|after:now',
        ]);

        $validated['updated_by'] = Auth::id();

        $product->update($validated);

        // Handle media uploads
        if ($request->hasFile('image')) {
            $product->clearMediaCollection('products');
            $product->addMediaFromRequest('image')
                    ->toMediaCollection('products', 'public');
        }

        // Handle gallery images
        if ($request->hasFile('gallery')) {
            $product->clearMediaCollection('gallery');
            foreach ($request->file('gallery') as $image) {
                $product->addMedia($image)
                        ->toMediaCollection('gallery', 'public');
            }
        }

        return redirect()->route('admin.products.index')
                        ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
                        ->with('success', 'Product deleted successfully!');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $productIds = $request->input('product_ids', []);

        if (empty($productIds)) {
            return back()->with('error', 'Please select products to perform action.');
        }

        switch ($action) {
            case 'activate':
                Product::whereIn('id', $productIds)->update(['status' => 'active']);
                $message = 'Selected products activated successfully!';
                break;

            case 'deactivate':
                Product::whereIn('id', $productIds)->update(['status' => 'inactive']);
                $message = 'Selected products deactivated successfully!';
                break;

            case 'feature':
                Product::whereIn('id', $productIds)->update(['featured' => true]);
                $message = 'Selected products featured successfully!';
                break;

            case 'unfeature':
                Product::whereIn('id', $productIds)->update(['featured' => false]);
                $message = 'Selected products unfeatured successfully!';
                break;

            case 'mark_popular':
                Product::whereIn('id', $productIds)->update(['popular' => true]);
                $message = 'Selected products marked as popular successfully!';
                break;

            case 'unmark_popular':
                Product::whereIn('id', $productIds)->update(['popular' => false]);
                $message = 'Selected products unmarked as popular successfully!';
                break;

            case 'mark_trending':
                Product::whereIn('id', $productIds)->update(['trending' => true]);
                $message = 'Selected products marked as trending successfully!';
                break;

            case 'unmark_trending':
                Product::whereIn('id', $productIds)->update(['trending' => false]);
                $message = 'Selected products unmarked as trending successfully!';
                break;

            case 'delete':
                Product::whereIn('id', $productIds)->delete();
                $message = 'Selected products deleted successfully!';
                break;

            default:
                return back()->with('error', 'Invalid action selected.');
        }

        return back()->with('success', $message);
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'status' => $product->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'status' => $product->status,
            'message' => "Product {$product->status} successfully!"
        ]);
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['featured' => !$product->featured]);

        return response()->json([
            'success' => true,
            'featured' => $product->featured,
            'message' => $product->featured ? 'Product featured successfully!' : 'Product unfeatured successfully!'
        ]);
    }

    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->title = $product->title . ' (Copy)';
        $newProduct->status = 'inactive';
        $newProduct->featured = false;
        $newProduct->popular = false;
        $newProduct->trending = false;
        $newProduct->sku = $product->sku ? $product->sku . '_copy' : null;
        $newProduct->created_by = Auth::id();
        $newProduct->save();

        // Copy media
        foreach ($product->getMedia() as $media) {
            $newProduct->addMedia($media->getPath())
                       ->toMediaCollection($media->collection_name, 'public');
        }

        return redirect()->route('admin.products.edit', $newProduct)
                        ->with('success', 'Product duplicated successfully! You can now edit the copy.');
    }

    public function analytics(Product $product)
    {
        $product->load(['clicks', 'conversions']);

        $clickData = $product->clicks()
                            ->selectRaw('DATE(created_at) as date, COUNT(*) as clicks')
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get();

        $conversionData = $product->conversions()
                                 ->selectRaw('DATE(created_at) as date, COUNT(*) as conversions')
                                 ->groupBy('date')
                                 ->orderBy('date')
                                 ->get();

        $revenueData = $product->conversions()
                              ->selectRaw('DATE(created_at) as date, SUM(commission_earned) as revenue')
                              ->groupBy('date')
                              ->orderBy('date')
                              ->get();

        return view('admin.products.analytics', compact('product', 'clickData', 'conversionData', 'revenueData'));
    }

    public function export(Request $request)
    {
        $query = Product::with(['store', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('availability')) {
            $query->where('availability', $request->availability);
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $products = $query->get();

        $filename = 'products_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Title', 'Store', 'Category', 'Brand', 'SKU', 'Original Price',
                'Current Price', 'Availability', 'Status', 'Featured', 'Popular',
                'Clicks', 'Conversions', 'Revenue', 'Created At'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->title,
                    $product->store->name ?? '',
                    $product->category->name ?? '',
                    $product->brand,
                    $product->sku,
                    $product->original_price,
                    $product->current_price,
                    $product->availability,
                    $product->status,
                    $product->featured ? 'Yes' : 'No',
                    $product->popular ? 'Yes' : 'No',
                    $product->click_count,
                    $product->conversion_count,
                    $product->conversions->sum('commission_earned'),
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateButtonSettings(Request $request, Product $product)
    {
        $validated = $request->validate([
            'button_text' => 'required|string|max:100',
            'button_color' => 'required|string|max:7',
            'button_hover_effect' => 'required|string|max:100',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Button settings updated successfully!'
        ]);
    }
}