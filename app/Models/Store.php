<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Store extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'logo',
        'banner',
        'website_url',
        'affiliate_url',
        'tracking_id',
        'commission_rate',
        'commission_type',
        'minimum_payout',
        'payout_schedule',
        'contact_email',
        'contact_phone',
        'contact_address',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'social_youtube',
        'social_linkedin',
        'social_tiktok',
        'social_pinterest',
        'is_active',
        'is_featured',
        'is_popular',
        'is_verified',
        'rating',
        'review_count',
        'click_count',
        'conversion_count',
        'revenue',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'seo_settings',
        'affiliate_settings',
        'store_settings',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_verified' => 'boolean',
        'rating' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'minimum_payout' => 'decimal:2',
        'affiliate_settings' => 'array',
        'store_settings' => 'array',
        'seo_settings' => 'array'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'store_categories');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorable');
    }

    public function reviews()
    {
        return $this->hasMany(StoreReview::class);
    }

    public function clicks()
    {
        return $this->hasMany(StoreClick::class);
    }

    public function conversions()
    {
        return $this->hasMany(StoreConversion::class);
    }

    public function storeHours()
    {
        return $this->hasMany(StoreHour::class);
    }

    public function storeLocations()
    {
        return $this->hasMany(StoreLocation::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRating($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeByCommission($query, $minCommission = 0)
    {
        return $query->where('commission_rate', '>=', $minCommission);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    // Accessors & Mutators
    public function getLogoUrlAttribute()
    {
        if ($this->hasMedia('store_logos')) {
            return $this->getFirstMediaUrl('store_logos');
        }
        return asset('images/default-store-logo.png');
    }

    public function getBannerUrlAttribute()
    {
        if ($this->hasMedia('store_banners')) {
            return $this->getFirstMediaUrl('store_banners');
        }
        return asset('images/default-store-banner.png');
    }

    public function getFullAddressAttribute()
    {
        return $this->contact_address;
    }

    public function getSocialLinksAttribute()
    {
        return [
            'facebook' => $this->social_facebook,
            'twitter' => $this->social_twitter,
            'instagram' => $this->social_instagram,
            'youtube' => $this->social_youtube,
            'linkedin' => $this->social_linkedin,
            'tiktok' => $this->social_tiktok,
            'pinterest' => $this->social_pinterest
        ];
    }

    public function getActiveSocialLinksAttribute()
    {
        return array_filter($this->social_links);
    }

    public function getCommissionTextAttribute()
    {
        if ($this->commission_type === 'percentage') {
            return $this->commission_rate . '% Commission';
        }
        return '$' . number_format($this->commission_rate, 2) . ' Commission';
    }

    public function getRatingStarsAttribute()
    {
        $rating = $this->rating ?? 0;
        $fullStars = floor($rating);
        $halfStar = $rating - $fullStars >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
        
        return [
            'full' => $fullStars,
            'half' => $halfStar ? 1 : 0,
            'empty' => $emptyStars
        ];
    }

    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating ?? 0, 1);
    }

    public function getActiveCouponsCountAttribute()
    {
        return $this->coupons()->active()->count();
    }

    public function getActiveDealsCountAttribute()
    {
        return $this->deals()->active()->count();
    }

    public function getActiveProductsCountAttribute()
    {
        return $this->products()->active()->count();
    }

    // Methods
    public function incrementClick()
    {
        $this->increment('click_count');
    }

    public function incrementConversion()
    {
        $this->increment('conversion_count');
    }

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $this->update([
            'rating' => $avgRating,
            'review_count' => $this->reviews()->count()
        ]);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('store_logos')
            ->singleFile()
            ->useDisk('public');
            
        $this->addMediaCollection('store_banners')
            ->singleFile()
            ->useDisk('public');
    }

    public function getSeoTitle()
    {
        return $this->meta_title ?: $this->name . ' - Coupons & Deals';
    }

    public function getSeoDescription()
    {
        return $this->meta_description ?: substr(strip_tags($this->description), 0, 160);
    }

    public function getSeoKeywords()
    {
        if ($this->meta_keywords) {
            return $this->meta_keywords;
        }
        
        $keywords = [$this->name, 'coupons', 'deals', 'discounts', 'online shopping'];
        if ($this->categories->count() > 0) {
            $keywords = array_merge($keywords, $this->categories->pluck('name')->toArray());
        }
        
        return implode(', ', array_unique($keywords));
    }

    public function isOpen()
    {
        // Check if store is currently open based on store hours
        $now = now();
        $dayOfWeek = strtolower($now->format('l'));
        
        $todayHours = $this->storeHours()->where('day', $dayOfWeek)->first();
        
        if (!$todayHours || !$todayHours->is_open) {
            return false;
        }
        
        $currentTime = $now->format('H:i:s');
        return $currentTime >= $todayHours->open_time && $currentTime <= $todayHours->close_time;
    }

    public function getNextOpeningTime()
    {
        $now = now();
        $dayOfWeek = strtolower($now->format('l'));
        
        $todayHours = $this->storeHours()->where('day', $dayOfWeek)->first();
        
        if ($todayHours && $todayHours->is_open) {
            $currentTime = $now->format('H:i:s');
            if ($currentTime < $todayHours->open_time) {
                return $todayHours->open_time;
            }
        }
        
        // Find next open day
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $currentDayIndex = array_search($dayOfWeek, $days);
        
        for ($i = 1; $i <= 7; $i++) {
            $nextDayIndex = ($currentDayIndex + $i) % 7;
            $nextDay = $days[$nextDayIndex];
            
            $nextDayHours = $this->storeHours()->where('day', $nextDay)->first();
            if ($nextDayHours && $nextDayHours->is_open) {
                return [
                    'day' => ucfirst($nextDay),
                    'time' => $nextDayHours->open_time
                ];
            }
        }
        
        return null;
    }
}