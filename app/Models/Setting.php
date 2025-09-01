<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type', // string, integer, boolean, json, text, file
        'group', // general, theme, seo, adsense, social, email, notification, security, performance
        'label',
        'description',
        'options', // JSON array for select/radio/checkbox options
        'validation', // JSON validation rules
        'is_public', // whether this setting can be accessed publicly
        'is_required',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'options' => 'array',
        'validation' => 'array',
        'is_public' => 'boolean',
        'is_required' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('key', 'like', "%{$search}%")
              ->orWhere('label', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'integer':
                return (int) $this->value;
            case 'json':
                return json_decode($this->value, true);
            case 'file':
                return asset('storage/settings/' . $this->value);
            default:
                return $this->value;
        }
    }

    public function getTypeTextAttribute()
    {
        return match ($this->type) {
            'string' => 'Text Input',
            'integer' => 'Number Input',
            'boolean' => 'Checkbox',
            'json' => 'JSON Input',
            'text' => 'Textarea',
            'file' => 'File Upload',
            'select' => 'Dropdown',
            'radio' => 'Radio Buttons',
            'checkbox' => 'Multiple Checkboxes',
            'color' => 'Color Picker',
            'date' => 'Date Picker',
            'time' => 'Time Picker',
            'datetime' => 'Date & Time Picker',
            default => ucfirst($this->type)
        };
    }

    public function getGroupTextAttribute()
    {
        return match ($this->group) {
            'general' => 'General Settings',
            'theme' => 'Theme & Appearance',
            'seo' => 'SEO & Meta',
            'adsense' => 'AdSense & Ads',
            'social' => 'Social Media',
            'email' => 'Email Settings',
            'notification' => 'Notifications',
            'security' => 'Security',
            'performance' => 'Performance',
            'payment' => 'Payment',
            'shipping' => 'Shipping',
            'tax' => 'Tax Settings',
            'analytics' => 'Analytics',
            'backup' => 'Backup & Maintenance',
            default => ucfirst($this->group)
        };
    }

    public function getGroupIconAttribute()
    {
        return match ($this->group) {
            'general' => 'fas fa-cog',
            'theme' => 'fas fa-palette',
            'seo' => 'fas fa-search',
            'adsense' => 'fas fa-ad',
            'social' => 'fas fa-share-alt',
            'email' => 'fas fa-envelope',
            'notification' => 'fas fa-bell',
            'security' => 'fas fa-shield-alt',
            'performance' => 'fas fa-tachometer-alt',
            'payment' => 'fas fa-credit-card',
            'shipping' => 'fas fa-shipping-fast',
            'tax' => 'fas fa-percentage',
            'analytics' => 'fas fa-chart-bar',
            'backup' => 'fas fa-database',
            default => 'fas fa-cog'
        };
    }

    public function getGroupColorAttribute()
    {
        return match ($this->group) {
            'general' => 'primary',
            'theme' => 'info',
            'seo' => 'success',
            'adsense' => 'warning',
            'social' => 'danger',
            'email' => 'secondary',
            'notification' => 'dark',
            'security' => 'danger',
            'performance' => 'success',
            'payment' => 'primary',
            'shipping' => 'info',
            'tax' => 'warning',
            'analytics' => 'success',
            'backup' => 'secondary',
            default => 'secondary'
        };
    }

    public function getIsRequiredTextAttribute()
    {
        return $this->is_required ? 'Required' : 'Optional';
    }

    public function getIsRequiredColorAttribute()
    {
        return $this->is_required ? 'danger' : 'secondary';
    }

    public function getIsPublicTextAttribute()
    {
        return $this->is_public ? 'Public' : 'Private';
    }

    public function getIsPublicColorAttribute()
    {
        return $this->is_public ? 'success' : 'warning';
    }

    // Methods
    public function getValue($default = null)
    {
        if ($this->value === null || $this->value === '') {
            return $default;
        }

        return $this->formatted_value;
    }

    public function setValue($value)
    {
        $this->update(['value' => $value]);
        
        // Clear cache for this setting
        Cache::forget("setting.{$this->key}");
        
        return $this;
    }

    public function getOptions()
    {
        return $this->options ?: [];
    }

    public function getValidationRules()
    {
        return $this->validation ?: [];
    }

    public function isRequired()
    {
        return $this->is_required;
    }

    public function isPublic()
    {
        return $this->is_public;
    }

    public function belongsToGroup($group)
    {
        return $this->group === $group;
    }

    public function hasOptions()
    {
        return !empty($this->options);
    }

    public function hasValidation()
    {
        return !empty($this->validation);
    }

    // Static Methods
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        return $setting->getValue($default);
    }

    public static function set($key, $value, $options = [])
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            $setting = static::create(array_merge([
                'key' => $key,
                'value' => $value,
                'type' => $options['type'] ?? 'string',
                'group' => $options['group'] ?? 'general',
                'label' => $options['label'] ?? ucfirst(str_replace('_', ' ', $key)),
                'description' => $options['description'] ?? null,
                'is_public' => $options['is_public'] ?? false,
                'is_required' => $options['is_required'] ?? false,
                'sort_order' => $options['sort_order'] ?? 0,
            ], $options));
        } else {
            $setting->setValue($value);
        }
        
        return $setting;
    }

    public static function getGroup($group)
    {
        return static::byGroup($group)->ordered()->get();
    }

    public static function getPublicSettings()
    {
        return static::public()->ordered()->get();
    }

    public static function getRequiredSettings()
    {
        return static::required()->ordered()->get();
    }

    public static function getAllSettings()
    {
        return static::ordered()->get()->groupBy('group');
    }

    public static function getSettingsArray()
    {
        $settings = static::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->getValue();
        }
        
        return $result;
    }

    public static function getSettingsByGroup($group)
    {
        $settings = static::byGroup($group)->ordered()->get();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->getValue();
        }
        
        return $result;
    }

    public static function bulkUpdate($data)
    {
        $updated = 0;
        $errors = [];
        
        foreach ($data as $key => $value) {
            try {
                $setting = static::where('key', $key)->first();
                
                if ($setting) {
                    $setting->setValue($value);
                    $updated++;
                } else {
                    $errors[] = "Setting '{$key}' not found";
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to update '{$key}': " . $e->getMessage();
            }
        }
        
        return [
            'updated' => $updated,
            'errors' => $errors,
            'success' => empty($errors)
        ];
    }

    public static function createDefaultSettings()
    {
        $defaults = [
            // General Settings
            'site_name' => [
                'value' => 'Coupon Deals CMS',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Site Name',
                'description' => 'The name of your website',
                'is_required' => true,
                'is_public' => true,
            ],
            'site_description' => [
                'value' => 'Modern Coupon Deals and Affiliate Product CMS',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Site Description',
                'description' => 'A brief description of your website',
                'is_public' => true,
            ],
            'site_url' => [
                'value' => config('app.url'),
                'type' => 'string',
                'group' => 'general',
                'label' => 'Site URL',
                'description' => 'Your website URL',
                'is_required' => true,
                'is_public' => true,
            ],
            'contact_email' => [
                'value' => 'admin@example.com',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Contact Email',
                'description' => 'Primary contact email address',
                'is_required' => true,
                'is_public' => true,
            ],
            'contact_phone' => [
                'value' => '+91-1234567890',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Contact Phone',
                'description' => 'Primary contact phone number',
                'is_public' => true,
            ],
            'timezone' => [
                'value' => 'Asia/Kolkata',
                'type' => 'select',
                'group' => 'general',
                'label' => 'Timezone',
                'description' => 'Default timezone for the website',
                'options' => ['Asia/Kolkata', 'UTC', 'America/New_York', 'Europe/London'],
                'is_public' => true,
            ],
            'date_format' => [
                'value' => 'd/m/Y',
                'type' => 'select',
                'group' => 'general',
                'label' => 'Date Format',
                'description' => 'Default date format',
                'options' => ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd-m-Y'],
                'is_public' => true,
            ],
            'time_format' => [
                'value' => 'H:i',
                'type' => 'select',
                'group' => 'general',
                'label' => 'Time Format',
                'description' => 'Default time format',
                'options' => ['H:i', 'h:i A', 'H:i:s', 'h:i:s A'],
                'is_public' => true,
            ],
            'currency' => [
                'value' => 'INR',
                'type' => 'select',
                'group' => 'general',
                'label' => 'Default Currency',
                'description' => 'Default currency for prices',
                'options' => ['INR', 'USD', 'EUR', 'GBP'],
                'is_required' => true,
                'is_public' => true,
            ],
            'currency_symbol' => [
                'value' => '₹',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Currency Symbol',
                'description' => 'Currency symbol to display',
                'is_required' => true,
                'is_public' => true,
            ],

            // Theme Settings
            'primary_color' => [
                'value' => '#007bff',
                'type' => 'color',
                'group' => 'theme',
                'label' => 'Primary Color',
                'description' => 'Main brand color',
                'is_public' => true,
            ],
            'secondary_color' => [
                'value' => '#6c757d',
                'type' => 'color',
                'group' => 'theme',
                'label' => 'Secondary Color',
                'description' => 'Secondary brand color',
                'is_public' => true,
            ],
            'accent_color' => [
                'value' => '#28a745',
                'type' => 'color',
                'group' => 'theme',
                'label' => 'Accent Color',
                'description' => 'Accent color for highlights',
                'is_public' => true,
            ],
            'font_family' => [
                'value' => 'Inter, sans-serif',
                'type' => 'select',
                'group' => 'theme',
                'label' => 'Font Family',
                'description' => 'Primary font family',
                'options' => ['Inter, sans-serif', 'Roboto, sans-serif', 'Open Sans, sans-serif', 'Lato, sans-serif'],
                'is_public' => true,
            ],
            'font_size_base' => [
                'value' => '16px',
                'type' => 'select',
                'group' => 'theme',
                'label' => 'Base Font Size',
                'description' => 'Base font size for the website',
                'options' => ['14px', '16px', '18px', '20px'],
                'is_public' => true,
            ],
            'border_radius' => [
                'value' => '8px',
                'type' => 'select',
                'group' => 'theme',
                'label' => 'Border Radius',
                'description' => 'Default border radius for elements',
                'options' => ['0px', '4px', '8px', '12px', '16px'],
                'is_public' => true,
            ],
            'box_shadow' => [
                'value' => '0 2px 4px rgba(0,0,0,0.1)',
                'type' => 'string',
                'group' => 'theme',
                'label' => 'Box Shadow',
                'description' => 'Default box shadow for cards',
                'is_public' => true,
            ],

            // SEO Settings
            'meta_title' => [
                'value' => 'Coupon Deals CMS - Best Coupons, Deals & Offers',
                'type' => 'string',
                'group' => 'seo',
                'label' => 'Default Meta Title',
                'description' => 'Default meta title for pages',
                'is_public' => true,
            ],
            'meta_description' => [
                'value' => 'Find the best coupons, deals, and offers from top stores. Save money on your purchases with our exclusive deals.',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Default Meta Description',
                'description' => 'Default meta description for pages',
                'is_public' => true,
            ],
            'meta_keywords' => [
                'value' => 'coupons, deals, offers, discounts, savings, shopping',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Default Meta Keywords',
                'description' => 'Default meta keywords for pages',
                'is_public' => true,
            ],
            'og_image' => [
                'value' => 'images/og-default.jpg',
                'type' => 'file',
                'group' => 'seo',
                'label' => 'Default OG Image',
                'description' => 'Default Open Graph image for social sharing',
                'is_public' => true,
            ],
            'twitter_image' => [
                'value' => 'images/twitter-default.jpg',
                'type' => 'file',
                'group' => 'seo',
                'label' => 'Default Twitter Image',
                'description' => 'Default Twitter image for social sharing',
                'is_public' => true,
            ],
            'google_analytics_id' => [
                'value' => '',
                'type' => 'string',
                'group' => 'seo',
                'label' => 'Google Analytics ID',
                'description' => 'Google Analytics tracking ID (GA4)',
                'is_public' => false,
            ],
            'google_tag_manager_id' => [
                'value' => '',
                'type' => 'string',
                'group' => 'seo',
                'label' => 'Google Tag Manager ID',
                'description' => 'Google Tag Manager container ID',
                'is_public' => false,
            ],
            'google_site_verification' => [
                'value' => '',
                'type' => 'string',
                'group' => 'seo',
                'label' => 'Google Site Verification',
                'description' => 'Google Search Console verification code',
                'is_public' => false,
            ],
            'bing_verification' => [
                'value' => '',
                'type' => 'string',
                'group' => 'seo',
                'label' => 'Bing Verification',
                'description' => 'Bing Webmaster Tools verification code',
                'is_public' => false,
            ],

            // AdSense Settings
            'adsense_enabled' => [
                'value' => false,
                'type' => 'boolean',
                'group' => 'adsense',
                'label' => 'Enable AdSense',
                'description' => 'Enable Google AdSense integration',
                'is_public' => false,
            ],
            'adsense_publisher_id' => [
                'value' => '',
                'type' => 'string',
                'group' => 'adsense',
                'label' => 'AdSense Publisher ID',
                'description' => 'Your Google AdSense publisher ID',
                'is_public' => false,
            ],
            'adsense_client_id' => [
                'value' => '',
                'type' => 'string',
                'group' => 'adsense',
                'label' => 'AdSense Client ID',
                'description' => 'Your Google AdSense client ID',
                'is_public' => false,
            ],
            'adsense_header_ad' => [
                'value' => '',
                'type' => 'text',
                'group' => 'adsense',
                'label' => 'Header Ad Code',
                'description' => 'AdSense code for header area',
                'is_public' => false,
            ],
            'adsense_sidebar_ad' => [
                'value' => '',
                'type' => 'text',
                'group' => 'adsense',
                'label' => 'Sidebar Ad Code',
                'description' => 'AdSense code for sidebar area',
                'is_public' => false,
            ],
            'adsense_footer_ad' => [
                'value' => '',
                'type' => 'text',
                'group' => 'adsense',
                'label' => 'Footer Ad Code',
                'description' => 'AdSense code for footer area',
                'is_public' => false,
            ],
            'adsense_in_content_ad' => [
                'value' => '',
                'type' => 'text',
                'group' => 'adsense',
                'label' => 'In-Content Ad Code',
                'description' => 'AdSense code for content area',
                'is_public' => false,
            ],

            // Social Media Settings
            'facebook_url' => [
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Facebook URL',
                'description' => 'Your Facebook page URL',
                'is_public' => true,
            ],
            'twitter_url' => [
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Twitter URL',
                'description' => 'Your Twitter profile URL',
                'is_public' => true,
            ],
            'instagram_url' => [
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'Instagram URL',
                'description' => 'Your Instagram profile URL',
                'is_public' => true,
            ],
            'youtube_url' => [
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'YouTube URL',
                'description' => 'Your YouTube channel URL',
                'is_public' => true,
            ],
            'linkedin_url' => [
                'value' => '',
                'type' => 'string',
                'group' => 'social',
                'label' => 'LinkedIn URL',
                'description' => 'Your LinkedIn profile URL',
                'is_public' => true,
            ],

            // Notification Settings
            'onesignal_enabled' => [
                'value' => false,
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'Enable OneSignal',
                'description' => 'Enable OneSignal push notifications',
                'is_public' => false,
            ],
            'onesignal_app_id' => [
                'value' => '',
                'type' => 'string',
                'group' => 'notification',
                'label' => 'OneSignal App ID',
                'description' => 'Your OneSignal application ID',
                'is_public' => false,
            ],
            'onesignal_rest_api_key' => [
                'value' => '',
                'type' => 'string',
                'group' => 'notification',
                'label' => 'OneSignal REST API Key',
                'description' => 'Your OneSignal REST API key',
                'is_public' => false,
            ],
            'email_notifications_enabled' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'Enable Email Notifications',
                'description' => 'Enable email notifications for users',
                'is_public' => false,
            ],
            'push_notifications_enabled' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'notification',
                'label' => 'Enable Push Notifications',
                'description' => 'Enable push notifications for users',
                'is_public' => false,
            ],

            // Performance Settings
            'cache_enabled' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'performance',
                'label' => 'Enable Caching',
                'description' => 'Enable application caching for better performance',
                'is_public' => false,
            ],
            'cache_ttl' => [
                'value' => 3600,
                'type' => 'integer',
                'group' => 'performance',
                'label' => 'Cache TTL (seconds)',
                'description' => 'Cache time to live in seconds',
                'is_public' => false,
            ],
            'image_optimization' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'performance',
                'label' => 'Enable Image Optimization',
                'description' => 'Enable automatic image optimization',
                'is_public' => false,
            ],
            'lazy_loading' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'performance',
                'label' => 'Enable Lazy Loading',
                'description' => 'Enable lazy loading for images',
                'is_public' => false,
            ],
            'minify_css' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'performance',
                'label' => 'Minify CSS',
                'description' => 'Minify CSS files in production',
                'is_public' => false,
            ],
            'minify_js' => [
                'value' => true,
                'type' => 'boolean',
                'group' => 'performance',
                'label' => 'Minify JavaScript',
                'description' => 'Minify JavaScript files in production',
                'is_public' => false,
            ],
        ];

        $created = 0;
        $errors = [];

        foreach ($defaults as $key => $options) {
            try {
                if (!static::where('key', $key)->exists()) {
                    static::create(array_merge(['key' => $key], $options));
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to create setting '{$key}': " . $e->getMessage();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
            'success' => empty($errors)
        ];
    }

    public static function clearCache()
    {
        $settings = static::all();
        
        foreach ($settings as $setting) {
            Cache::forget("setting.{$setting->key}");
        }
        
        return true;
    }

    public static function exportSettings()
    {
        $settings = static::all();
        $export = [];
        
        foreach ($settings as $setting) {
            $export[$setting->key] = [
                'value' => $setting->value,
                'type' => $setting->type,
                'group' => $setting->group,
                'label' => $setting->label,
                'description' => $setting->description,
                'options' => $setting->options,
                'validation' => $setting->validation,
                'is_public' => $setting->is_public,
                'is_required' => $setting->is_required,
                'sort_order' => $setting->sort_order,
            ];
        }
        
        return $export;
    }

    public static function importSettings($data)
    {
        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($data as $key => $settingData) {
            try {
                $setting = static::where('key', $key)->first();
                
                if ($setting) {
                    $setting->update($settingData);
                    $updated++;
                } else {
                    static::create(array_merge(['key' => $key], $settingData));
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to import setting '{$key}': " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
            'success' => empty($errors)
        ];
    }
}