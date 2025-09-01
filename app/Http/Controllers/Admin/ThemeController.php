<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    /**
     * Show theme customization form
     */
    public function edit()
    {
        $settings = Setting::whereIn('key', [
            'theme_primary_color',
            'theme_secondary_color',
            'theme_accent_color',
            'theme_font_family',
            'theme_heading_font',
            'theme_font_size',
            'theme_border_radius',
            'theme_custom_css'
        ])->pluck('value', 'key');

        // Set defaults if not exist
        $defaults = [
            'theme_primary_color' => '#007bff',
            'theme_secondary_color' => '#6c757d',
            'theme_accent_color' => '#28a745',
            'theme_font_family' => 'Inter, system-ui, sans-serif',
            'theme_heading_font' => 'Inter, system-ui, sans-serif',
            'theme_font_size' => '16',
            'theme_border_radius' => '8',
            'theme_custom_css' => ''
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }

        return view('admin.theme.edit', compact('settings'));
    }

    /**
     * Update theme settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme_primary_color' => 'required|string|max:7',
            'theme_secondary_color' => 'required|string|max:7',
            'theme_accent_color' => 'required|string|max:7',
            'theme_font_family' => 'required|string|max:255',
            'theme_heading_font' => 'required|string|max:255',
            'theme_font_size' => 'required|integer|min:12|max:24',
            'theme_border_radius' => 'required|integer|min:0|max:50',
            'theme_custom_css' => 'nullable|string'
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        // Generate CSS file
        $this->generateThemeCSS($validated);

        return back()->with('success', 'Theme settings updated successfully.');
    }

    /**
     * Generate theme CSS file
     */
    private function generateThemeCSS($settings)
    {
        $css = ":root {
    --primary-color: {$settings['theme_primary_color']};
    --secondary-color: {$settings['theme_secondary_color']};
    --accent-color: {$settings['theme_accent_color']};
    --font-family: {$settings['theme_font_family']};
    --heading-font: {$settings['theme_heading_font']};
    --font-size: {$settings['theme_font_size']}px;
    --border-radius: {$settings['theme_border_radius']}px;
}

body {
    font-family: var(--font-family);
    font-size: var(--font-size);
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--heading-font);
}

.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-secondary {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
}

.btn-accent {
    background-color: var(--accent-color);
    border-color: var(--accent-color);
    color: white;
}

.card, .btn, .form-control, .modal-content {
    border-radius: var(--border-radius);
}

.text-primary {
    color: var(--primary-color) !important;
}

.bg-primary {
    background-color: var(--primary-color) !important;
}

.border-primary {
    border-color: var(--primary-color) !important;
}

/* Custom CSS */
{$settings['theme_custom_css']}
";

        // Ensure directory exists
        $cssDir = public_path('css');
        if (!file_exists($cssDir)) {
            mkdir($cssDir, 0755, true);
        }

        file_put_contents(public_path('css/theme.css'), $css);
    }

    /**
     * Reset theme to default
     */
    public function reset()
    {
        $defaultSettings = [
            'theme_primary_color' => '#007bff',
            'theme_secondary_color' => '#6c757d',
            'theme_accent_color' => '#28a745',
            'theme_font_family' => 'Inter, system-ui, sans-serif',
            'theme_heading_font' => 'Inter, system-ui, sans-serif',
            'theme_font_size' => '16',
            'theme_border_radius' => '8',
            'theme_custom_css' => ''
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        $this->generateThemeCSS($defaultSettings);

        return back()->with('success', 'Theme reset to default successfully.');
    }
}