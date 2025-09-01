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

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'price',
        'original_price',
        'discounted_price',
        'discount_percentage',
        'discount_amount',
        'currency',
        'product_type',
        'brand',
        'model',
        'sku',
        'upc',
        'ean',
        'isbn',
        'weight',
        'dimensions',
        'color',
        'size',
        'material',
        'availability',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'is_featured',
        'is_popular',
        'is_bestseller',
        'is_new_arrival',
        'is_on_sale',
        'sale_start_date',
        'sale_end_date',
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
        'specifications',
        'features',
        'reviews_count',
        'rating',
        'product_settings',
        'seo_settings'
    ];

    protected $casts = [
        'sale_start_date' => 'datetime',
        'sale_end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new_arrival' => 'boolean',
        'is_on_sale' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'weight' => 'decimal:2',
        'dimensions' => 'array',
        'specifications' => 'array',
        'features' => 'array',
        'tags' => 'array',
        'product_settings' => 'array',
        'seo_settings' => 'array',
        'rating' => 'decimal:2'
    ];

    protected $dates = [
        'sale_start_date',
        'sale_end_date',
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
        return $this->hasMany(ProductClick::class);
    }

    public function conversions()
    {
        return $this->hasMany(ProductConversion::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(Product::class, 'product_relations', 'product_id', 'related_product_id');
    }

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
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

    public function scopeBestseller($query)
    {
        return $query->where('is_bestseller', true);
    }

    public function scopeNewArrival($query)
    {
        return $query->where('is_new_arrival', true);
    }

    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true)
            ->where('sale_start_date', '<=', now())
            ->where('sale_end_date', '>=', now());
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

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeByAvailability($query, $availability)
    {
        return $query->where('availability', $availability);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->where('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    // Accessors & Mutators
    public function getIsOnSaleAttribute($value)
    {
        if (!$value) return false;
        if (!$this->sale_start_date || !$this->sale_end_date) return false;
        return now()->between($this->sale_start_date, $this->sale_end_date);
    }

    public function getCurrentPriceAttribute()
    {
        if ($this->is_on_sale && $this->discounted_price > 0) {
            return $this->discounted_price;
        }
        return $this->price;
    }

    public function getDiscountTextAttribute()
    {
        if ($this->discount_percentage > 0) {
            return $this->discount_percentage . '% OFF';
        }
        if ($this->discount_amount > 0) {
            return '$' . number_format($this->discount_amount, 2) . ' OFF';
        }
        return null;
    }

    public function getSavingsAmountAttribute()
    {
        if ($this->original_price > 0 && $this->current_price < $this->original_price) {
            return $this->original_price - $this->current_price;
        }
        return 0;
    }

    public function getSavingsPercentageAttribute()
    {
        if ($this->original_price > 0 && $this->current_price < $this->original_price) {
            return round((($this->original_price - $this->current_price) / $this->original_price) * 100, 2);
        }
        return 0;
    }

    public function getAvailabilityTextAttribute()
    {
        if ($this->stock_quantity > 0) {
            if ($this->stock_quantity <= $this->low_stock_threshold) {
                return 'Low Stock';
            }
            return 'In Stock';
        }
        return 'Out of Stock';
    }

    public function getAvailabilityBadgeAttribute()
    {
        if ($this->stock_quantity > 0) {
            if ($this->stock_quantity <= $this->low_stock_threshold) {
                return '<span class="badge bg-warning">Low Stock</span>';
            }
            return '<span class="badge bg-success">In Stock</span>';
        }
        return '<span class="badge bg-danger">Out of Stock</span>';
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
        if ($this->hasMedia('product_images')) {
            return $this->getFirstMediaUrl('product_images');
        }
        return asset('images/default-product.png');
    }

    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->current_price, 2);
    }

    public function getFormattedOriginalPriceAttribute()
    {
        if ($this->original_price > $this->current_price) {
            return '<span class="text-muted text-decoration-line-through">$' . number_format($this->original_price, 2) . '</span>';
        }
        return null;
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

    // Methods
    public function incrementClick()
    {
        $this->increment('click_count');
    }

    public function incrementConversion()
    {
        $this->increment('conversion_count');
    }

    public function canBePurchased()
    {
        if (!$this->is_active) return false;
        if ($this->stock_quantity <= 0) return false;
        return true;
    }

    public function updateStock($quantity)
    {
        $this->increment('stock_quantity', $quantity);
        
        // Check if stock is low
        if ($this->stock_quantity <= $this->low_stock_threshold) {
            // You can trigger notifications here
        }
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
        $this->addMediaCollection('product_images')
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
        
        $keywords = [$this->title, $this->brand, $this->model, $this->store_name, $this->category_name];
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
            'utm_medium' => 'product',
            'utm_campaign' => $this->id,
            'tracking_id' => $this->tracking_id,
            'product_id' => $this->id,
            'product_sku' => $this->sku
        ];

        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . http_build_query($params);
    }

    public function getMainImage()
    {
        return $this->getFirstMediaUrl('product_images') ?: asset('images/default-product.png');
    }

    public function getGalleryImages()
    {
        return $this->getMedia('product_images')->map(function ($media) {
            return $media->getUrl();
        });
    }

    public function hasVariants()
    {
        return $this->productVariants()->count() > 0;
    }

    public function getPriceRange()
    {
        if ($this->hasVariants()) {
            $minPrice = $this->productVariants()->min('price');
            $maxPrice = $this->productVariants()->max('price');
            return [
                'min' => $minPrice,
                'max' => $maxPrice,
                'formatted' => '$' . number_format($minPrice, 2) . ' - $' . number_format($maxPrice, 2)
            ];
        }
        return [
            'min' => $this->current_price,
            'max' => $this->current_price,
            'formatted' => $this->formatted_price
        ];
    }
}