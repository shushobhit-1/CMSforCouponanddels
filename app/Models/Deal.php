<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Deal extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'store_id',
        'category_id',
        'deal_type', // percentage, fixed, free_shipping, etc.
        'original_price',
        'deal_price',
        'discount_percentage',
        'discount_amount',
        'currency',
        'start_date',
        'end_date',
        'status', // active, inactive, expired, featured
        'featured',
        'popular',
        'affiliate_link',
        'affiliate_network',
        'commission_rate',
        'commission_type', // percentage, fixed
        'terms_conditions',
        'restrictions',
        'stock_quantity',
        'unlimited_stock',
        'min_purchase',
        'max_discount',
        'usage_limit',
        'used_count',
        'view_count',
        'click_count',
        'conversion_count',
        'rating',
        'review_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'popup_enabled',
        'popup_delay',
        'popup_animation',
        'popup_position',
        'popup_style',
        'created_by',
        'updated_by',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'featured' => 'boolean',
        'popular' => 'boolean',
        'unlimited_stock' => 'boolean',
        'popup_enabled' => 'boolean',
        'original_price' => 'decimal:2',
        'deal_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'rating' => 'decimal:1',
        'stock_quantity' => 'integer',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'view_count' => 'integer',
        'click_count' => 'integer',
        'conversion_count' => 'integer',
        'review_count' => 'integer',
        'popup_delay' => 'integer',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'published_at',
        'expires_at',
        'deleted_at',
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

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
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
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('popular', true);
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

    public function scopeByDiscount($query, $minDiscount)
    {
        return $query->where('discount_percentage', '>=', $minDiscount);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    public function getIsExpiredAttribute()
    {
        return $this->end_date < now();
    }

    public function getIsUpcomingAttribute()
    {
        return $this->start_date > now();
    }

    public function getDiscountTextAttribute()
    {
        if ($this->deal_type === 'percentage') {
            return "{$this->discount_percentage}% OFF";
        } elseif ($this->deal_type === 'fixed') {
            return "₹{$this->discount_amount} OFF";
        } elseif ($this->deal_type === 'free_shipping') {
            return "Free Shipping";
        }
        return "Special Deal";
    }

    public function getRemainingDaysAttribute()
    {
        if ($this->end_date) {
            return max(0, now()->diffInDays($this->end_date, false));
        }
        return null;
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->usage_limit > 0) {
            return round(($this->used_count / $this->usage_limit) * 100, 1);
        }
        return 0;
    }

    public function getStoreNameAttribute()
    {
        return $this->store->name ?? 'Unknown Store';
    }

    public function getCategoryNameAttribute()
    {
        return $this->category->name ?? 'Uncategorized';
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('deals', 'thumb') ?: asset('images/default-deal.jpg');
    }

    public function getBannerUrlAttribute()
    {
        return $this->getFirstMediaUrl('deals', 'banner') ?: asset('images/default-deal-banner.jpg');
    }

    public function getFormattedPriceAttribute()
    {
        return "₹{$this->deal_price}";
    }

    public function getFormattedOriginalPriceAttribute()
    {
        return "₹{$this->original_price}";
    }

    public function getFormattedDiscountAttribute()
    {
        if ($this->deal_type === 'percentage') {
            return "Save {$this->discount_percentage}%";
        } elseif ($this->deal_type === 'fixed') {
            return "Save ₹{$this->discount_amount}";
        }
        return "Special Offer";
    }

    // Methods
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementClickCount()
    {
        $this->increment('click_count');
    }

    public function incrementConversionCount()
    {
        $this->increment('conversion_count');
    }

    public function incrementUsedCount()
    {
        if (!$this->unlimited_stock && $this->stock_quantity > 0) {
            $this->decrement('stock_quantity');
        }
        $this->increment('used_count');
    }

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $this->update(['rating' => round($avgRating, 1)]);
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
            ->singleFile()
            ->useDisk('public');
    }

    public function getSeoData()
    {
        return [
            'title' => $this->meta_title ?: $this->title,
            'description' => $this->meta_description ?: $this->short_description,
            'keywords' => $this->meta_keywords,
            'og_image' => $this->og_image ?: $this->image_url,
            'twitter_image' => $this->twitter_image ?: $this->image_url,
        ];
    }

    public function isAvailable()
    {
        return $this->is_active && 
               ($this->unlimited_stock || $this->stock_quantity > 0) &&
               ($this->usage_limit === 0 || $this->used_count < $this->usage_limit);
    }

    public function getNextOpeningTime()
    {
        if ($this->store) {
            return $this->store->getNextOpeningTime();
        }
        return null;
    }
}