<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id',
        'notes',
        'is_public',
        'created_at'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('favoritable_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getFavoritableNameAttribute()
    {
        if ($this->favoritable) {
            return $this->favoritable->title ?? $this->favoritable->name ?? 'Unknown';
        }
        return 'Unknown';
    }

    public function getFavoritableTypeTextAttribute()
    {
        return match($this->favoritable_type) {
            Store::class => 'Store',
            Coupon::class => 'Coupon',
            Deal::class => 'Deal',
            Product::class => 'Product',
            default => 'Unknown'
        };
    }

    public function getFavoritableUrlAttribute()
    {
        if (!$this->favoritable) return '#';
        
        return match($this->favoritable_type) {
            Store::class => route('stores.show', $this->favoritable->slug),
            Coupon::class => route('coupons.show', $this->favoritable->slug),
            Deal::class => route('deals.show', $this->favoritable->slug),
            Product::class => route('products.show', $this->favoritable->slug),
            default => '#'
        };
    }

    public function getFavoritableImageAttribute()
    {
        if (!$this->favoritable) return asset('images/default-placeholder.jpg');
        
        return match($this->favoritable_type) {
            Store::class => $this->favoritable->logo_url,
            Coupon::class => $this->favoritable->image_url,
            Deal::class => $this->favoritable->image_url,
            Product::class => $this->favoritable->image_url,
            default => asset('images/default-placeholder.jpg')
        };
    }

    // Methods
    public static function toggle($userId, $favoritableType, $favoritableId, $notes = null, $isPublic = false)
    {
        $existing = static::where('user_id', $userId)
                          ->where('favoritable_type', $favoritableType)
                          ->where('favoritable_id', $favoritableId)
                          ->first();

        if ($existing) {
            $existing->delete();
            return ['action' => 'removed', 'favorite' => null];
        }

        $favorite = static::create([
            'user_id' => $userId,
            'favoritable_type' => $favoritableType,
            'favoritable_id' => $favoritableId,
            'notes' => $notes,
            'is_public' => $isPublic
        ]);

        return ['action' => 'added', 'favorite' => $favorite];
    }

    public static function isFavorited($userId, $favoritableType, $favoritableId)
    {
        return static::where('user_id', $userId)
                    ->where('favoritable_type', $favoritableType)
                    ->where('favoritable_id', $favoritableId)
                    ->exists();
    }

    public static function getFavoritesByType($userId, $type)
    {
        return static::where('user_id', $userId)
                    ->where('favoritable_type', $type)
                    ->with('favoritable')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    public static function getFavoriteCount($userId, $type = null)
    {
        $query = static::where('user_id', $userId);
        
        if ($type) {
            $query->where('favoritable_type', $type);
        }
        
        return $query->count();
    }

    public static function getPublicFavorites($type = null, $limit = 20)
    {
        $query = static::where('is_public', true)
                      ->with(['user', 'favoritable'])
                      ->orderBy('created_at', 'desc');
        
        if ($type) {
            $query->where('favoritable_type', $type);
        }
        
        return $query->limit($limit)->get();
    }

    // Events
    protected static function booted()
    {
        static::created(function ($favorite) {
            // Log activity
            activity()
                ->performedOn($favorite->favoritable)
                ->causedBy($favorite->user)
                ->log('added to favorites');
        });

        static::deleted(function ($favorite) {
            // Log activity
            activity()
                ->performedOn($favorite->favoritable)
                ->causedBy($favorite->user)
                ->log('removed from favorites');
        });
    }
}