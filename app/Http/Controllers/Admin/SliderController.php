<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    /**
     * Display a listing of sliders
     */
    public function index()
    {
        $sliders = Slider::orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new slider
     */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /**
     * Store a newly created slider
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'link_url' => 'nullable|url',
            'link_text' => 'nullable|string|max:255',
            'link_target' => 'nullable|in:_self,_blank',
            'text_position' => 'nullable|in:left,center,right',
            'text_color' => 'nullable|string|max:7',
            'overlay_color' => 'nullable|string|max:7',
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? Slider::max('sort_order') + 1;

        $slider = Slider::create($validated);

        // Handle image upload
        if ($request->hasFile('image')) {
            $slider->addMediaFromRequest('image')
                   ->toMediaCollection('image');
        }

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider created successfully.');
    }

    /**
     * Display the specified slider
     */
    public function show(Slider $slider)
    {
        return view('admin.sliders.show', compact('slider'));
    }

    /**
     * Show the form for editing the specified slider
     */
    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified slider
     */
    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'link_url' => 'nullable|url',
            'link_text' => 'nullable|string|max:255',
            'link_target' => 'nullable|in:_self,_blank',
            'text_position' => 'nullable|in:left,center,right',
            'text_color' => 'nullable|string|max:7',
            'overlay_color' => 'nullable|string|max:7',
            'overlay_opacity' => 'nullable|integer|min:0|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $slider->update($validated);

        // Handle image upload
        if ($request->hasFile('image')) {
            $slider->clearMediaCollection('image');
            $slider->addMediaFromRequest('image')
                   ->toMediaCollection('image');
        }

        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider updated successfully.');
    }

    /**
     * Remove the specified slider
     */
    public function destroy(Slider $slider)
    {
        $slider->delete();
        
        return redirect()->route('admin.sliders.index')
                        ->with('success', 'Slider deleted successfully.');
    }

    /**
     * Update slider order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'sliders' => 'required|array',
            'sliders.*.id' => 'required|exists:sliders,id',
            'sliders.*.sort_order' => 'required|integer'
        ]);

        foreach ($request->sliders as $slider) {
            Slider::where('id', $slider['id'])
                  ->update(['sort_order' => $slider['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle slider status
     */
    public function toggleStatus(Slider $slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);
        
        $status = $slider->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Slider {$status} successfully.");
    }
}