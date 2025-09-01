<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function header()
    {
        $header = optional(Setting::where('key', 'header_html')->first())->value['html'] ?? '';
        return view('admin.appearance.header', compact('header'));
    }

    public function saveHeader(Request $request)
    {
        $request->validate(['html' => 'nullable|string']);
        Setting::updateOrCreate(['key' => 'header_html'], ['value' => ['html' => $request->html], 'group' => 'appearance']);
        return back()->with('success', 'Header updated.');
    }

    public function footer()
    {
        $footer = optional(Setting::where('key', 'footer_html')->first())->value['html'] ?? '';
        return view('admin.appearance.footer', compact('footer'));
    }

    public function saveFooter(Request $request)
    {
        $request->validate(['html' => 'nullable|string']);
        Setting::updateOrCreate(['key' => 'footer_html'], ['value' => ['html' => $request->html], 'group' => 'appearance']);
        return back()->with('success', 'Footer updated.');
    }

    public function menus()
    {
        $menu = Menu::firstOrCreate(['location' => 'primary'], ['name' => 'Primary Menu', 'items' => [], 'is_active' => true]);
        return view('admin.appearance.menus', compact('menu'));
    }

    public function saveMenus(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:50',
            'items' => 'nullable|array',
            'items.*.label' => 'required_with:items|string|max:255',
            'items.*.url' => 'required_with:items|url',
            'items.*.target' => 'nullable|in:_self,_blank',
        ]);
        $menu = Menu::firstOrCreate(['location' => $request->location]);
        $menu->update([
            'name' => $request->name,
            'items' => $request->items ?? [],
            'is_active' => true,
        ]);
        return back()->with('success', 'Menu saved.');
    }
}

