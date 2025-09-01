<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favorable_type',
        'favorable_id',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('favorable_type', $type);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    // Accessors
    public function getFavorableNameAttribute()
    {
        if ($this->favorable) {
            return $this->favorable->title ?? $this->favorable->name ?? 'Unknown';
        }
        return 'Unknown';
    }

    public function getFavorableUrlAttribute()
    {
        if ($this->favorable) {
            $type = strtolower(class_basename($this->favorable_type));
            $slug = $this->favorable->slug ?? $this->favorable->id;
            
            switch ($type) {
                case 'coupon':
                    return route('coupons.show', $slug);
                case 'deal':
                    return route('deals.show', $slug);
                case 'product':
                    return route('products.show', $slug);
                case 'store':
                    return route('stores.show', $slug);
                default:
                    return '#';
            }
        }
        return '#';
    }

    public function getFavorableImageAttribute()
    {
        if ($this->favorable) {
            return $this->favorable->image_url ?? asset('images/default.jpg');
        }
        return asset('images/default.jpg');
    }

    public function getFavorableTypeTextAttribute()
    {
        return match (class_basename($this->favorable_type)) {
            'Coupon' => 'Coupon',
            'Deal' => 'Deal',
            'Product' => 'Product',
            'Store' => 'Store',
            default => 'Item'
        };
    }

    public function getFavorableTypeIconAttribute()
    {
        return match (class_basename($this->favorable_type)) {
            'Coupon' => 'fas fa-ticket-alt',
            'Deal' => 'fas fa-tags',
            'Product' => 'fas fa-box',
            'Store' => 'fas fa-store',
            default => 'fas fa-heart'
        };
    }

    public function getFavorableTypeColorAttribute()
    {
        return match (class_basename($this->favorable_type)) {
            'Coupon' => 'success',
            'Deal' => 'warning',
            'Product' => 'primary',
            'Store' => 'info',
            default => 'secondary'
        };
    }

    // Methods
    public static function toggle($userId, $favorableType, $favorableId, $notes = null)
    {
        $existing = static::where('user_id', $userId)
                          ->where('favorable_type', $favorableType)
                          ->where('favorable_id', $favorableId)
                          ->first();

        if ($existing) {
            $existing->delete();
            
            // Decrement favorite count on the favorable item
            $favorable = $favorableType::find($favorableId);
            if ($favorable && method_exists($favorable, 'decrementFavoriteCount')) {
                $favorable->decrementFavoriteCount();
            }
            
            return false; // Removed from favorites
        } else {
            static::create([
                'user_id' => $userId,
                'favorable_type' => $favorableType,
                'favorable_id' => $favorableId,
                'notes' => $notes,
            ]);
            
            // Increment favorite count on the favorable item
            $favorable = $favorableType::find($favorableId);
            if ($favorable && method_exists($favorable, 'incrementFavoriteCount')) {
                $favorable->incrementFavoriteCount();
            }
            
            return true; // Added to favorites
        }
    }

    public static function isFavorited($userId, $favorableType, $favorableId)
    {
        return static::where('user_id', $userId)
                    ->where('favorable_type', $favorableType)
                    ->where('favorable_id', $favorableId)
                    ->exists();
    }

    public static function getUserFavorites($userId, $type = null, $limit = null)
    {
        $query = static::with('favorable')->where('user_id', $userId);
        
        if ($type) {
            $query->where('favorable_type', $type);
        }
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->latest()->get();
    }

    public static function getFavoriteCount($favorableType, $favorableId)
    {
        return static::where('favorable_type', $favorableType)
                    ->where('favorable_id', $favorableId)
                    ->count();
    }

    public static function getPopularFavorites($type = null, $limit = 10)
    {
        $query = static::selectRaw('favorable_type, favorable_id, COUNT(*) as favorite_count')
                       ->groupBy('favorable_type', 'favorable_id')
                       ->orderBy('favorite_count', 'desc');
        
        if ($type) {
            $query->where('favorable_type', $type);
        }
        
        return $query->limit($limit)->get();
    }

    public static function getRecentFavorites($type = null, $limit = 10)
    {
        $query = static::with('favorable')->latest();
        
        if ($type) {
            $query->where('favorable_type', $type);
        }
        
        return $query->limit($limit)->get();
    }

    public static function getUserFavoriteStats($userId)
    {
        $stats = static::where('user_id', $userId)
                      ->selectRaw('favorable_type, COUNT(*) as count')
                      ->groupBy('favorable_type')
                      ->get()
                      ->keyBy('favorable_type');

        return [
            'coupons' => $stats->get('App\Models\Coupon')->count ?? 0,
            'deals' => $stats->get('App\Models\Deal')->count ?? 0,
            'products' => $stats->get('App\Models\Product')->count ?? 0,
            'stores' => $stats->get('App\Models\Store')->count ?? 0,
            'total' => $stats->sum('count'),
        ];
    }

    public static function getFavoritesByDateRange($userId, $startDate, $endDate, $type = null)
    {
        $query = static::where('user_id', $userId)
                      ->whereBetween('created_at', [$startDate, $endDate]);
        
        if ($type) {
            $query->where('favorable_type', $type);
        }
        
        return $query->with('favorable')->latest()->get();
    }

    public static function searchFavorites($userId, $search, $type = null)
    {
        $query = static::where('user_id', $userId)
                      ->whereHasMorph('favorable', [
                          'App\Models\Coupon',
                          'App\Models\Deal',
                          'App\Models\Product',
                          'App\Models\Store'
                      ], function ($q) use ($search) {
                          $q->where('title', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                      });
        
        if ($type) {
            $query->where('favorable_type', $type);
        }
        
        return $query->with('favorable')->latest()->get();
    }

    public static function getFavoritesForNotification($userId, $type = null)
    {
        $query = static::where('user_id', $userId)
                      ->whereHas('favorable', function ($q) {
                          $q->where('status', 'active');
                      });
        
        if ($type) {
            $query->where('favorable_type', $type);
        }
        
        return $query->with('favorable')->get();
    }

    public static function cleanupOrphanedFavorites()
    {
        // Remove favorites for deleted items
        $orphanedCoupons = static::where('favorable_type', 'App\Models\Coupon')
                                ->whereDoesntHave('favorable')
                                ->delete();
        
        $orphanedDeals = static::where('favorable_type', 'App\Models\Deal')
                               ->whereDoesntHave('favorable')
                               ->delete();
        
        $orphanedProducts = static::where('favorable_type', 'App\Models\Product')
                                 ->whereDoesntHave('favorable')
                                 ->delete();
        
        $orphanedStores = static::where('favorable_type', 'App\Models\Store')
                                ->whereDoesntHave('favorable')
                                ->delete();
        
        return [
            'coupons' => $orphanedCoupons,
            'deals' => $orphanedDeals,
            'products' => $orphanedProducts,
            'stores' => $orphanedStores,
        ];
    }
}