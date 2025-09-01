<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->paginate(15);
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sliders,slug',
            'is_active' => 'sometimes|boolean',
            'slides' => 'nullable|array',
            'slides.*.title' => 'nullable|string|max:255',
            'slides.*.subtitle' => 'nullable|string|max:255',
            'slides.*.cta_label' => 'nullable|string|max:255',
            'slides.*.cta_url' => 'nullable|url',
            'slides.*.image' => 'nullable|image|max:4096',
        ]);

        $slides = [];
        if ($request->filled('slides')) {
            foreach ($request->input('slides') as $index => $slide) {
                $imagePath = null;
                if ($request->hasFile("slides.$index.image")) {
                    $stored = $request->file("slides.$index.image")->store('public/sliders');
                    $imagePath = Storage::url($stored);
                }
                $slides[] = [
                    'title' => $slide['title'] ?? null,
                    'subtitle' => $slide['subtitle'] ?? null,
                    'cta_label' => $slide['cta_label'] ?? null,
                    'cta_url' => $slide['cta_url'] ?? null,
                    'image' => $imagePath,
                ];
            }
        }

        $slider = Slider::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'slides' => $slides,
            'is_active' => (bool)($validated['is_active'] ?? true),
        ]);

        return redirect()->route('admin.sliders.edit', $slider)
            ->with('success', 'Slider created successfully.');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required','string','max:255', Rule::unique('sliders','slug')->ignore($slider->id)],
            'is_active' => 'sometimes|boolean',
            'slides' => 'nullable|array',
            'slides.*.title' => 'nullable|string|max:255',
            'slides.*.subtitle' => 'nullable|string|max:255',
            'slides.*.cta_label' => 'nullable|string|max:255',
            'slides.*.cta_url' => 'nullable|url',
            'slides.*.existing_image' => 'nullable|string',
            'slides.*.image' => 'nullable|image|max:4096',
        ]);

        $newSlides = [];
        $inputSlides = $request->input('slides', []);
        foreach ($inputSlides as $index => $slide) {
            $imagePath = $slide['existing_image'] ?? null;
            if ($request->hasFile("slides.$index.image")) {
                $stored = $request->file("slides.$index.image")->store('public/sliders');
                $imagePath = Storage::url($stored);
            }
            if ($slide || $imagePath) {
                $newSlides[] = [
                    'title' => $slide['title'] ?? null,
                    'subtitle' => $slide['subtitle'] ?? null,
                    'cta_label' => $slide['cta_label'] ?? null,
                    'cta_url' => $slide['cta_url'] ?? null,
                    'image' => $imagePath,
                ];
            }
        }

        $slider->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'slides' => $newSlides,
            'is_active' => (bool)($validated['is_active'] ?? false),
        ]);

        return back()->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Slider deleted.');
    }
}

