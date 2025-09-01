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
        'price',
        'sale_price',
        'currency',
        'availability', // in_stock, out_of_stock, pre_order, discontinued
        'stock_quantity',
        'weight',
        'dimensions',
        'color',
        'size',
        'material',
        'warranty',
        'rating',
        'review_count',
        'is_featured',
        'is_popular',
        'is_active',
        'affiliate_link',
        'tracking_id',
        'commission_rate',
        'commission_type', // percentage, fixed
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'created_by',
        'status', // active, inactive, draft
        'priority',
        'tags',
        'specifications', // JSON
        'features', // JSON
        'button_text',
        'button_color',
        'button_hover_effect',
        'seo_score',
        'page_speed_score'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'rating' => 'decimal:1',
        'review_count' => 'integer',
        'stock_quantity' => 'integer',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'priority' => 'integer',
        'tags' => 'array',
        'specifications' => 'array',
        'features' => 'array',
        'seo_score' => 'integer',
        'page_speed_score' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
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

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_id');
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
        return $query->whereBetween('price', [$min, $max]);
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('brand', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsOnSaleAttribute()
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_on_sale || $this->price <= 0) return 0;
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getSavingsAmountAttribute()
    {
        if (!$this->is_on_sale) return 0;
        return $this->price - $this->sale_price;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->is_on_sale ? $this->sale_price : $this->price;
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
        return $this->getFirstMediaUrl('products', 'medium') ?: asset('images/default-product.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('products', 'thumbnail') ?: asset('images/default-product-thumb.jpg');
    }

    public function getGalleryImagesAttribute()
    {
        return $this->getMedia('gallery')->map(function($media) {
            return $media->getUrl();
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

    public function getButtonTextAttribute($value)
    {
        return $value ?: 'Check Product';
    }

    public function getAvailabilityTextAttribute()
    {
        return match($this->availability) {
            'in_stock' => 'In Stock',
            'out_of_stock' => 'Out of Stock',
            'pre_order' => 'Pre-order',
            'discontinued' => 'Discontinued',
            default => 'Unknown'
        };
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

    public function updateRating()
    {
        $avgRating = $this->reviews()->avg('rating');
        $reviewCount = $this->reviews()->count();
        
        $this->update([
            'rating' => $avgRating ?: 0,
            'review_count' => $reviewCount
        ]);
    }

    public function isAvailable()
    {
        return $this->is_active && 
               $this->status === 'active' && 
               $this->availability === 'in_stock';
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

    // Performance Methods
    public function calculateSeoScore()
    {
        $score = 0;
        
        if ($this->meta_title) $score += 20;
        if ($this->meta_description) $score += 20;
        if ($this->meta_keywords) $score += 15;
        if ($this->description && strlen($this->description) > 100) $score += 15;
        if ($this->tags && count($this->tags) > 0) $score += 10;
        if ($this->specifications && count($this->specifications) > 0) $score += 10;
        if ($this->features && count($this->features) > 0) $score += 10;
        
        $this->update(['seo_score' => $score]);
        return $score;
    }
}