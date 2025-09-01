<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = Store::withCount(['coupons', 'deals', 'products'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        return view('admin.stores.index', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.stores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stores,slug',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $store = Store::create($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $store->addMediaFromRequest('logo')
                  ->toMediaCollection('logo');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $store->addMediaFromRequest('banner')
                  ->toMediaCollection('banner');
        }

        return redirect()->route('admin.stores.index')
                        ->with('success', 'Store created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        $store->loadCount(['coupons', 'deals', 'products']);
        
        return view('admin.stores.show', compact('store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:stores,slug,' . $store->id,
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $store->update($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $store->clearMediaCollection('logo');
            $store->addMediaFromRequest('logo')
                  ->toMediaCollection('logo');
        }

        // Handle banner upload
        if ($request->hasFile('banner')) {
            $store->clearMediaCollection('banner');
            $store->addMediaFromRequest('banner')
                  ->toMediaCollection('banner');
        }

        return redirect()->route('admin.stores.index')
                        ->with('success', 'Store updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();
        
        return redirect()->route('admin.stores.index')
                        ->with('success', 'Store deleted successfully.');
    }
}