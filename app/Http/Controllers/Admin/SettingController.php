<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display general settings
     */
    public function general()
    {
        $settings = Setting::whereIn('key', [
            'site_name',
            'site_description',
            'site_logo',
            'site_favicon',
            'contact_email',
            'contact_phone',
            'contact_address',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_youtube'
        ])->pluck('value', 'key');

        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'site_favicon' => 'nullable|image|mimes:ico,png|max:512',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:20',
            'contact_address' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_youtube' => 'nullable|url',
        ]);

        foreach ($validated as $key => $value) {
            if ($key === 'site_logo' || $key === 'site_favicon') {
                if ($request->hasFile($key)) {
                    // Handle file upload
                    $file = $request->file($key);
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/settings'), $filename);
                    $value = 'uploads/settings/' . $filename;
                } else {
                    continue; // Skip if no file uploaded
                }
            }

            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => ($key === 'site_logo' || $key === 'site_favicon') ? 'file' : 'string'
                ]
            );
        }

        return back()->with('success', 'General settings updated successfully.');
    }

    /**
     * Display SEO settings
     */
    public function seo()
    {
        $settings = Setting::whereIn('key', [
            'meta_title',
            'meta_description',
            'meta_keywords',
            'google_analytics_id',
            'google_tag_manager_id',
            'google_site_verification',
            'bing_site_verification',
            'yandex_site_verification'
        ])->pluck('value', 'key');

        return view('admin.settings.seo', compact('settings'));
    }

    /**
     * Update SEO settings
     */
    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'google_analytics_id' => 'nullable|string|max:255',
            'google_tag_manager_id' => 'nullable|string|max:255',
            'google_site_verification' => 'nullable|string|max:255',
            'bing_site_verification' => 'nullable|string|max:255',
            'yandex_site_verification' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        return back()->with('success', 'SEO settings updated successfully.');
    }

    /**
     * Display integration settings
     */
    public function integrations()
    {
        $settings = Setting::whereIn('key', [
            'adsense_publisher_id',
            'adsense_auto_ads',
            'onesignal_app_id',
            'onesignal_rest_api_key',
            'pusher_app_id',
            'pusher_app_key',
            'pusher_app_secret',
            'pusher_app_cluster'
        ])->pluck('value', 'key');

        return view('admin.settings.integrations', compact('settings'));
    }

    /**
     * Update integration settings
     */
    public function updateIntegrations(Request $request)
    {
        $validated = $request->validate([
            'adsense_publisher_id' => 'nullable|string|max:255',
            'adsense_auto_ads' => 'boolean',
            'onesignal_app_id' => 'nullable|string|max:255',
            'onesignal_rest_api_key' => 'nullable|string|max:255',
            'pusher_app_id' => 'nullable|string|max:255',
            'pusher_app_key' => 'nullable|string|max:255',
            'pusher_app_secret' => 'nullable|string|max:255',
            'pusher_app_cluster' => 'nullable|string|max:10',
        ]);

        foreach ($validated as $key => $value) {
            $type = $key === 'adsense_auto_ads' ? 'boolean' : 'string';
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => $type]
            );
        }

        return back()->with('success', 'Integration settings updated successfully.');
    }
}