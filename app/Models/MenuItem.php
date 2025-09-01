<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'target', // _self, _blank, _parent, _top
        'rel',
        'icon',
        'icon_class',
        'image',
        'description',
        'css_class',
        'css_id',
        'link_class',
        'item_class',
        'sort_order',
        'level',
        'status', // active, inactive
        'is_visible',
        'is_featured',
        'is_external',
        'is_mega',
        'mega_menu_content',
        'mega_menu_columns',
        'mega_menu_width',
        'mega_menu_style',
        'dropdown_trigger', // hover, click
        'dropdown_animation',
        'dropdown_delay',
        'permission',
        'linkable_type',
        'linkable_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'is_external' => 'boolean',
        'is_mega' => 'boolean',
        'mega_menu_content' => 'array',
        'mega_menu_columns' => 'integer',
        'mega_menu_width' => 'integer',
        'mega_menu_style' => 'array',
        'dropdown_delay' => 'integer',
        'level' => 'integer',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
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

    public function linkable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByMenu($query, $menuId)
    {
        return $query->where('menu_id', $menuId);
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsVisibleAttribute()
    {
        return $this->is_visible;
    }

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
        return $this->children->isNotEmpty();
    }

    public function getIsLeafAttribute()
    {
        return $this->children->isEmpty();
    }

    public function getDepthAttribute()
    {
        return $this->level;
    }

    public function getTargetTextAttribute()
    {
        return match ($this->target) {
            '_self' => 'Same Window',
            '_blank' => 'New Window',
            '_parent' => 'Parent Frame',
            '_top' => 'Top Frame',
            default => 'Same Window'
        };
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            default => 'secondary'
        };
    }

    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('storage/menu-icons/' . $this->icon);
        }
        return null;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/menu-images/' . $this->image);
        }
        return null;
    }

    public function getCssClassesAttribute()
    {
        $classes = [];
        
        if ($this->css_class) {
            $classes[] = $this->css_class;
        }
        
        if ($this->is_mega) {
            $classes[] = 'mega-menu-item';
        }
        
        if ($this->has_children) {
            $classes[] = 'has-dropdown';
        }
        
        if ($this->is_featured) {
            $classes[] = 'featured';
        }
        
        return implode(' ', $classes);
    }

    public function getLinkClassesAttribute()
    {
        $classes = [];
        
        if ($this->link_class) {
            $classes[] = $this->link_class;
        }
        
        if ($this->is_external) {
            $classes[] = 'external-link';
        }
        
        if ($this->is_featured) {
            $classes[] = 'featured-link';
        }
        
        return implode(' ', $classes);
    }

    public function getItemClassesAttribute()
    {
        $classes = [];
        
        if ($this->item_class) {
            $classes[] = $this->item_class;
        }
        
        if ($this->level > 0) {
            $classes[] = "level-{$this->level}";
        }
        
        if ($this->is_featured) {
            $classes[] = 'featured-item';
        }
        
        return implode(' ', $classes);
    }

    public function getFullUrlAttribute()
    {
        if ($this->url) {
            if ($this->is_external || str_starts_with($this->url, 'http')) {
                return $this->url;
            }
            
            if (str_starts_with($this->url, '/')) {
                return url($this->url);
            }
            
            return url('/' . $this->url);
        }
        
        if ($this->linkable) {
            return $this->linkable->url ?? '#';
        }
        
        return '#';
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
        return $this->breadcrumb->pluck('title')->implode(' > ');
    }

    public function getMegaMenuStyleAttribute($value)
    {
        if ($value && is_array($value)) {
            return $value;
        }
        
        return [
            'width' => $this->mega_menu_width ?: 800,
            'columns' => $this->mega_menu_columns ?: 4,
            'style' => 'default',
            'animation' => 'fade',
            'delay' => $this->dropdown_delay ?: 300,
        ];
    }

    public function getDropdownSettingsAttribute()
    {
        return [
            'trigger' => $this->dropdown_trigger ?: 'hover',
            'animation' => $this->dropdown_animation ?: 'fade',
            'delay' => $this->dropdown_delay ?: 300,
        ];
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

    public function getRootItem()
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

    public function getMenuData()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->full_url,
            'target' => $this->target,
            'rel' => $this->rel,
            'icon' => $this->icon_class,
            'image' => $this->image_url,
            'description' => $this->description,
            'css_class' => $this->css_classes,
            'link_class' => $this->link_classes,
            'item_class' => $this->item_classes,
            'is_visible' => $this->is_visible,
            'is_featured' => $this->is_featured,
            'is_external' => $this->is_external,
            'is_mega' => $this->is_mega,
            'has_children' => $this->has_children,
            'level' => $this->level,
            'sort_order' => $this->sort_order,
        ];

        if ($this->is_mega) {
            $data['mega_menu'] = [
                'content' => $this->mega_menu_content,
                'style' => $this->mega_menu_style,
            ];
        }

        if ($this->has_children) {
            $data['children'] = $this->children->map(function ($child) {
                return $child->getMenuData();
            });
        }

        return $data;
    }

    public function renderHtml()
    {
        $html = "<li class=\"{$this->item_classes}\">";
        
        $html .= "<a href=\"{$this->full_url}\"";
        
        if ($this->target && $this->target !== '_self') {
            $html .= " target=\"{$this->target}\"";
        }
        
        if ($this->rel) {
            $html .= " rel=\"{$this->rel}\"";
        }
        
        if ($this->link_classes) {
            $html .= " class=\"{$this->link_classes}\"";
        }
        
        $html .= ">";
        
        if ($this->icon_class) {
            $html .= "<i class=\"{$this->icon_class}\"></i>";
        }
        
        $html .= "<span>{$this->title}</span>";
        
        if ($this->has_children) {
            $html .= "<i class=\"fas fa-chevron-down dropdown-arrow\"></i>";
        }
        
        $html .= "</a>";
        
        if ($this->has_children) {
            $html .= "<ul class=\"dropdown-menu\">";
            
            foreach ($this->children as $child) {
                $html .= $child->renderHtml();
            }
            
            $html .= "</ul>";
        }
        
        $html .= "</li>";
        
        return $html;
    }

    public function isDescendantOf($item)
    {
        return $this->getAllAncestors()->contains($item);
    }

    public function isAncestorOf($item)
    {
        return $item->isDescendantOf($this);
    }

    public function canAccess($user = null)
    {
        if (!$this->permission) {
            return true;
        }
        
        if (!$user) {
            return false;
        }
        
        return $user->hasPermissionTo($this->permission);
    }

    // Static Methods
    public static function getByMenu($menuId)
    {
        return static::byMenu($menuId)->ordered()->get();
    }

    public static function getRootItems($menuId)
    {
        return static::byMenu($menuId)->root()->ordered()->get();
    }

    public static function getVisibleItems($menuId)
    {
        return static::byMenu($menuId)->visible()->active()->ordered()->get();
    }

    public static function getFeaturedItems($menuId)
    {
        return static::byMenu($menuId)->featured()->visible()->active()->ordered()->get();
    }

    public static function reorderItems($itemIds)
    {
        foreach ($itemIds as $index => $itemId) {
            static::where('id', $itemId)->update(['sort_order' => $index + 1]);
        }
        
        return true;
    }

    public static function getMenuTree($menuId)
    {
        $items = static::byMenu($menuId)->ordered()->get();
        
        return $items->whereNull('parent_id')->map(function ($item) use ($items) {
            return $item->buildTree($items);
        });
    }

    public function buildTree($items)
    {
        $children = $items->where('parent_id', $this->id);
        
        if ($children->isNotEmpty()) {
            $this->children = $children->map(function ($child) use ($items) {
                return $child->buildTree($items);
            });
        }
        
        return $this;
    }

    public static function cleanupOrphanedItems()
    {
        return static::whereDoesntHave('menu')->delete();
    }

    public static function getMenuItemStats()
    {
        $total = static::count();
        $active = static::active()->count();
        $inactive = static::where('status', 'inactive')->count();
        $visible = static::visible()->count();
        $featured = static::featured()->count();
        $withChildren = static::whereHas('children')->count();
        $root = static::root()->count();

        $byLevel = static::selectRaw('level, COUNT(*) as count')
                         ->groupBy('level')
                         ->get()
                         ->keyBy('level');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'visible' => $visible,
            'featured' => $featured,
            'with_children' => $withChildren,
            'root' => $root,
            'by_level' => $byLevel,
        ];
    }
}