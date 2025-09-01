<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function edit()
    {
        $theme = optional(Setting::where('key', 'theme')->first())->value ?? [];
        return view('admin.appearance.theme', compact('theme'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'required|string|max:20',
            'secondary_color' => 'required|string|max:20',
            'font_family' => 'required|string|max:100',
            'rounded' => 'sometimes|boolean',
        ]);

        Setting::updateOrCreate(
            ['key' => 'theme'],
            ['value' => [
                'primary_color' => $validated['primary_color'],
                'secondary_color' => $validated['secondary_color'],
                'font_family' => $validated['font_family'],
                'rounded' => (bool)($validated['rounded'] ?? false),
            ], 'group' => 'appearance']
        );

        return back()->with('success', 'Theme settings saved.');
    }
}

