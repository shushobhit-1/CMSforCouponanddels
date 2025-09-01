<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
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
        'unlimited_stock',
        'min_order_quantity',
        'max_order_quantity',
        'weight',
        'dimensions',
        'color',
        'size',
        'material',
        'warranty',
        'shipping_info',
        'return_policy',
        'affiliate_link',
        'affiliate_network',
        'commission_rate',
        'commission_type', // percentage, fixed
        'tracking_id',
        'status', // active, inactive, featured, popular
        'featured',
        'popular',
        'trending',
        'rating',
        'review_count',
        'view_count',
        'click_count',
        'conversion_count',
        'favorite_count',
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
        'button_text',
        'button_color',
        'button_hover_effect',
        'created_by',
        'updated_by',
        'published_at',
        'featured_until',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured_until' => 'datetime',
        'featured' => 'boolean',
        'popular' => 'boolean',
        'trending' => 'boolean',
        'unlimited_stock' => 'boolean',
        'popup_enabled' => 'boolean',
        'original_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'weight' => 'decimal:2',
        'rating' => 'decimal:1',
        'stock_quantity' => 'integer',
        'min_order_quantity' => 'integer',
        'max_order_quantity' => 'integer',
        'review_count' => 'integer',
        'view_count' => 'integer',
        'click_count' => 'integer',
        'conversion_count' => 'integer',
        'favorite_count' => 'integer',
        'popup_delay' => 'integer',
        'dimensions' => 'array',
        'color' => 'array',
        'size' => 'array',
    ];

    protected $dates = [
        'published_at',
        'featured_until',
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

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true)
                    ->where(function ($q) {
                        $q->whereNull('featured_until')
                          ->orWhere('featured_until', '>', now());
                    });
    }

    public function scopePopular($query)
    {
        return $query->where('popular', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('trending', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('availability', 'in_stock');
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('current_price', [$min, $max]);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsInStockAttribute()
    {
        return $this->availability === 'in_stock' && 
               ($this->unlimited_stock || $this->stock_quantity > 0);
    }

    public function getIsFeaturedAttribute()
    {
        return $this->featured && 
               (!$this->featured_until || $this->featured_until > now());
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->original_price > 0 && $this->current_price < $this->original_price) {
            return round((($this->original_price - $this->current_price) / $this->original_price) * 100, 1);
        }
        return 0;
    }

    public function getSavingsAmountAttribute()
    {
        if ($this->original_price > 0 && $this->current_price < $this->original_price) {
            return $this->original_price - $this->current_price;
        }
        return 0;
    }

    public function getDiscountTextAttribute()
    {
        if ($this->discount_percentage > 0) {
            return "Save {$this->discount_percentage}%";
        }
        return null;
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
        return $this->getFirstMediaUrl('products', 'thumb') ?: asset('images/default-product.jpg');
    }

    public function getGalleryUrlsAttribute()
    {
        return $this->getMedia('gallery')->map(function ($media) {
            return $media->getUrl();
        });
    }

    public function getFormattedPriceAttribute()
    {
        return "₹{$this->current_price}";
    }

    public function getFormattedOriginalPriceAttribute()
    {
        return "₹{$this->original_price}";
    }

    public function getFormattedWeightAttribute()
    {
        if ($this->weight) {
            return "{$this->weight} kg";
        }
        return null;
    }

    public function getFormattedDimensionsAttribute()
    {
        if ($this->dimensions && is_array($this->dimensions)) {
            $dims = $this->dimensions;
            if (isset($dims['length']) && isset($dims['width']) && isset($dims['height'])) {
                return "{$dims['length']} × {$dims['width']} × {$dims['height']} cm";
            }
        }
        return null;
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
            'empty' => $emptyStars,
        ];
    }

    public function getFormattedRatingAttribute()
    {
        if ($this->rating) {
            return number_format($this->rating, 1);
        }
        return 'No ratings';
    }

    public function getButtonTextAttribute($value)
    {
        return $value ?: 'Check Product';
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

    public function incrementFavoriteCount()
    {
        $this->increment('favorite_count');
    }

    public function decrementFavoriteCount()
    {
        $this->decrement('favorite_count');
    }

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $this->update(['rating' => round($avgRating, 1)]);
    }

    public function isAvailableForOrder($quantity = 1)
    {
        if ($this->availability !== 'in_stock') {
            return false;
        }
        
        if ($this->unlimited_stock) {
            return $quantity >= $this->min_order_quantity && 
                   ($this->max_order_quantity === 0 || $quantity <= $this->max_order_quantity);
        }
        
        return $this->stock_quantity >= $quantity && 
               $quantity >= $this->min_order_quantity && 
               ($this->max_order_quantity === 0 || $quantity <= $this->max_order_quantity);
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
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('gallery')
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

    public function getRelatedProducts($limit = 4)
    {
        return $this->relatedProducts()
                    ->where('status', 'active')
                    ->limit($limit)
                    ->get();
    }

    public function getSimilarProducts($limit = 4)
    {
        return static::where('category_id', $this->category_id)
                    ->where('id', '!=', $this->id)
                    ->where('status', 'active')
                    ->where('store_id', $this->store_id)
                    ->limit($limit)
                    ->get();
    }
}