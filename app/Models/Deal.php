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

class Deal extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'original_price',
        'discounted_price',
        'discount_percentage',
        'discount_amount',
        'currency',
        'deal_type',
        'deal_category',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'is_popular',
        'is_exclusive',
        'is_flash_sale',
        'flash_sale_end',
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
        'deal_settings',
        'seo_settings'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'flash_sale_end' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_exclusive' => 'boolean',
        'is_flash_sale' => 'boolean',
        'original_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tags' => 'array',
        'conditions' => 'array',
        'exclusions' => 'array',
        'deal_settings' => 'array',
        'seo_settings' => 'array'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'flash_sale_end',
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
        return $this->hasMany(DealClick::class);
    }

    public function conversions()
    {
        return $this->hasMany(DealConversion::class);
    }

    public function reviews()
    {
        return $this->hasMany(DealReview::class);
    }

    public function relatedDeals()
    {
        return $this->belongsToMany(Deal::class, 'deal_relations', 'deal_id', 'related_deal_id');
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

    public function scopeFlashSale($query)
    {
        return $query->where('is_flash_sale', true)
            ->where('flash_sale_end', '>', now());
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

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('discounted_price', [$minPrice, $maxPrice]);
    }

    public function scopeByDiscountPercentage($query, $minPercentage)
    {
        return $query->where('discount_percentage', '>=', $minPercentage);
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
        return true;
    }

    public function getDiscountTextAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->discount_percentage . '% OFF';
        }
        if ($this->discount_amount > 0) {
            return '$' . number_format($this->discount_amount, 2) . ' OFF';
        }
        return 'Special Deal';
    }

    public function getRemainingDaysAttribute()
    {
        if (!$this->end_date) return null;
        return max(0, now()->diffInDays($this->end_date, false));
    }

    public function getFlashSaleRemainingTimeAttribute()
    {
        if (!$this->is_flash_sale || !$this->flash_sale_end) return null;
        return now()->diff($this->flash_sale_end);
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
        if ($this->hasMedia('deal_images')) {
            return $this->getFirstMediaUrl('deal_images');
        }
        return $this->store ? $this->store->logo_url : asset('images/default-deal.png');
    }

    public function getFormattedOriginalPriceAttribute()
    {
        return '$' . number_format($this->original_price, 2);
    }

    public function getFormattedDiscountedPriceAttribute()
    {
        return '$' . number_format($this->discountedPrice, 2);
    }

    public function getDiscountedPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->original_price - ($this->original_price * $this->discount_percentage / 100);
        }
        if ($this->discount_amount > 0) {
            return $this->original_price - $this->discount_amount;
        }
        return $this->original_price;
    }

    public function getSavingsAmountAttribute()
    {
        return $this->original_price - $this->discounted_price;
    }

    public function getSavingsPercentageAttribute()
    {
        if ($this->original_price > 0) {
            return round((($this->original_price - $this->discounted_price) / $this->original_price) * 100, 2);
        }
        return 0;
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

    public function canBeUsed()
    {
        if (!$this->is_active) return false;
        if ($this->isExpired) return false;
        return true;
    }

    public function isFlashSaleActive()
    {
        return $this->is_flash_sale && $this->flash_sale_end && $this->flash_sale_end->isFuture();
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
        $this->addMediaCollection('deal_images')
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
        
        $keywords = [$this->title, $this->store_name, $this->category_name, 'deal', 'discount'];
        if ($this->tags) {
            $keywords = array_merge($keywords, $this->tags);
        }
        
        return implode(', ', array_unique($keywords));
    }

    public function getAffiliateUrl()
    {
        $url = $this->affiliate_link;
        
        // Add tracking parameters
        $params = [
            'utm_source' => 'couponcms',
            'utm_medium' => 'deal',
            'utm_campaign' => $this->id,
            'tracking_id' => $this->tracking_id,
            'deal_id' => $this->id
        ];

        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . http_build_query($params);
    }
}