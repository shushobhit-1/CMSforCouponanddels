<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'store_id',
        'category_id',
        'brand',
        'model',
        'sku',
        'original_price',
        'current_price',
        'currency',
        'availability', // in_stock, out_of_stock, pre_order, discontinued
        'stock_quantity',
        'low_stock_threshold',
        'affiliate_link',
        'tracking_id',
        'commission_rate',
        'commission_type', // percentage, fixed
        'commission_value',
        'is_featured',
        'is_popular',
        'is_active',
        'is_verified',
        'rating',
        'review_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'created_by',
        'status', // active, inactive, pending, rejected
        'priority',
        'tags',
        'specifications', // JSON for product specs
        'features', // JSON for product features
        'button_text',
        'button_color',
        'button_hover_effect',
        'product_url',
        'affiliate_network', // vcommission, cuelinks, optimisemedia, inrdeals, amazon, flipkart
        'network_product_id',
        'last_sync_at'
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'rating' => 'decimal:1',
        'review_count' => 'integer',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'priority' => 'integer',
        'tags' => 'array',
        'specifications' => 'array',
        'features' => 'array',
        'last_sync_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'last_sync_at',
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
        return $this->hasMany(ProductClick::class);
    }

    public function conversions()
    {
        return $this->hasMany(ProductConversion::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function images()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('availability', 'in_stock');
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('current_price', [$min, $max]);
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeByAffiliateNetwork($query, $network)
    {
        return $query->where('affiliate_network', $network);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price <= 0) return 0;
        return round((($this->original_price - $this->current_price) / $this->original_price) * 100);
    }

    public function getSavingsAmountAttribute()
    {
        return $this->original_price - $this->current_price;
    }

    public function getStoreNameAttribute()
    {
        return $this->store ? $this->store->name : 'Unknown Store';
    }

    public function getCategoryNameAttribute()
    {
        return $this->category ? $this->category->name : 'Uncategorized';
    }

    public function getMainImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('products', 'medium') ?: asset('images/default-product.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('products', 'thumbnail') ?: asset('images/default-product-thumb.jpg');
    }

    public function getGalleryImagesAttribute()
    {
        return $this->getMedia('products')->map(function($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->getUrl('thumbnail'),
                'alt' => $media->name
            ];
        });
    }

    public function getStatusTextAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getAvailabilityTextAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->availability));
    }

    public function getButtonTextAttribute($value)
    {
        return $value ?: 'Check Product';
    }

    public function getCommissionTextAttribute()
    {
        if ($this->commission_type === 'percentage') {
            return "{$this->commission_rate}% Commission";
        }
        return "{$this->currency} {$this->commission_value} Commission";
    }

    public function getStockStatusAttribute()
    {
        if ($this->availability === 'out_of_stock') return 'Out of Stock';
        if ($this->availability === 'discontinued') return 'Discontinued';
        if ($this->stock_quantity <= $this->low_stock_threshold) return 'Low Stock';
        return 'In Stock';
    }

    public function getStockStatusColorAttribute()
    {
        if ($this->availability === 'out_of_stock') return 'danger';
        if ($this->availability === 'discontinued') return 'secondary';
        if ($this->stock_quantity <= $this->low_stock_threshold) return 'warning';
        return 'success';
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

    public function updateRating($newRating)
    {
        $totalRating = ($this->rating * $this->review_count) + $newRating;
        $this->review_count++;
        $this->rating = round($totalRating / $this->review_count, 1);
        $this->save();
    }

    public function isAvailable()
    {
        return $this->is_active && 
               $this->status === 'active' && 
               $this->availability === 'in_stock';
    }

    public function isLowStock()
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
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
        $this->addMediaCollection('products')
            ->useDisk('public');

        $this->addMediaCollection('gallery')
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
        return $this->og_image ?: $this->main_image_url;
    }

    public function getTwitterImage()
    {
        return $this->twitter_image ?: $this->main_image_url;
    }

    // Affiliate Network Methods
    public function syncWithAffiliateNetwork()
    {
        // This method would integrate with the specific affiliate network API
        // to sync product data, pricing, availability, etc.
        $this->update(['last_sync_at' => now()]);
    }

    public function getAffiliateLink($userId = null)
    {
        $baseLink = $this->affiliate_link;
        
        if ($userId) {
            $baseLink .= (strpos($baseLink, '?') !== false ? '&' : '?') . "ref={$userId}";
        }
        
        if ($this->tracking_id) {
            $baseLink .= (strpos($baseLink, '?') !== false ? '&' : '?') . "tracking={$this->tracking_id}";
        }
        
        return $baseLink;
    }
}