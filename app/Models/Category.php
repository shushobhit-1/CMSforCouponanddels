<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'parent_id',
        'level',
        'order',
        'status', // active, inactive, featured
        'featured',
        'popular',
        'icon',
        'icon_class',
        'color',
        'background_color',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_image',
        'banner_text',
        'banner_button_text',
        'banner_button_link',
        'show_in_menu',
        'show_in_footer',
        'show_in_homepage',
        'show_in_sidebar',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'featured' => 'boolean',
        'popular' => 'boolean',
        'show_in_menu' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_homepage' => 'boolean',
        'show_in_sidebar' => 'boolean',
        'level' => 'integer',
        'order' => 'integer',
        'parent_id' => 'integer',
    ];

    protected $dates = [
        'published_at',
        'deleted_at',
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

    public function ancestors()
    {
        return $this->parent ? $this->parent->ancestors()->push($this->parent) : collect();
    }

    public function descendants()
    {
        return $this->children->map(function ($child) {
            return $child->descendants()->push($child);
        })->flatten();
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

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function menuItems()
    {
        return $this->morphMany(MenuItem::class, 'linkable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('popular', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeInMenu($query)
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeInFooter($query)
    {
        return $query->where('show_in_footer', true);
    }

    public function scopeInHomepage($query)
    {
        return $query->where('show_in_homepage', true);
    }

    public function scopeInSidebar($query)
    {
        return $query->where('show_in_sidebar', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsRootAttribute()
    {
        return is_null($this->parent_id);
    }

    public function getIsLeafAttribute()
    {
        return $this->children->isEmpty();
    }

    public function getDepthAttribute()
    {
        return $this->level;
    }

    public function getFullPathAttribute()
    {
        $path = collect([$this->name]);
        $parent = $this->parent;
        
        while ($parent) {
            $path->prepend($parent->name);
            $parent = $parent->parent;
        }
        
        return $path->implode(' > ');
    }

    public function getBreadcrumbsAttribute()
    {
        $breadcrumbs = collect([$this]);
        $parent = $this->parent;
        
        while ($parent) {
            $breadcrumbs->prepend($parent);
            $parent = $parent->parent;
        }
        
        return $breadcrumbs;
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('categories', 'thumb') ?: asset('images/default-category.jpg');
    }

    public function getBannerUrlAttribute()
    {
        return $this->getFirstMediaUrl('categories', 'banner') ?: asset('images/default-category-banner.jpg');
    }

    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return $this->getFirstMediaUrl('icons') ?: asset('images/default-icon.png');
        }
        return null;
    }

    public function getFormattedColorAttribute()
    {
        return $this->color ?: '#007bff';
    }

    public function getFormattedBackgroundColorAttribute()
    {
        return $this->background_color ?: '#f8f9fa';
    }

    public function getContentCountAttribute()
    {
        return $this->coupons()->count() + $this->deals()->count() + $this->products()->count();
    }

    public function getActiveContentCountAttribute()
    {
        return $this->coupons()->active()->count() + 
               $this->deals()->active()->count() + 
               $this->products()->active()->count();
    }

    public function getStoreCountAttribute()
    {
        return $this->stores()->count();
    }

    public function getActiveStoreCountAttribute()
    {
        return $this->stores()->active()->count();
    }

    public function getHasChildrenAttribute()
    {
        return $this->children->isNotEmpty();
    }

    public function getChildrenCountAttribute()
    {
        return $this->children->count();
    }

    public function getSiblingsAttribute()
    {
        if ($this->parent_id) {
            return static::where('parent_id', $this->parent_id)
                        ->where('id', '!=', $this->id)
                        ->where('status', 'active')
                        ->ordered()
                        ->get();
        }
        return collect();
    }

    public function getSiblingsCountAttribute()
    {
        if ($this->parent_id) {
            return static::where('parent_id', $this->parent_id)
                        ->where('id', '!=', $this->id)
                        ->where('status', 'active')
                        ->count();
        }
        return 0;
    }

    // Methods
    public function updateLevel()
    {
        if ($this->parent_id) {
            $this->level = $this->parent->level + 1;
        } else {
            $this->level = 0;
        }
        $this->save();
        
        // Update children levels
        foreach ($this->children as $child) {
            $child->updateLevel();
        }
    }

    public function moveTo($newParentId)
    {
        $oldParentId = $this->parent_id;
        $this->parent_id = $newParentId;
        $this->save();
        
        $this->updateLevel();
        
        // Update old parent's children count if needed
        if ($oldParentId) {
            $oldParent = static::find($oldParentId);
            if ($oldParent) {
                $oldParent->updateChildrenCount();
            }
        }
        
        // Update new parent's children count
        if ($newParentId) {
            $newParent = static::find($newParentId);
            if ($newParent) {
                $newParent->updateChildrenCount();
            }
        }
    }

    public function updateChildrenCount()
    {
        $this->children_count = $this->children()->count();
        $this->save();
    }

    public function getRootCategory()
    {
        if ($this->is_root) {
            return $this;
        }
        
        $parent = $this->parent;
        while ($parent && !$parent->is_root) {
            $parent = $parent->parent;
        }
        
        return $parent ?: $this;
    }

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

    public function getSeoData()
    {
        return [
            'title' => $this->meta_title ?: $this->name,
            'description' => $this->meta_description ?: $this->description,
            'keywords' => $this->meta_keywords,
            'og_image' => $this->og_image ?: $this->image_url,
            'twitter_image' => $this->twitter_image ?: $this->image_url,
        ];
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
            ->singleFile()
            ->useDisk('public');

        $this->addMediaCollection('icons')
            ->singleFile()
            ->useDisk('public');
    }

    public function getMenuData()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => route('categories.show', $this->slug),
            'icon' => $this->icon_class,
            'color' => $this->color,
            'children' => $this->children->active()->ordered()->map(function ($child) {
                return $child->getMenuData();
            }),
        ];
    }

    public function getFeaturedContent($limit = 6)
    {
        $coupons = $this->coupons()->featured()->active()->limit($limit)->get();
        $deals = $this->deals()->featured()->active()->limit($limit)->get();
        $products = $this->products()->featured()->active()->limit($limit)->get();
        
        return collect([$coupons, $deals, $products])->flatten()->take($limit);
    }

    public function getPopularContent($limit = 6)
    {
        $coupons = $this->coupons()->popular()->active()->limit($limit)->get();
        $deals = $this->deals()->popular()->active()->limit($limit)->get();
        $products = $this->products()->popular()->active()->limit($limit)->get();
        
        return collect([$coupons, $deals, $products])->flatten()->take($limit);
    }

    public function getRecentContent($limit = 6)
    {
        $coupons = $this->coupons()->active()->latest()->limit($limit)->get();
        $deals = $this->deals()->active()->latest()->limit($limit)->get();
        $products = $this->products()->active()->latest()->limit($limit)->get();
        
        return collect([$coupons, $deals, $products])->flatten()->take($limit);
    }
}