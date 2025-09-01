<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'parent_id',
        'icon',
        'color',
        'is_featured',
        'is_active',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'created_by',
        'status', // active, inactive, draft
        'banner_text',
        'banner_color',
        'show_in_menu',
        'show_in_footer',
        'show_in_homepage',
        'seo_score',
        'page_speed_score'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_homepage' => 'boolean',
        'sort_order' => 'integer',
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

    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    public function ancestors()
    {
        return $this->parent()->with('ancestors');
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
        return $this->belongsToMany(Store::class, 'store_categories');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeMenuVisible($query)
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeFooterVisible($query)
    {
        return $query->where('show_in_footer', true);
    }

    public function scopeHomepageVisible($query)
    {
        return $query->where('show_in_homepage', true);
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
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('name', 'asc');
    }

    // Accessors
    public function getIsRootAttribute()
    {
        return is_null($this->parent_id);
    }

    public function getIsChildAttribute()
    {
        return !is_null($this->parent_id);
    }

    public function getHasChildrenAttribute()
    {
        return $this->children()->exists();
    }

    public function getLevelAttribute()
    {
        if ($this->is_root) return 0;
        return $this->parent ? $this->parent->level + 1 : 1;
    }

    public function getBreadcrumbAttribute()
    {
        $breadcrumb = collect([$this]);
        $parent = $this->parent;
        
        while ($parent) {
            $breadcrumb->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $breadcrumb;
    }

    public function getBreadcrumbTextAttribute()
    {
        return $this->breadcrumb->pluck('name')->implode(' > ');
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('categories', 'medium') ?: asset('images/default-category.jpg');
    }

    public function getThumbnailUrlAttribute()
    {
        return $this->getFirstMediaUrl('categories', 'thumbnail') ?: asset('images/default-category-thumb.jpg');
    }

    public function getIconUrlAttribute()
    {
        return $this->icon ?: 'fas fa-folder';
    }

    public function getContentCountAttribute()
    {
        return $this->coupons()->count() + 
               $this->deals()->count() + 
               $this->products()->count();
    }

    public function getActiveContentCountAttribute()
    {
        return $this->coupons()->active()->count() + 
               $this->deals()->active()->count() + 
               $this->products()->active()->count();
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'draft' => 'Draft',
            default => 'Unknown'
        };
    }

    // Methods
    public function getAllDescendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }

    public function getAllAncestors()
    {
        $ancestors = collect();
        $parent = $this->parent;
        
        while ($parent) {
            $ancestors->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $ancestors;
    }

    public function getPath()
    {
        return $this->ancestors->pluck('slug')->push($this->slug)->implode('/');
    }

    public function getFullPath()
    {
        return '/categories/' . $this->getPath();
    }

    public function isDescendantOf($category)
    {
        return $this->getAllAncestors()->contains($category);
    }

    public function isAncestorOf($category)
    {
        return $category->isDescendantOf($this);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('categories')
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('banners')
            ->useDisk('public');
    }

    // SEO Methods
    public function getSeoTitle()
    {
        return $this->meta_title ?: $this->name;
    }

    public function getSeoDescription()
    {
        return $this->meta_description ?: Str::limit($this->description, 160);
    }

    public function getSeoKeywords()
    {
        return $this->meta_keywords ?: $this->name;
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
        if ($this->image_url) $score += 10;
        if ($this->icon) $score += 10;
        if ($this->color) $score += 5;
        if ($this->banner_text) $score += 5;
        
        $this->update(['seo_score' => $score]);
        return $score;
    }

    // Menu Methods
    public function getMenuData()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => $this->getFullPath(),
            'icon' => $this->icon_url,
            'color' => $this->color,
            'children' => $this->children->active()->menuVisible()->ordered()->get()->map(function($child) {
                return $child->getMenuData();
            })
        ];
    }

    // Statistics Methods
    public function getStatistics()
    {
        return [
            'total_coupons' => $this->coupons()->count(),
            'active_coupons' => $this->coupons()->active()->count(),
            'total_deals' => $this->deals()->count(),
            'active_deals' => $this->deals()->active()->count(),
            'total_products' => $this->products()->count(),
            'active_products' => $this->products()->active()->count(),
            'total_stores' => $this->stores()->count(),
            'featured_stores' => $this->stores()->where('is_featured', true)->count()
        ];
    }
}