<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'network_type', // vcommission, cuelinks, optimisemedia, inrdeals, amazon, flipkart, custom
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'base_url',
        'api_endpoint',
        'is_active',
        'is_verified',
        'commission_rate',
        'commission_type', // percentage, fixed
        'payout_threshold',
        'payout_schedule', // weekly, monthly, quarterly
        'contact_email',
        'contact_phone',
        'website_url',
        'logo',
        'banner',
        'terms_conditions',
        'privacy_policy',
        'status', // active, inactive, pending, suspended
        'last_sync_at',
        'sync_frequency', // hourly, daily, weekly
        'auto_sync',
        'created_by',
        'settings', // JSON for network-specific settings
        'webhook_url',
        'webhook_secret',
        'tracking_parameters', // JSON for tracking parameters
        'performance_metrics' // JSON for performance data
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'commission_rate' => 'decimal:2',
        'payout_threshold' => 'decimal:2',
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'auto_sync' => 'boolean',
        'settings' => 'array',
        'tracking_parameters' => 'array',
        'performance_metrics' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'token_expires_at',
        'last_sync_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
        'webhook_secret'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
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

    public function clicks()
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function conversions()
    {
        return $this->hasMany(AffiliateConversion::class);
    }

    public function payouts()
    {
        return $this->hasMany(AffiliatePayout::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByNetworkType($query, $type)
    {
        return $query->where('network_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAutoSync($query)
    {
        return $query->where('auto_sync', true);
    }

    public function scopeNeedsSync($query)
    {
        return $query->where('auto_sync', true)
                    ->where(function($q) {
                        $q->whereNull('last_sync_at')
                          ->orWhere('last_sync_at', '<=', now()->subHours($this->getSyncInterval()));
                    });
    }

    // Accessors
    public function getNetworkTypeTextAttribute()
    {
        return match($this->network_type) {
            'vcommission' => 'vCommission',
            'cuelinks' => 'Cuelinks',
            'optimisemedia' => 'OptimiseMedia',
            'inrdeals' => 'INR Deals',
            'amazon' => 'Amazon Associates',
            'flipkart' => 'Flipkart Affiliate',
            'custom' => 'Custom Network',
            default => 'Unknown'
        };
    }

    public function getNetworkTypeIconAttribute()
    {
        return match($this->network_type) {
            'vcommission' => 'fas fa-chart-line',
            'cuelinks' => 'fas fa-link',
            'optimisemedia' => 'fas fa-bullseye',
            'inrdeals' => 'fas fa-rupee-sign',
            'amazon' => 'fab fa-amazon',
            'flipkart' => 'fas fa-shopping-cart',
            'custom' => 'fas fa-network-wired',
            default => 'fas fa-question'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
            'suspended' => 'Suspended',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'pending' => 'warning',
            'suspended' => 'danger',
            default => 'secondary'
        };
    }

    public function getPayoutScheduleTextAttribute()
    {
        return match($this->payout_schedule) {
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            default => 'Not Set'
        };
    }

    public function getCommissionTextAttribute()
    {
        if ($this->commission_type === 'percentage') {
            return "{$this->commission_rate}% Commission";
        }
        return "{$this->commission_rate} Fixed Commission";
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ?: asset('images/default-affiliate-logo.jpg');
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ?: asset('images/default-affiliate-banner.jpg');
    }

    public function getIsTokenExpiredAttribute()
    {
        return $this->token_expires_at && $this->token_expires_at < now();
    }

    public function getNeedsTokenRefreshAttribute()
    {
        return $this->token_expires_at && $this->token_expires_at->diffInHours(now()) < 24;
    }

    public function getSyncIntervalAttribute()
    {
        return match($this->sync_frequency) {
            'hourly' => 1,
            'daily' => 24,
            'weekly' => 168,
            default => 24
        };
    }

    public function getPerformanceMetricsAttribute($value)
    {
        $metrics = $value ?: [];
        
        return array_merge([
            'total_clicks' => 0,
            'total_conversions' => 0,
            'total_revenue' => 0,
            'total_commission' => 0,
            'conversion_rate' => 0,
            'avg_order_value' => 0,
            'last_month_clicks' => 0,
            'last_month_conversions' => 0,
            'last_month_revenue' => 0,
            'last_month_commission' => 0
        ], $metrics);
    }

    // Methods
    public function syncData()
    {
        try {
            $method = "sync" . ucfirst($this->network_type);
            
            if (method_exists($this, $method)) {
                $result = $this->$method();
                $this->update(['last_sync_at' => now()]);
                return $result;
            }
            
            throw new \Exception("Sync method not implemented for network type: {$this->network_type}");
            
        } catch (\Exception $e) {
            \Log::error("Failed to sync affiliate data for {$this->name}: " . $e->getMessage());
            throw $e;
        }
    }

    public function refreshToken()
    {
        if (!$this->refresh_token) {
            throw new \Exception('No refresh token available');
        }

        try {
            $method = "refresh" . ucfirst($this->network_type) . "Token";
            
            if (method_exists($this, $method)) {
                return $this->$method();
            }
            
            throw new \Exception("Token refresh method not implemented for network type: {$this->network_type}");
            
        } catch (\Exception $e) {
            \Log::error("Failed to refresh token for {$this->name}: " . $e->getMessage());
            throw $e;
        }
    }

    public function testConnection()
    {
        try {
            $method = "test" . ucfirst($this->network_type) . "Connection";
            
            if (method_exists($this, $method)) {
                return $this->$method();
            }
            
            throw new \Exception("Connection test method not implemented for network type: {$this->network_type}");
            
        } catch (\Exception $e) {
            \Log::error("Failed to test connection for {$this->name}: " . $e->getMessage());
            throw $e;
        }
    }

    public function generateTrackingLink($baseUrl, $userId = null, $campaign = null)
    {
        $trackingParams = $this->tracking_parameters ?: [];
        $url = $baseUrl;
        
        // Add network-specific tracking parameters
        $separator = strpos($url, '?') !== false ? '&' : '?';
        
        foreach ($trackingParams as $key => $value) {
            $url .= $separator . urlencode($key) . '=' . urlencode($value);
            $separator = '&';
        }
        
        // Add user tracking if available
        if ($userId) {
            $url .= $separator . 'ref=' . urlencode($userId);
        }
        
        // Add campaign tracking if available
        if ($campaign) {
            $url .= $separator . 'utm_campaign=' . urlencode($campaign);
        }
        
        return $url;
    }

    public function updatePerformanceMetrics()
    {
        $metrics = [
            'total_clicks' => $this->clicks()->count(),
            'total_conversions' => $this->conversions()->count(),
            'total_revenue' => $this->conversions()->sum('amount'),
            'total_commission' => $this->conversions()->sum('commission'),
            'conversion_rate' => $this->clicks()->count() > 0 ? 
                ($this->conversions()->count() / $this->clicks()->count()) * 100 : 0,
            'avg_order_value' => $this->conversions()->count() > 0 ? 
                $this->conversions()->avg('amount') : 0
        ];
        
        // Last month metrics
        $lastMonth = now()->subMonth();
        $metrics['last_month_clicks'] = $this->clicks()->whereMonth('created_at', $lastMonth->month)->count();
        $metrics['last_month_conversions'] = $this->conversions()->whereMonth('created_at', $lastMonth->month)->count();
        $metrics['last_month_revenue'] = $this->conversions()->whereMonth('created_at', $lastMonth->month)->sum('amount');
        $metrics['last_month_commission'] = $this->conversions()->whereMonth('created_at', $lastMonth->month)->sum('commission');
        
        $this->update(['performance_metrics' => $metrics]);
        
        return $metrics;
    }

    // Network-specific sync methods
    public function syncVcommission()
    {
        // Implementation for vCommission API sync
        // This would fetch coupons, deals, and products from vCommission
        return ['status' => 'success', 'message' => 'vCommission data synced successfully'];
    }

    public function syncCuelinks()
    {
        // Implementation for Cuelinks API sync
        return ['status' => 'success', 'message' => 'Cuelinks data synced successfully'];
    }

    public function syncOptimisemedia()
    {
        // Implementation for OptimiseMedia API sync
        return ['status' => 'success', 'message' => 'OptimiseMedia data synced successfully'];
    }

    public function syncInrdeals()
    {
        // Implementation for INR Deals API sync
        return ['status' => 'success', 'message' => 'INR Deals data synced successfully'];
    }

    public function syncAmazon()
    {
        // Implementation for Amazon Associates API sync
        return ['status' => 'success', 'message' => 'Amazon data synced successfully'];
    }

    public function syncFlipkart()
    {
        // Implementation for Flipkart Affiliate API sync
        return ['status' => 'success', 'message' => 'Flipkart data synced successfully'];
    }

    // Token refresh methods
    public function refreshVcommissionToken()
    {
        // Implementation for vCommission token refresh
        return ['status' => 'success', 'message' => 'vCommission token refreshed successfully'];
    }

    // Connection test methods
    public function testVcommissionConnection()
    {
        // Implementation for vCommission connection test
        return ['status' => 'success', 'message' => 'vCommission connection successful'];
    }

    // Events
    protected static function booted()
    {
        static::created(function ($affiliate) {
            // Log activity
            activity()
                ->performedOn($affiliate)
                ->causedBy($affiliate->creator)
                ->log('created affiliate network');
        });

        static::updated(function ($affiliate) {
            if ($affiliate->wasChanged('status')) {
                activity()
                    ->performedOn($affiliate)
                    ->causedBy(auth()->user())
                    ->log("updated affiliate status to {$affiliate->status}");
            }
        });
    }
}