<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'preferences',
        'social_links',
        'bio',
        'location',
        'website',
        'company',
        'position',
        'birth_date',
        'gender',
        'timezone',
        'language',
        'currency',
        'notification_settings'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'preferences' => 'array',
        'social_links' => 'array',
        'notification_settings' => 'array'
    ];

    // Relationships
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'created_by');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'created_by');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'created_by');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    public function sliders()
    {
        return $this->hasMany(Slider::class, 'created_by');
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'created_by');
    }

    public function settings()
    {
        return $this->hasMany(Setting::class, 'updated_by');
    }

    public function themes()
    {
        return $this->hasMany(Theme::class, 'updated_by');
    }

    public function affiliates()
    {
        return $this->hasMany(Affiliate::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmins($query)
    {
        return $query->role('admin');
    }

    public function scopeUsers($query)
    {
        return $query->role('user');
    }

    // Accessors & Mutators
    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getIsAdminAttribute()
    {
        return $this->hasRole('admin');
    }

    public function getIsUserAttribute()
    {
        return $this->hasRole('user');
    }

    // Methods
    public function markAsOnline()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function updatePreferences($preferences)
    {
        $this->update(['preferences' => array_merge($this->preferences ?? [], $preferences)]);
    }

    public function updateNotificationSettings($settings)
    {
        $this->update(['notification_settings' => array_merge($this->notification_settings ?? [], $settings)]);
    }

    public function hasFavorite($favorable)
    {
        return $this->favorites()->where('favorable_type', get_class($favorable))
            ->where('favorable_id', $favorable->id)->exists();
    }

    public function toggleFavorite($favorable)
    {
        if ($this->hasFavorite($favorable)) {
            $this->favorites()->where('favorable_type', get_class($favorable))
                ->where('favorable_id', $favorable->id)->delete();
            return false;
        } else {
            $this->favorites()->create([
                'favorable_type' => get_class($favorable),
                'favorable_id' => $favorable->id
            ]);
            return true;
        }
    }
}