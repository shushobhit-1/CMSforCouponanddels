<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Slider extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location', // homepage, category, product, custom
        'type', // carousel, hero, banner, testimonial, logo
        'status', // active, inactive, scheduled
        'is_active',
        'is_featured',
        'start_date',
        'end_date',
        'autoplay',
        'autoplay_speed',
        'pause_on_hover',
        'show_arrows',
        'show_dots',
        'show_indicators',
        'infinite_loop',
        'fade_effect',
        'slide_effect',
        'animation_speed',
        'height',
        'width',
        'max_height',
        'min_height',
        'responsive',
        'mobile_enabled',
        'tablet_enabled',
        'desktop_enabled',
        'css_class',
        'css_id',
        'container_class',
        'wrapper_class',
        'slide_class',
        'arrow_class',
        'dot_class',
        'indicator_class',
        'theme',
        'style',
        'settings',
        'created_by',
        'updated_by',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'autoplay' => 'boolean',
        'pause_on_hover' => 'boolean',
        'show_arrows' => 'boolean',
        'show_dots' => 'boolean',
        'show_indicators' => 'boolean',
        'infinite_loop' => 'boolean',
        'fade_effect' => 'boolean',
        'responsive' => 'boolean',
        'mobile_enabled' => 'boolean',
        'tablet_enabled' => 'boolean',
        'desktop_enabled' => 'boolean',
        'autoplay_speed' => 'integer',
        'animation_speed' => 'integer',
        'height' => 'integer',
        'width' => 'integer',
        'max_height' => 'integer',
        'min_height' => 'integer',
        'settings' => 'array',
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'published_at',
        'expires_at',
        'deleted_at',
    ];

    // Relationships
    public function slides()
    {
        return $this->hasMany(Slide::class)->orderBy('sort_order');
    }

    public function activeSlides()
    {
        return $this->hasMany(Slide::class)
                    ->where('status', 'active')
                    ->where(function ($query) {
                        $query->whereNull('start_date')
                              ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    })
                    ->orderBy('sort_order');
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
        return $query->where('status', 'active')
                    ->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('published_at')
              ->orWhere('published_at', '<=', now());
        });
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
              });
        });
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
        return $this->status === 'active' && 
               $this->is_active && 
               $this->isPublished() && 
               !$this->isExpired();
    }

    public function getIsPublishedAttribute()
    {
        return $this->isPublished();
    }

    public function getIsExpiredAttribute()
    {
        return $this->isExpired();
    }

    public function getIsScheduledAttribute()
    {
        return $this->start_date && $this->start_date > now();
    }

    public function getStatusTextAttribute()
    {
        if ($this->is_expired) return 'Expired';
        if ($this->is_scheduled) return 'Scheduled';
        if ($this->is_active) return 'Active';
        return 'Inactive';
    }

    public function getStatusColorAttribute()
    {
        if ($this->is_expired) return 'danger';
        if ($this->is_scheduled) return 'warning';
        if ($this->is_active) return 'success';
        return 'secondary';
    }

    public function getTypeTextAttribute()
    {
        return match ($this->type) {
            'carousel' => 'Carousel Slider',
            'hero' => 'Hero Slider',
            'banner' => 'Banner Slider',
            'testimonial' => 'Testimonial Slider',
            'logo' => 'Logo Slider',
            default => ucfirst($this->type)
        };
    }

    public function getLocationTextAttribute()
    {
        return match ($this->location) {
            'homepage' => 'Homepage',
            'category' => 'Category Page',
            'product' => 'Product Page',
            'custom' => 'Custom Page',
            default => ucfirst($this->location)
        };
    }

    public function getCssClassesAttribute()
    {
        $classes = ['slider', "slider-{$this->type}"];
        
        if ($this->css_class) {
            $classes[] = $this->css_class;
        }
        
        if ($this->is_featured) {
            $classes[] = 'featured-slider';
        }
        
        if ($this->responsive) {
            $classes[] = 'responsive-slider';
        }
        
        if ($this->fade_effect) {
            $classes[] = 'fade-slider';
        }
        
        return implode(' ', $classes);
    }

    public function getSliderAttributesAttribute()
    {
        $attributes = [];
        
        if ($this->css_id) {
            $attributes['id'] = $this->css_id;
        }
        
        if ($this->css_classes) {
            $attributes['class'] = $this->css_classes;
        }
        
        if ($this->autoplay) {
            $attributes['data-autoplay'] = 'true';
            $attributes['data-autoplay-speed'] = $this->autoplay_speed ?: 5000;
        }
        
        if ($this->pause_on_hover) {
            $attributes['data-pause-on-hover'] = 'true';
        }
        
        if ($this->show_arrows) {
            $attributes['data-show-arrows'] = 'true';
        }
        
        if ($this->show_dots) {
            $attributes['data-show-dots'] = 'true';
        }
        
        if ($this->show_indicators) {
            $attributes['data-show-indicators'] = 'true';
        }
        
        if ($this->infinite_loop) {
            $attributes['data-infinite'] = 'true';
        }
        
        if ($this->fade_effect) {
            $attributes['data-fade'] = 'true';
        }
        
        $attributes['data-animation-speed'] = $this->animation_speed ?: 600;
        
        return $attributes;
    }

    public function getSliderSettingsAttribute()
    {
        $settings = $this->settings ?: [];
        
        return array_merge([
            'autoplay' => $this->autoplay,
            'autoplaySpeed' => $this->autoplay_speed ?: 5000,
            'pauseOnHover' => $this->pause_on_hover,
            'arrows' => $this->show_arrows,
            'dots' => $this->show_dots,
            'indicators' => $this->show_indicators,
            'infinite' => $this->infinite_loop,
            'fade' => $this->fade_effect,
            'speed' => $this->animation_speed ?: 600,
            'responsive' => $this->responsive,
            'mobileEnabled' => $this->mobile_enabled,
            'tabletEnabled' => $this->tablet_enabled,
            'desktopEnabled' => $this->desktop_enabled,
        ], $settings);
    }

    public function getSliderStyleAttribute()
    {
        $style = [];
        
        if ($this->height) {
            $style['height'] = $this->height . 'px';
        }
        
        if ($this->width) {
            $style['width'] = $this->width . 'px';
        }
        
        if ($this->max_height) {
            $style['max-height'] = $this->max_height . 'px';
        }
        
        if ($this->min_height) {
            $style['min-height'] = $this->min_height . 'px';
        }
        
        return $style;
    }

    public function getSlideCountAttribute()
    {
        return $this->slides()->count();
    }

    public function getActiveSlideCountAttribute()
    {
        return $this->activeSlides()->count();
    }

    public function getFeaturedSlideCountAttribute()
    {
        return $this->slides()->where('is_featured', true)->count();
    }

    // Methods
    public function isPublished()
    {
        return !$this->published_at || $this->published_at <= now();
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function isActive()
    {
        return $this->status === 'active' && 
               $this->is_active && 
               $this->isPublished() && 
               !$this->isExpired();
    }

    public function publish()
    {
        $this->update([
            'status' => 'active',
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'inactive',
            'is_active' => false,
        ]);
    }

    public function schedule($startDate, $endDate = null)
    {
        $this->update([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'scheduled',
        ]);
    }

    public function activate()
    {
        $this->update([
            'status' => 'active',
            'is_active' => true,
        ]);
    }

    public function deactivate()
    {
        $this->update([
            'status' => 'inactive',
            'is_active' => false,
        ]);
    }

    public function addSlide($data)
    {
        return $this->slides()->create($data);
    }

    public function updateSlide($slideId, $data)
    {
        $slide = $this->slides()->find($slideId);
        
        if ($slide) {
            $slide->update($data);
            return $slide;
        }
        
        return false;
    }

    public function removeSlide($slideId)
    {
        $slide = $this->slides()->find($slideId);
        
        if ($slide) {
            return $slide->delete();
        }
        
        return false;
    }

    public function reorderSlides($slideIds)
    {
        foreach ($slideIds as $index => $slideId) {
            $this->slides()->where('id', $slideId)->update(['sort_order' => $index + 1]);
        }
        
        return true;
    }

    public function duplicate()
    {
        $newSlider = $this->replicate();
        $newSlider->name = $this->name . ' (Copy)';
        $newSlider->slug = $this->slug . '-copy';
        $newSlider->is_active = false;
        $newSlider->status = 'inactive';
        $newSlider->save();
        
        // Duplicate slides
        foreach ($this->slides as $slide) {
            $newSlide = $slide->replicate();
            $newSlide->slider_id = $newSlider->id;
            $newSlide->save();
        }
        
        return $newSlider;
    }

    public function getSliderHtml()
    {
        $slides = $this->activeSlides;
        
        if ($slides->isEmpty()) {
            return '';
        }
        
        $html = "<div class=\"{$this->container_class}\">";
        $html .= "<div class=\"{$this->wrapper_class}\">";
        $html .= "<div class=\"{$this->css_classes}\"";
        
        foreach ($this->slider_attributes as $key => $value) {
            $html .= " {$key}=\"{$value}\"";
        }
        
        $html .= ">";
        
        foreach ($slides as $slide) {
            $html .= $slide->renderHtml();
        }
        
        if ($this->show_arrows) {
            $html .= "<button class=\"{$this->arrow_class} prev-arrow\"><i class=\"fas fa-chevron-left\"></i></button>";
            $html .= "<button class=\"{$this->arrow_class} next-arrow\"><i class=\"fas fa-chevron-right\"></i></button>";
        }
        
        if ($this->show_dots) {
            $html .= "<div class=\"{$this->dot_class}\">";
            foreach ($slides as $index => $slide) {
                $activeClass = $index === 0 ? 'active' : '';
                $html .= "<span class=\"dot {$activeClass}\" data-slide=\"{$index}\"></span>";
            }
            $html .= "</div>";
        }
        
        if ($this->show_indicators) {
            $html .= "<div class=\"{$this->indicator_class}\">";
            foreach ($slides as $index => $slide) {
                $activeClass = $index === 0 ? 'active' : '';
                $html .= "<span class=\"indicator {$activeClass}\" data-slide=\"{$index}\"></span>";
            }
            $html .= "</div>";
        }
        
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        
        return $html;
    }

    public function getSliderJson()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'location' => $this->location,
            'settings' => $this->slider_settings,
            'slides' => $this->activeSlides->map(function ($slide) {
                return $slide->getSlideData();
            }),
        ];
    }

    // Static Methods
    public static function getByLocation($location)
    {
        return static::active()->published()->notExpired()->byLocation($location)->first();
    }

    public static function getHomepageSlider()
    {
        return static::getByLocation('homepage');
    }

    public static function getActiveSliders()
    {
        return static::active()->published()->notExpired()->get();
    }

    public static function getFeaturedSliders()
    {
        return static::featured()->active()->published()->notExpired()->get();
    }

    public static function getSlidersByType($type)
    {
        return static::active()->published()->notExpired()->byType($type)->get();
    }

    public static function createDefaultSliders()
    {
        $sliders = [
            [
                'name' => 'Homepage Hero Slider',
                'slug' => 'homepage-hero-slider',
                'description' => 'Main hero slider for the homepage',
                'location' => 'homepage',
                'type' => 'hero',
                'status' => 'active',
                'is_active' => true,
                'is_featured' => true,
                'autoplay' => true,
                'autoplay_speed' => 5000,
                'pause_on_hover' => true,
                'show_arrows' => true,
                'show_dots' => true,
                'show_indicators' => true,
                'infinite_loop' => true,
                'fade_effect' => false,
                'animation_speed' => 600,
                'height' => 500,
                'responsive' => true,
                'mobile_enabled' => true,
                'tablet_enabled' => true,
                'desktop_enabled' => true,
                'css_class' => 'hero-slider',
                'container_class' => 'hero-slider-container',
                'wrapper_class' => 'hero-slider-wrapper',
                'slide_class' => 'hero-slide',
                'arrow_class' => 'hero-arrow',
                'dot_class' => 'hero-dots',
                'indicator_class' => 'hero-indicators',
                'theme' => 'default',
                'style' => 'modern',
            ],
            [
                'name' => 'Category Banner Slider',
                'slug' => 'category-banner-slider',
                'description' => 'Banner slider for category pages',
                'location' => 'category',
                'type' => 'banner',
                'status' => 'active',
                'is_active' => true,
                'is_featured' => false,
                'autoplay' => true,
                'autoplay_speed' => 4000,
                'pause_on_hover' => true,
                'show_arrows' => true,
                'show_dots' => false,
                'show_indicators' => false,
                'infinite_loop' => true,
                'fade_effect' => false,
                'animation_speed' => 500,
                'height' => 300,
                'responsive' => true,
                'mobile_enabled' => true,
                'tablet_enabled' => true,
                'desktop_enabled' => true,
                'css_class' => 'category-banner-slider',
                'container_class' => 'category-banner-container',
                'wrapper_class' => 'category-banner-wrapper',
                'slide_class' => 'category-banner-slide',
                'arrow_class' => 'category-banner-arrow',
                'theme' => 'default',
                'style' => 'clean',
            ],
            [
                'name' => 'Logo Slider',
                'slug' => 'logo-slider',
                'description' => 'Logo slider for brand showcase',
                'location' => 'homepage',
                'type' => 'logo',
                'status' => 'active',
                'is_active' => true,
                'is_featured' => false,
                'autoplay' => true,
                'autoplay_speed' => 3000,
                'pause_on_hover' => true,
                'show_arrows' => false,
                'show_dots' => false,
                'show_indicators' => false,
                'infinite_loop' => true,
                'fade_effect' => false,
                'animation_speed' => 400,
                'height' => 100,
                'responsive' => true,
                'mobile_enabled' => true,
                'tablet_enabled' => true,
                'desktop_enabled' => true,
                'css_class' => 'logo-slider',
                'container_class' => 'logo-slider-container',
                'wrapper_class' => 'logo-slider-wrapper',
                'slide_class' => 'logo-slide',
                'theme' => 'default',
                'style' => 'minimal',
            ],
        ];

        $created = 0;
        $errors = [];

        foreach ($sliders as $sliderData) {
            try {
                if (!static::where('slug', $sliderData['slug'])->exists()) {
                    static::create($sliderData);
                    $created++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to create slider '{$sliderData['name']}': " . $e->getMessage();
            }
        }

        return [
            'created' => $created,
            'errors' => $errors,
            'success' => empty($errors)
        ];
    }

    public static function getSliderStats()
    {
        $total = static::count();
        $active = static::active()->count();
        $inactive = static::where('status', 'inactive')->count();
        $scheduled = static::where('status', 'scheduled')->count();
        $expired = static::where('expires_at', '<', now())->count();
        $featured = static::featured()->count();

        $byType = static::selectRaw('type, COUNT(*) as count')
                        ->groupBy('type')
                        ->get()
                        ->keyBy('type');

        $byLocation = static::selectRaw('location, COUNT(*) as count')
                            ->groupBy('location')
                            ->get()
                            ->keyBy('location');

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'scheduled' => $scheduled,
            'expired' => $expired,
            'featured' => $featured,
            'by_type' => $byType,
            'by_location' => $byLocation,
        ];
    }

    public static function cleanupExpiredSliders()
    {
        return static::where('expires_at', '<', now())->delete();
    }

    public static function getSlidersForPage($page, $location = null)
    {
        $query = static::active()->published()->notExpired();
        
        if ($location) {
            $query->byLocation($location);
        }
        
        if ($page === 'homepage') {
            $query->whereIn('location', ['homepage', 'custom']);
        } elseif ($page === 'category') {
            $query->whereIn('location', ['category', 'custom']);
        } elseif ($page === 'product') {
            $query->whereIn('location', ['product', 'custom']);
        }
        
        return $query->get();
    }
}