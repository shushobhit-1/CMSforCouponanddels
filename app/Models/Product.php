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
        'product_type', // physical, digital, service
        'brand',
        'model',
        'sku',
        'original_price',
        'sale_price',
        'currency',
        'affiliate_link',
        'tracking_id',
        'commission_rate',
        'commission_amount',
        'click_count',
        'conversion_count',
        'revenue',
        'rating',
        'review_count',
        'is_featured',
        'is_popular',
        'is_active',
        'is_verified',
        'in_stock',
        'stock_quantity',
        'weight',
        'dimensions',
        'color_options',
        'size_options',
        'tags',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'created_by',
        'status',
        'sort_order',
        'product_code',
        'warranty',
        'shipping_info',
        'return_policy',
        'product_popup_settings',
        'button_text',
        'button_color',
        'button_hover_effect',
        'affiliate_network',
        'network_product_id',
        'network_category',
        'network_rating',
        'network_review_count'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'in_stock' => 'boolean',
        'color_options' => 'array',
        'size_options' => 'array',
        'tags' => 'array',
        'product_popup_settings' => 'array',
        'original_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'revenue' => 'decimal:2',
        'rating' => 'decimal:1',
        'weight' => 'decimal:2',
        'dimensions' => 'array'
    ];

    protected $dates = [
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

    public function affiliateNetwork()
    {
        return $this->belongsTo(Affiliate::class, 'affiliate_network');
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

    public function scopeInStock($query)
    {
        return $query->where('in_stock', true);
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
        return $query->whereBetween('sale_price', [$min, $max]);
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
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    public function scopeByTags($query, $tags)
    {
        if (is_array($tags)) {
            return $query->whereJsonContains('tags', $tags);
        }
        return $query->whereJsonContains('tags', [$tags]);
    }

    // Accessors
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?: $this->original_price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price > 0 && $this->sale_price > 0) {
            return round((($this->original_price - $this->sale_price) / $this->original_price) * 100, 2);
        }
        return 0;
    }

    public function getDiscountAmountAttribute()
    {
        if ($this->original_price > 0 && $this->sale_price > 0) {
            return $this->original_price - $this->sale_price;
        }
        return 0;
    }

    public function getHasDiscountAttribute()
    {
        return $this->sale_price > 0 && $this->sale_price < $this->original_price;
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
        return $this->getFirstMediaUrl('product_images', 'medium') ?: asset('images/default-product.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('product_images', 'thumbnail') ?: asset('images/default-product-thumb.jpg');
    }

    public function getGalleryImagesAttribute()
    {
        return $this->getMedia('product_images')->map(function($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumbnail' => $media->getUrl('thumbnail'),
                'alt' => $media->name
            ];
        });
    }

    public function getRatingStarsAttribute()
    {
        $rating = $this->rating ?: 0;
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
        return number_format($this->rating ?: 0, 1);
    }

    public function getFormattedPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->current_price, 2);
    }

    public function getFormattedOriginalPriceAttribute()
    {
        return $this->currency . ' ' . number_format($this->original_price, 2);
    }

    public function getFormattedSalePriceAttribute()
    {
        if ($this->sale_price) {
            return $this->currency . ' ' . number_format($this->sale_price, 2);
        }
        return null;
    }

    public function getStockStatusAttribute()
    {
        if (!$this->in_stock) {
            return 'out_of_stock';
        }
        
        if ($this->stock_quantity === null) {
            return 'in_stock';
        }
        
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }
        
        if ($this->stock_quantity <= 10) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }

    public function getStockStatusTextAttribute()
    {
        switch ($this->stock_status) {
            case 'in_stock':
                return 'In Stock';
            case 'low_stock':
                return 'Low Stock';
            case 'out_of_stock':
                return 'Out of Stock';
            default:
                return 'Unknown';
        }
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

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $reviewCount = $this->reviews()->count();
        
        $this->update([
            'rating' => round($avgRating, 1),
            'review_count' => $reviewCount
        ]);
    }

    public function updatePopularity()
    {
        $score = ($this->click_count * 0.2) + ($this->conversion_count * 0.5) + ($this->rating * 10);
        
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
        $this->addMediaCollection('product_images')
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
            'canonical_url' => route('products.show', $this->slug)
        ];
    }

    public function getButtonText()
    {
        return $this->button_text ?: 'Check Product';
    }

    public function getButtonColor()
    {
        return $this->button_color ?: '#28a745';
    }

    public function getButtonHoverEffect()
    {
        return $this->button_hover_effect ?: 'scale';
    }

    public function getAffiliateUrl()
    {
        $url = $this->affiliate_link;
        
        // Add tracking parameters
        $params = [
            'utm_source' => 'couponcms',
            'utm_medium' => 'product',
            'utm_campaign' => $this->id,
            'tracking_id' => $this->tracking_id,
            'product_id' => $this->id
        ];

        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . http_build_query($params);
    }
}