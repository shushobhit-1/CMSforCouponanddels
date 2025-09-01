<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Deal extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'original_price',
        'deal_price',
        'discount_percentage',
        'starts_at',
        'expires_at',
        'is_featured',
        'is_active',
        'affiliate_url',
        'store_id',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'original_price' => 'decimal:2',
        'deal_price' => 'decimal:2',
        'discount_percentage' => 'integer',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
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

    public function scopeExpiring($query)
    {
        return $query->where('expires_at', '>', now())
                    ->where('expires_at', '<=', now()->addDays(3));
    }

    // Accessors
    public function getSavingsAttribute()
    {
        return $this->original_price - $this->deal_price;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('images') ?: '/images/default-deal.png';
    }
}
