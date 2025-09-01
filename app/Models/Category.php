<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'parent_id',
        'is_active',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'icon_class',
        'color',
        'banner_text',
        'category_type', // coupon, deal, product, store
        'commission_rate',
        'created_by',
        'status',
        'seo_settings',
        'display_settings',
        'filter_options',
        'category_popup_settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'seo_settings' => 'array',
        'display_settings' => 'array',
        'filter_options' => 'array',
        'category_popup_settings' => 'array',
        'commission_rate' => 'decimal:2'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function allParents()
    {
        return $this->parent()->with('allParents');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function menuItems()
    {
        return $this->morphMany(MenuItem::class, 'linkable');
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

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('category_type', $type);
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->full_name . ' > ' . $this->name;
        }
        return $this->name;
    }

    public function getBreadcrumbAttribute()
    {
        $breadcrumb = [];
        $current = $this;
        
        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug
            ]);
            $current = $current->parent;
        }
        
        return $breadcrumb;
    }

    public function getLevelAttribute()
    {
        $level = 0;
        $current = $this;
        
        while ($current->parent) {
            $level++;
            $current = $current->parent;
        }
        
        return $level;
    }

    public function getIsRootAttribute()
    {
        return is_null($this->parent_id);
    }

    public function getIsLeafAttribute()
    {
        return $this->children()->count() === 0;
    }

    public function getHasChildrenAttribute()
    {
        return $this->children()->count() > 0;
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('category_images', 'medium') ?: asset('images/default-category.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('category_images', 'thumbnail') ?: asset('images/default-category-thumb.jpg');
    }

    public function getBannerUrlAttribute()
    {
        return $this->getFirstMediaUrl('category_banners', 'large') ?: asset('images/default-category-banner.jpg');
    }

    public function getIconHtmlAttribute()
    {
        if ($this->icon_class) {
            return '<i class="' . $this->icon_class . '"></i>';
        }
        return '<i class="fas fa-folder"></i>';
    }

    public function getColorStyleAttribute()
    {
        if ($this->color) {
            return 'color: ' . $this->color;
        }
        return '';
    }

    public function getContentCountAttribute()
    {
        $count = 0;
        
        if ($this->category_type === 'coupon' || $this->category_type === 'all') {
            $count += $this->coupons()->active()->count();
        }
        
        if ($this->category_type === 'deal' || $this->category_type === 'all') {
            $count += $this->deals()->active()->count();
        }
        
        if ($this->category_type === 'product' || $this->category_type === 'all') {
            $count += $this->products()->active()->count();
        }
        
        if ($this->category_type === 'store' || $this->category_type === 'all') {
            $count += $this->stores()->active()->count();
        }
        
        return $count;
    }

    public function getActiveContentCountAttribute()
    {
        $count = 0;
        
        if ($this->category_type === 'coupon' || $this->category_type === 'all') {
            $count += $this->coupons()->active()->count();
        }
        
        if ($this->category_type === 'deal' || $this->category_type === 'all') {
            $count += $this->deals()->active()->count();
        }
        
        if ($this->category_type === 'product' || $this->category_type === 'all') {
            $count += $this->products()->active()->count();
        }
        
        if ($this->category_type === 'store' || $this->category_type === 'all') {
            $count += $this->stores()->active()->count();
        }
        
        return $count;
    }

    public function getSeoData()
    {
        return [
            'title' => $this->meta_title ?: $this->name . ' - Coupons, Deals & Products',
            'description' => $this->meta_description ?: $this->short_description ?: 'Find the best coupons, deals, and products in ' . $this->name . '. Save money with exclusive offers and discounts.',
            'keywords' => $this->meta_keywords ?: $this->name . ', coupons, deals, discounts, savings, offers',
            'og_image' => $this->og_image ?: $this->image_url,
            'twitter_image' => $this->twitter_image ?: $this->image_url,
            'canonical_url' => route('categories.show', $this->slug)
        ];
    }

    // Methods
    public function getAllChildren()
    {
        $children = collect();
        
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }
        
        return $children;
    }

    public function getAllParents()
    {
        $parents = collect();
        $current = $this->parent;
        
        while ($current) {
            $parents->push($current);
            $current = $current->parent;
        }
        
        return $parents->reverse();
    }

    public function getDescendants()
    {
        return $this->getAllChildren();
    }

    public function getAncestors()
    {
        return $this->getAllParents();
    }

    public function isDescendantOf($category)
    {
        return $this->getAncestors()->contains('id', $category->id);
    }

    public function isAncestorOf($category)
    {
        return $category->isDescendantOf($this);
    }

    public function getSiblings()
    {
        if ($this->parent_id) {
            return static::where('parent_id', $this->parent_id)
                        ->where('id', '!=', $this->id)
                        ->get();
        }
        
        return collect();
    }

    public function getPath()
    {
        $path = collect([$this]);
        $current = $this;
        
        while ($current->parent) {
            $path->prepend($current->parent);
            $current = $current->parent;
        }
        
        return $path;
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
            
        $this->addMediaCollection('category_banners')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->withResponsiveImages();
    }

    public function updateContentCount()
    {
        $this->content_count = $this->getContentCountAttribute();
        $this->save();
    }

    public function getFilterOptions()
    {
        $options = $this->filter_options ?: [];
        
        // Add dynamic filter options based on content
        if ($this->category_type === 'product' || $this->category_type === 'all') {
            $priceRange = $this->products()->selectRaw('MIN(sale_price) as min_price, MAX(sale_price) as max_price')->first();
            if ($priceRange && $priceRange->min_price && $priceRange->max_price) {
                $options['price_range'] = [
                    'min' => $priceRange->min_price,
                    'max' => $priceRange->max_price
                ];
            }
            
            $brands = $this->products()->distinct()->pluck('brand')->filter()->values();
            if ($brands->count() > 0) {
                $options['brands'] = $brands->toArray();
            }
        }
        
        return $options;
    }

    public function getDisplaySettings()
    {
        $defaults = [
            'show_banner' => true,
            'show_description' => true,
            'show_subcategories' => true,
            'items_per_page' => 20,
            'sort_options' => ['newest', 'popular', 'price_low', 'price_high', 'rating'],
            'default_sort' => 'newest',
            'show_filters' => true,
            'show_pagination' => true
        ];
        
        $settings = $this->display_settings ?: [];
        return array_merge($defaults, $settings);
    }
}