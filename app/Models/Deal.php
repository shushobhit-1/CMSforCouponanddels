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
        'deal_type', // percentage, fixed, free_shipping, etc.
        'original_price',
        'deal_price',
        'discount_percentage',
        'discount_amount',
        'currency',
        'affiliate_link',
        'tracking_id',
        'start_date',
        'end_date',
        'is_featured',
        'is_popular',
        'is_active',
        'is_verified',
        'click_count',
        'conversion_count',
        'revenue',
        'commission_rate',
        'commission_amount',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'created_by',
        'status',
        'sort_order',
        'deal_code',
        'terms_conditions',
        'restrictions',
        'minimum_purchase',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'deal_popup_settings',
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
        'is_verified' => 'boolean',
        'deal_popup_settings' => 'array',
        'original_price' => 'decimal:2',
        'deal_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'revenue' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2'
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
        return $this->morphMany(Review::class, 'reviewable');
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
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('deal_code', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getExpirationStatusAttribute()
    {
        if ($this->end_date < now()) {
            return 'expired';
        } elseif ($this->end_date < now()->addDays(7)) {
            return 'expiring_soon';
        } else {
            return 'active';
        }
    }

    public function getDiscountTextAttribute()
    {
        if ($this->deal_type === 'percentage') {
            return "Save {$this->discount_percentage}%";
        } elseif ($this->deal_type === 'fixed') {
            return "Save {$this->currency} {$this->discount_amount}";
        } elseif ($this->deal_type === 'free_shipping') {
            return 'Free Shipping';
        } else {
            return 'Special Deal';
        }
    }

    public function getRemainingDaysAttribute()
    {
        if ($this->end_date < now()) {
            return 0;
        }
        return now()->diffInDays($this->end_date, false);
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->usage_limit === 0) {
            return 0;
        }
        return round(($this->used_count / $this->usage_limit) * 100, 2);
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
        return $this->getFirstMediaUrl('deal_images', 'medium') ?: asset('images/default-deal.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('deal_images', 'thumbnail') ?: asset('images/default-deal-thumb.jpg');
    }

    public function getIsExpiredAttribute()
    {
        return $this->end_date < now();
    }

    public function getIsActiveAttribute()
    {
        return $this->is_active && !$this->is_expired;
    }

    // Methods
    public function trackClick($userId = null, $ip = null, $userAgent = null)
    {
        $this->increment('click_count');
        
        $this->clicks()->create([
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'clicked_at' => now()
        ]);
    }

    public function trackConversion($userId = null, $orderId = null, $amount = null, $commission = null)
    {
        $this->increment('conversion_count');
        
        if ($amount) {
            $this->increment('revenue', $amount);
        }
        
        if ($commission) {
            $this->increment('commission_amount', $commission);
        }
        
        $this->conversions()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'amount' => $amount,
            'commission' => $commission,
            'converted_at' => now()
        ]);
    }

    public function updatePopularity()
    {
        $score = ($this->click_count * 0.3) + ($this->conversion_count * 0.7);
        
        if ($score > 100) {
            $this->update(['is_popular' => true]);
        } else {
            $this->update(['is_popular' => false]);
        }
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('deal_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
    }

    public function getSeoData()
    {
        return [
            'title' => $this->meta_title ?: $this->title,
            'description' => $this->meta_description ?: $this->short_description,
            'keywords' => $this->meta_keywords,
            'og_image' => $this->og_image ?: $this->image_url,
            'twitter_image' => $this->twitter_image ?: $this->image_url,
            'canonical_url' => route('deals.show', $this->slug)
        ];
    }

    public function getButtonText()
    {
        return $this->button_text ?: 'Get Deal';
    }

    public function getButtonColor()
    {
        return $this->button_color ?: '#007bff';
    }

    public function getButtonHoverEffect()
    {
        return $this->button_hover_effect ?: 'scale';
    }
}