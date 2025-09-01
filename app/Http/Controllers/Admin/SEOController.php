<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SEOController extends Controller
{
    public function index()
    {
        $seoSettings = Setting::whereIn('key', [
            'seo_site_title',
            'seo_site_description', 
            'seo_keywords',
            'seo_og_title',
            'seo_og_description',
            'seo_og_image',
            'seo_twitter_title',
            'seo_twitter_description',
            'seo_twitter_image',
            'google_analytics_id',
            'google_tag_manager_id',
            'google_site_verification',
            'bing_verification',
            'facebook_pixel_id',
            'seo_robots_txt',
            'seo_sitemap_enabled'
        ])->pluck('value', 'key');

        return view('admin.seo.index', compact('seoSettings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'seo_site_title' => 'nullable|string|max:255',
            'seo_site_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',
            'seo_og_title' => 'nullable|string|max:255',
            'seo_og_description' => 'nullable|string|max:500',
            'seo_og_image' => 'nullable|url',
            'seo_twitter_title' => 'nullable|string|max:255',
            'seo_twitter_description' => 'nullable|string|max:500',
            'seo_twitter_image' => 'nullable|url',
            'google_analytics_id' => 'nullable|string|max:255',
            'google_tag_manager_id' => 'nullable|string|max:255',
            'google_site_verification' => 'nullable|string|max:255',
            'bing_verification' => 'nullable|string|max:255',
            'facebook_pixel_id' => 'nullable|string|max:255',
            'seo_robots_txt' => 'nullable|string',
            'seo_sitemap_enabled' => 'boolean'
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'type' => 'string']
            );
        }

        // Generate robots.txt if provided
        if (!empty($validated['seo_robots_txt'])) {
            file_put_contents(public_path('robots.txt'), $validated['seo_robots_txt']);
        }

        return back()->with('success', 'SEO settings updated successfully.');
    }

    public function generateSitemap()
    {
        $sitemap = $this->buildSitemap();
        file_put_contents(public_path('sitemap.xml'), $sitemap);
        
        return back()->with('success', 'Sitemap generated successfully.');
    }

    private function buildSitemap()
    {
        $urls = collect();

        // Add static pages
        $urls->push([
            'url' => url('/'),
            'priority' => '1.0',
            'changefreq' => 'daily',
            'lastmod' => now()->toDateString()
        ]);

        $urls->push([
            'url' => route('coupons.index'),
            'priority' => '0.9',
            'changefreq' => 'daily',
            'lastmod' => now()->toDateString()
        ]);

        $urls->push([
            'url' => route('deals.index'),
            'priority' => '0.9',
            'changefreq' => 'daily',
            'lastmod' => now()->toDateString()
        ]);

        // Add dynamic content
        \App\Models\Coupon::where('is_active', true)->chunk(100, function ($coupons) use ($urls) {
            foreach ($coupons as $coupon) {
                $urls->push([
                    'url' => route('coupons.show', $coupon->slug),
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $coupon->updated_at->toDateString()
                ]);
            }
        });

        \App\Models\Deal::where('is_active', true)->chunk(100, function ($deals) use ($urls) {
            foreach ($deals as $deal) {
                $urls->push([
                    'url' => route('deals.show', $deal->slug),
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $deal->updated_at->toDateString()
                ]);
            }
        });

        // Generate XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['url']) . '</loc>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }
}