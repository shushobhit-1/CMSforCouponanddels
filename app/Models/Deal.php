<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Deal extends Model
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'store_id',
        'category_id',
        'deal_type', // percentage, fixed, free_shipping, buy_one_get_one
        'discount_value',
        'original_price',
        'deal_price',
        'currency',
        'start_date',
        'end_date',
        'is_featured',
        'is_popular',
        'is_active',
        'affiliate_link',
        'tracking_id',
        'commission_rate',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'used_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'created_by',
        'status', // active, inactive, expired, scheduled
        'priority',
        'tags',
        'conditions',
        'exclusions',
        'popup_settings', // JSON for popup customization
        'button_text',
        'button_color',
        'button_hover_effect'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'original_price' => 'decimal:2',
        'deal_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'priority' => 'integer',
        'tags' => 'array',
        'conditions' => 'array',
        'exclusions' => 'array',
        'popup_settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
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

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    public function clicks()
    {
        return $this->hasMany(DealClick::class);
    }

    public function conversions()
    {
        return $this->hasMany(DealConversion::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'active')
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

    public function scopeExpiring($query, $days = 7)
    {
        return $query->where('end_date', '<=', now()->addDays($days))
                    ->where('end_date', '>=', now());
    }

    public function scopeNewArrivals($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('deal_price', [$min, $max]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getExpiredAttribute()
    {
        return $this->end_date < now();
    }

    public function getExpiresInDaysAttribute()
    {
        if ($this->expired) return 0;
        return now()->diffInDays($this->end_date, false);
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price <= 0) return 0;
        return round((($this->original_price - $this->deal_price) / $this->original_price) * 100);
    }

    public function getSavingsAmountAttribute()
    {
        return $this->original_price - $this->deal_price;
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
        return $this->getFirstMediaUrl('deals', 'medium') ?: asset('images/default-deal.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('deals', 'thumbnail') ?: asset('images/default-deal-thumb.jpg');
    }

    public function getStatusTextAttribute()
    {
        if ($this->expired) return 'Expired';
        if ($this->start_date > now()) return 'Upcoming';
        return 'Active';
    }

    public function getButtonTextAttribute($value)
    {
        return $value ?: 'Get Deal';
    }

    // Methods
    public function trackClick($userId = null, $ip = null, $userAgent = null)
    {
        return $this->clicks()->create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'clicked_at' => now()
        ]);
    }

    public function trackConversion($userId = null, $orderId = null, $amount = null, $commission = null)
    {
        return $this->conversions()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'amount' => $amount,
            'commission' => $commission,
            'converted_at' => now()
        ]);
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function isAvailable()
    {
        return $this->is_active && 
               $this->status === 'active' && 
               !$this->expired && 
               ($this->usage_limit === null || $this->used_count < $this->usage_limit);
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
        $this->addMediaCollection('deals')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('banners')
            ->useDisk('public');
    }

    // SEO Methods
    public function getSeoTitle()
    {
        return $this->meta_title ?: $this->title;
    }

    public function getSeoDescription()
    {
        return $this->meta_description ?: Str::limit($this->description, 160);
    }

    public function getSeoKeywords()
    {
        return $this->meta_keywords ?: implode(', ', $this->tags ?? []);
    }

    public function getOgImage()
    {
        return $this->og_image ?: $this->image_url;
    }

    public function getTwitterImage()
    {
        return $this->twitter_image ?: $this->image_url;
    }
}