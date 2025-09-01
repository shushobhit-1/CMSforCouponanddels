<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location', // header, footer, sidebar, mobile, custom
        'type', // mega, dropdown, simple
        'status', // active, inactive
        'is_primary',
        'is_mobile',
        'is_footer',
        'is_sidebar',
        'max_depth',
        'css_class',
        'css_id',
        'container_class',
        'menu_class',
        'item_class',
        'link_class',
        'dropdown_class',
        'mega_menu_width',
        'mega_menu_columns',
        'mega_menu_style',
        'animation_speed',
        'hover_effect',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_mobile' => 'boolean',
        'is_footer' => 'boolean',
        'is_sidebar' => 'boolean',
        'max_depth' => 'integer',
        'mega_menu_width' => 'integer',
        'mega_menu_columns' => 'integer',
        'animation_speed' => 'integer',
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('sort_order');
    }

    public function allItems()
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
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

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeMobile($query)
    {
        return $query->where('is_mobile', true);
    }

    public function scopeFooter($query)
    {
        return $query->where('is_footer', true);
    }

    public function scopeSidebar($query)
    {
        return $query->where('is_sidebar', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsMegaMenuAttribute()
    {
        return $this->type === 'mega';
    }

    public function getIsDropdownAttribute()
    {
        return $this->type === 'dropdown';
    }

    public function getIsSimpleAttribute()
    {
        return $this->type === 'simple';
    }

    public function getTypeTextAttribute()
    {
        return match ($this->type) {
            'mega' => 'Mega Menu',
            'dropdown' => 'Dropdown Menu',
            'simple' => 'Simple Menu',
            default => ucfirst($this->type)
        };
    }

    public function getLocationTextAttribute()
    {
        return match ($this->location) {
            'header' => 'Header Menu',
            'footer' => 'Footer Menu',
            'sidebar' => 'Sidebar Menu',
            'mobile' => 'Mobile Menu',
            'custom' => 'Custom Menu',
            default => ucfirst($this->location)
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

    public function getCssClassesAttribute()
    {
        $classes = [];
        
        if ($this->css_class) {
            $classes[] = $this->css_class;
        }
        
        if ($this->type === 'mega') {
            $classes[] = 'mega-menu';
        }
        
        if ($this->type === 'dropdown') {
            $classes[] = 'dropdown-menu';
        }
        
        return implode(' ', $classes);
    }

    public function getMenuAttributesAttribute()
    {
        $attributes = [];
        
        if ($this->css_id) {
            $attributes['id'] = $this->css_id;
        }
        
        if ($this->css_classes) {
            $attributes['class'] = $this->css_classes;
        }
        
        return $attributes;
    }

    public function getMegaMenuStyleAttribute($value)
    {
        if ($value) {
            return json_decode($value, true);
        }
        
        return [
            'width' => $this->mega_menu_width ?: 800,
            'columns' => $this->mega_menu_columns ?: 4,
            'style' => 'default',
        ];
    }

    public function getHoverEffectAttribute($value)
    {
        if ($value) {
            return json_decode($value, true);
        }
        
        return [
            'type' => 'fade',
            'speed' => $this->animation_speed ?: 300,
            'direction' => 'down',
        ];
    }

    // Methods
    public function getMenuStructure()
    {
        return $this->items->map(function ($item) {
            return $item->getMenuData();
        });
    }

    public function getMenuHtml()
    {
        $html = "<ul class=\"{$this->menu_class}\">";
        
        foreach ($this->items as $item) {
            $html .= $item->renderHtml();
        }
        
        $html .= "</ul>";
        
        return $html;
    }

    public function getMenuJson()
    {
        return $this->getMenuStructure()->toJson();
    }

    public function addItem($data)
    {
        return $this->items()->create($data);
    }

    public function updateItem($itemId, $data)
    {
        $item = $this->items()->find($itemId);
        
        if ($item) {
            $item->update($data);
            return $item;
        }
        
        return false;
    }

    public function removeItem($itemId)
    {
        $item = $this->items()->find($itemId);
        
        if ($item) {
            return $item->delete();
        }
        
        return false;
    }

    public function reorderItems($itemIds)
    {
        foreach ($itemIds as $index => $itemId) {
            $this->items()->where('id', $itemId)->update(['sort_order' => $index + 1]);
        }
        
        return true;
    }

    public function duplicate()
    {
        $newMenu = $this->replicate();
        $newMenu->name = $this->name . ' (Copy)';
        $newMenu->slug = $this->slug . '-copy';
        $newMenu->is_primary = false;
        $newMenu->save();
        
        // Duplicate menu items
        foreach ($this->allItems as $item) {
            $newItem = $item->replicate();
            $newItem->menu_id = $newMenu->id;
            $newItem->save();
        }
        
        return $newMenu;
    }

    public function getActiveItems()
    {
        return $this->items()->whereHas('children', function ($query) {
            $query->where('status', 'active');
        })->orWhere('status', 'active')->get();
    }

    public function getVisibleItems()
    {
        return $this->items()->where('is_visible', true)->get();
    }

    public function getItemsByPermission($user = null)
    {
        $query = $this->items();
        
        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('permission')
                  ->orWhereHas('permissions', function ($permQuery) use ($user) {
                      $permQuery->whereIn('name', $user->getAllPermissions()->pluck('name'));
                  });
            });
        }
        
        return $query->get();
    }

    // Static Methods
    public static function getByLocation($location)
    {
        return static::active()->byLocation($location)->first();
    }

    public static function getPrimaryMenu()
    {
        return static::active()->primary()->first();
    }

    public static function getMobileMenu()
    {
        return static::active()->mobile()->first();
    }

    public static function getFooterMenu()
    {
        return static::active()->footer()->first();
    }

    public static function getSidebarMenu()
    {
        return static::active()->sidebar()->first();
    }

    public static function createDefaultMenus()
    {
        $menus = [
            [
                'name' => 'Main Navigation',
                'slug' => 'main-navigation',
                'description' => 'Primary navigation menu for the header',
                'location' => 'header',
                'type' => 'mega',
                'status' => 'active',
                'is_primary' => true,
                'is_mobile' => true,
                'max_depth' => 3,
                'mega_menu_width' => 800,
                'mega_menu_columns' => 4,
                'css_class' => 'navbar-nav',
                'menu_class' => 'nav-menu',
                'item_class' => 'nav-item',
                'link_class' => 'nav-link',
                'dropdown_class' => 'dropdown-menu',
                'animation_speed' => 300,
            ],
            [
                'name' => 'Footer Menu',
                'slug' => 'footer-menu',
                'description' => 'Footer navigation menu',
                'location' => 'footer',
                'type' => 'simple',
                'status' => 'active',
                'is_footer' => true,
                'max_depth' => 2,
                'css_class' => 'footer-nav',
                'menu_class' => 'footer-menu',
                'item_class' => 'footer-item',
                'link_class' => 'footer-link',
            ],
            [
                'name' => 'Sidebar Menu',
                'slug' => 'sidebar-menu',
                'description' => 'Sidebar navigation menu',
                'location' => 'sidebar',
                'type' => 'dropdown',
                'status' => 'active',
                'is_sidebar' => true,
                'max_depth' => 2,
                'css_class' => 'sidebar-nav',
                'menu_class' => 'sidebar-menu',
                'item_class' => 'sidebar-item',
                'link_class' => 'sidebar-link',
                'dropdown_class' => 'sidebar-dropdown',
            ],
            [
                'name' => 'Mobile Menu',
                'slug' => 'mobile-menu',
                'description' => 'Mobile navigation menu',
                'location' => 'mobile',
                'type' => 'dropdown',
                'status' => 'active',
                'is_mobile' => true,
                'max_depth' => 2,
                'css_class' => 'mobile-nav',
                'menu_class' => 'mobile-menu',
                'item_class' => 'mobile-item',
                'link_class' => 'mobile-link',
                'dropdown_class' => 'mobile-dropdown',
                'animation_speed' => 200,
            ],
        ];

        $created = 0;
        $errors = [];

        foreach ($menus as $menuData) {
            try {
                if (!static::where('slug', $menuData['slug'])->exists()) {
                    static::create($menuData);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to create menu '{$menuData['name']}': " . $e->getMessage();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
            'success' => empty($errors)
        ];
    }

    public static function getMenuStats()
    {
        $total = static::count();
        $active = static::active()->count();
        $inactive = static::where('status', 'inactive')->count();
        $mega = static::byType('mega')->count();
        $dropdown = static::byType('dropdown')->count();
        $simple = static::byType('simple')->count();

        $byLocation = static::selectRaw('location, COUNT(*) as count')
                            ->groupBy('location')
                            ->get()
                            ->keyBy('location');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'by_type' => [
                'mega' => $mega,
                'dropdown' => $dropdown,
                'simple' => $simple,
            ],
            'by_location' => $byLocation,
        ];
    }
}