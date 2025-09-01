<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function seo()
    {
        $seo = optional(Setting::where('key', 'seo')->first())->value ?? [
            'default_title' => config('app.name'),
            'default_description' => 'Best deals, coupons and affiliate products from top stores',
            'default_keywords' => 'deals,coupons,discounts,offers,affiliate',
            'og_image' => null,
            'twitter_image' => null,
        ];
        return view('admin.integrations.seo', compact('seo'));
    }

    public function seoSave(Request $request)
    {
        $validated = $request->validate([
            'default_title' => 'required|string|max:255',
            'default_description' => 'required|string|max:500',
            'default_keywords' => 'nullable|string|max:500',
            'og_image' => 'nullable|url',
            'twitter_image' => 'nullable|url',
        ]);

        Setting::updateOrCreate(['key' => 'seo'], ['value' => $validated, 'group' => 'seo']);
        return back()->with('success', 'SEO settings saved.');
    }

    public function google()
    {
        $sitekit = optional(Setting::where('key', 'google_site_kit')->first())->value ?? [
            'site_kit_id' => null,
            'analytics_id' => null,
            'tag_manager_id' => null,
            'search_console_verified' => false,
        ];
        return view('admin.integrations.google', compact('sitekit'));
    }

    public function googleSave(Request $request)
    {
        $validated = $request->validate([
            'site_kit_id' => 'nullable|string|max:100',
            'analytics_id' => 'nullable|string|max:30',
            'tag_manager_id' => 'nullable|string|max:30',
            'search_console_verified' => 'sometimes|boolean',
        ]);

        Setting::updateOrCreate(['key' => 'google_site_kit'], ['value' => [
            'site_kit_id' => $validated['site_kit_id'] ?? null,
            'analytics_id' => $validated['analytics_id'] ?? null,
            'tag_manager_id' => $validated['tag_manager_id'] ?? null,
            'search_console_verified' => (bool)($validated['search_console_verified'] ?? false),
        ], 'group' => 'integrations']);

        return back()->with('success', 'Google Site Kit settings saved.');
    }

    public function adsense()
    {
        $adsense = optional(Setting::where('key', 'adsense')->first())->value ?? [
            'enabled' => false,
            'publisher_id' => null,
            'auto_ads' => true,
        ];
        return view('admin.integrations.adsense', compact('adsense'));
    }

    public function adsenseSave(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'sometimes|boolean',
            'publisher_id' => 'nullable|string|max:30',
            'auto_ads' => 'sometimes|boolean',
        ]);

        Setting::updateOrCreate(['key' => 'adsense'], ['value' => [
            'enabled' => (bool)($validated['enabled'] ?? false),
            'publisher_id' => $validated['publisher_id'] ?? null,
            'auto_ads' => (bool)($validated['auto_ads'] ?? true),
        ], 'group' => 'integrations']);

        return back()->with('success', 'AdSense settings saved.');
    }

    public function onesignal()
    {
        $onesignal = optional(Setting::where('key', 'onesignal')->first())->value ?? [
            'app_id' => null,
            'safari_web_id' => null,
            'enabled' => false,
        ];
        return view('admin.integrations.onesignal', compact('onesignal'));
    }

    public function onesignalSave(Request $request)
    {
        $validated = $request->validate([
            'app_id' => 'nullable|string|max:64',
            'safari_web_id' => 'nullable|string|max:64',
            'enabled' => 'sometimes|boolean',
        ]);

        Setting::updateOrCreate(['key' => 'onesignal'], ['value' => [
            'app_id' => $validated['app_id'] ?? null,
            'safari_web_id' => $validated['safari_web_id'] ?? null,
            'enabled' => (bool)($validated['enabled'] ?? false),
        ], 'group' => 'integrations']);

        return back()->with('success', 'OneSignal settings saved.');
    }
}

