<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of menus
     */
    public function index()
    {
        $menus = Menu::withCount('items')->orderBy('name')->get();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new menu
     */
    public function create()
    {
        return view('admin.menus.create');
    }

    /**
     * Store a newly created menu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255|unique:menus,location',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        $menu = Menu::create($validated);

        return redirect()->route('admin.menus.edit', $menu)
                        ->with('success', 'Menu created successfully.');
    }

    /**
     * Display the specified menu
     */
    public function show(Menu $menu)
    {
        $menu->load('items');
        return view('admin.menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified menu
     */
    public function edit(Menu $menu)
    {
        $menu->load(['items' => function($query) {
            $query->orderBy('sort_order');
        }]);
        
        return view('admin.menus.edit', compact('menu'));
    }

    /**
     * Update the specified menu
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255|unique:menus,location,' . $menu->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'items' => 'nullable|array',
            'items.*.title' => 'required|string|max:255',
            'items.*.url' => 'required|string|max:500',
            'items.*.target' => 'nullable|in:_self,_blank',
            'items.*.icon' => 'nullable|string|max:255',
            'items.*.css_class' => 'nullable|string|max:255',
            'items.*.sort_order' => 'nullable|integer'
        ]);

        $menu->update([
            'name' => $validated['name'],
            'location' => $validated['location'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? false
        ]);

        // Update menu items
        if (isset($validated['items'])) {
            // Delete existing items
            $menu->items()->delete();

            // Create new items
            foreach ($validated['items'] as $index => $itemData) {
                MenuItem::create([
                    'menu_id' => $menu->id,
                    'title' => $itemData['title'],
                    'url' => $itemData['url'],
                    'target' => $itemData['target'] ?? '_self',
                    'icon' => $itemData['icon'] ?? null,
                    'css_class' => $itemData['css_class'] ?? null,
                    'sort_order' => $itemData['sort_order'] ?? $index,
                    'is_active' => true
                ]);
            }
        }

        return back()->with('success', 'Menu updated successfully.');
    }

    /**
     * Remove the specified menu
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        
        return redirect()->route('admin.menus.index')
                        ->with('success', 'Menu deleted successfully.');
    }

    /**
     * Add menu item
     */
    public function addItem(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'target' => 'nullable|in:_self,_blank',
            'icon' => 'nullable|string|max:255',
            'css_class' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer'
        ]);

        $validated['menu_id'] = $menu->id;
        $validated['target'] = $validated['target'] ?? '_self';
        $validated['sort_order'] = $validated['sort_order'] ?? $menu->items()->count();
        $validated['is_active'] = true;

        MenuItem::create($validated);

        return back()->with('success', 'Menu item added successfully.');
    }

    /**
     * Update menu item order
     */
    public function updateOrder(Request $request, Menu $menu)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->items as $item) {
            MenuItem::where('id', $item['id'])
                   ->where('menu_id', $menu->id)
                   ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}