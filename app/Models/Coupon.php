<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Carbon\Carbon;

class Coupon extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'code',
        'discount_type',
        'discount_value',
        'minimum_purchase',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'is_popular',
        'is_exclusive',
        'store_id',
        'category_id',
        'created_by',
        'updated_by',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'affiliate_link',
        'tracking_id',
        'click_count',
        'conversion_count',
        'revenue',
        'status',
        'tags',
        'conditions',
        'exclusions',
        'how_to_use',
        'terms_conditions',
        'popup_settings',
        'seo_settings'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_exclusive' => 'boolean',
        'tags' => 'array',
        'conditions' => 'array',
        'exclusions' => 'array',
        'popup_settings' => 'array',
        'seo_settings' => 'array'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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

    public function clicks()
    {
        return $this->hasMany(CouponClick::class);
    }

    public function conversions()
    {
        return $this->hasMany(CouponConversion::class);
    }

    public function reviews()
    {
        return $this->hasMany(CouponReview::class);
    }

    public function relatedCoupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_relations', 'coupon_id', 'related_coupon_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeExclusive($query)
    {
        return $query->where('is_exclusive', true);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
            ->where('end_date', '>=', now());
    }

    public function scopeNewArrivals($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors & Mutators
    public function getIsExpiredAttribute()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function getIsActiveAttribute($value)
    {
        if (!$value) return false;
        if ($this->isExpired) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function getDiscountTextAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '% OFF';
        }
        return '$' . number_format($this->discount_value, 2) . ' OFF';
    }

    public function getRemainingDaysAttribute()
    {
        if (!$this->end_date) return null;
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getUsagePercentageAttribute()
    {
        if (!$this->usage_limit) return 0;
        return round(($this->used_count / $this->usage_limit) * 100, 2);
    }

    public function getStoreLogoAttribute()
    {
        return $this->store ? $this->store->logo_url : null;
    }

    public function getStoreNameAttribute()
    {
        return $this->store ? $this->store->name : 'Unknown Store';
    }

    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : 'Uncategorized';
    }

    public function getImageUrlAttribute()
    {
        if ($this->hasMedia('coupon_images')) {
            return $this->getFirstMediaUrl('coupon_images');
        }
        return $this->store ? $this->store->logo_url : asset('images/default-coupon.png');
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

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function canBeUsed()
    {
        if (!$this->is_active) return false;
        if ($this->isExpired) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        return true;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('coupon_images')
            ->singleFile()
            ->useDisk('public');
    }

    public function getSeoTitle()
    {
        return $this->meta_title ?: $this->title . ' - ' . $this->store_name;
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
        
        $keywords = [$this->title, $this->store_name, $this->category_name];
        if ($this->tags) {
            $keywords = array_merge($keywords, $this->tags);
        }
        
        return implode(', ', array_unique($keywords));
    }
}