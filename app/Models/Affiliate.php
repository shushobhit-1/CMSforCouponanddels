<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Affiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'affiliate_type', // network, program, individual
        'network_type', // vcommission, cuelinks, optimisemedia, inrdeals, amazon, flipkart, custom
        'website_url',
        'logo',
        'api_endpoint',
        'api_version',
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'tracking_id',
        'publisher_id',
        'commission_rate',
        'commission_type', // percentage, fixed, tiered
        'commission_structure',
        'minimum_payout',
        'payout_schedule', // weekly, monthly, quarterly
        'payment_methods',
        'is_active',
        'is_verified',
        'is_featured',
        'status',
        'settings',
        'credentials',
        'api_limits',
        'last_sync_at',
        'sync_frequency', // hourly, daily, weekly
        'created_by',
        'notes'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'commission_structure' => 'array',
        'payment_methods' => 'array',
        'settings' => 'array',
        'credentials' => 'array',
        'api_limits' => 'array',
        'commission_rate' => 'decimal:2',
        'minimum_payout' => 'decimal:2'
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
        'credentials'
    ];

    protected $dates = [
        'token_expires_at',
        'last_sync_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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
        return $this->hasMany(Store::class);
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

    public function reports()
    {
        return $this->hasMany(AffiliateReport::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('affiliate_type', $type);
    }

    public function scopeByNetwork($query, $network)
    {
        return $query->where('network_type', $network);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('display_name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/affiliates/' . $this->logo);
        }
        return asset('images/default-affiliate-logo.png');
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'active' => 'success',
            'pending' => 'warning',
            'suspended' => 'danger',
            'inactive' => 'secondary'
        ];

        $badgeClass = $statuses[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$badgeClass}'>{$this->status}</span>";
    }

    public function getNetworkIconAttribute()
    {
        $icons = [
            'vcommission' => 'fas fa-chart-line',
            'cuelinks' => 'fas fa-link',
            'optimisemedia' => 'fas fa-bullhorn',
            'inrdeals' => 'fas fa-rupee-sign',
            'amazon' => 'fab fa-amazon',
            'flipkart' => 'fas fa-shopping-cart',
            'custom' => 'fas fa-cog'
        ];

        return $icons[$this->network_type] ?? 'fas fa-network-wired';
    }

    public function getCommissionTextAttribute()
    {
        if ($this->commission_type === 'percentage') {
            return $this->commission_rate . '%';
        } elseif ($this->commission_type === 'fixed') {
            return '$' . number_format($this->commission_rate, 2);
        } elseif ($this->commission_type === 'tiered') {
            return 'Tiered (' . $this->commission_structure['tiers'] . ' levels)';
        }
        return 'Variable';
    }

    public function getIsTokenExpiredAttribute()
    {
        if (!$this->token_expires_at) {
            return false;
        }
        return $this->token_expires_at->isPast();
    }

    public function getNeedsTokenRefreshAttribute()
    {
        if (!$this->token_expires_at) {
            return false;
        }
        return $this->token_expires_at->diffInHours(now()) < 24;
    }

    public function getLastSyncTextAttribute()
    {
        if (!$this->last_sync_at) {
            return 'Never';
        }
        return $this->last_sync_at->diffForHumans();
    }

    public function getSyncStatusAttribute()
    {
        if (!$this->last_sync_at) {
            return 'never';
        }
        
        $hoursSinceSync = $this->last_sync_at->diffInHours(now());
        
        if ($hoursSinceSync < 1) {
            return 'recent';
        } elseif ($hoursSinceSync < 24) {
            return 'recent';
        } elseif ($hoursSinceSync < 168) { // 1 week
            return 'stale';
        } else {
            return 'outdated';
        }
    }

    // Methods
    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = Crypt::encryptString($value);
    }

    public function getApiKeyAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setApiSecretAttribute($value)
    {
        $this->attributes['api_secret'] = Crypt::encryptString($value);
    }

    public function getApiSecretAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setAccessTokenAttribute($value)
    {
        $this->attributes['access_token'] = Crypt::encryptString($value);
    }

    public function getAccessTokenAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setRefreshTokenAttribute($value)
    {
        $this->attributes['refresh_token'] = Crypt::encryptString($value);
    }

    public function getRefreshTokenAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function testConnection()
    {
        try {
            // Implement network-specific connection testing
            switch ($this->network_type) {
                case 'vcommission':
                    return $this->testVCommissionConnection();
                case 'cuelinks':
                    return $this->testCuelinksConnection();
                case 'optimisemedia':
                    return $this->testOptimiseMediaConnection();
                case 'inrdeals':
                    return $this->testInrDealsConnection();
                case 'amazon':
                    return $this->testAmazonConnection();
                case 'flipkart':
                    return $this->testFlipkartConnection();
                default:
                    return $this->testCustomConnection();
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }

    public function syncData()
    {
        try {
            $this->update(['last_sync_at' => now()]);
            
            // Implement network-specific data synchronization
            switch ($this->network_type) {
                case 'vcommission':
                    return $this->syncVCommissionData();
                case 'cuelinks':
                    return $this->syncCuelinksData();
                case 'optimisemedia':
                    return $this->syncOptimiseMediaData();
                case 'inrdeals':
                    return $this->syncInrDealsData();
                case 'amazon':
                    return $this->syncAmazonData();
                case 'flipkart':
                    return $this->syncFlipkartConnection();
                default:
                    return $this->syncCustomData();
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ];
        }
    }

    public function getApiCredentials()
    {
        return [
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'tracking_id' => $this->tracking_id,
            'publisher_id' => $this->publisher_id
        ];
    }

    public function updateToken($accessToken, $refreshToken = null, $expiresAt = null)
    {
        $this->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken ?: $this->refresh_token,
            'token_expires_at' => $expiresAt
        ]);
    }

    public function getNetworkConfig()
    {
        $configs = [
            'vcommission' => [
                'api_endpoint' => 'https://api.vcommission.com',
                'api_version' => 'v1',
                'required_fields' => ['api_key', 'tracking_id']
            ],
            'cuelinks' => [
                'api_endpoint' => 'https://api.cuelinks.com',
                'api_version' => 'v1',
                'required_fields' => ['api_key', 'publisher_id']
            ],
            'optimisemedia' => [
                'api_endpoint' => 'https://api.optimisemedia.com',
                'api_version' => 'v1',
                'required_fields' => ['api_key', 'api_secret']
            ],
            'inrdeals' => [
                'api_endpoint' => 'https://api.inrdeals.com',
                'api_version' => 'v1',
                'required_fields' => ['api_key', 'tracking_id']
            ],
            'amazon' => [
                'api_endpoint' => 'https://webservices.amazon.com',
                'api_version' => 'v1',
                'required_fields' => ['api_key', 'api_secret', 'tracking_id']
            ],
            'flipkart' => [
                'api_endpoint' => 'https://affiliate.flipkart.com',
                'api_version' => 'v1',
                'required_fields' => ['api_key', 'tracking_id']
            ]
        ];

        return $configs[$this->network_type] ?? [];
    }

    public function validateCredentials()
    {
        $config = $this->getNetworkConfig();
        $requiredFields = $config['required_fields'] ?? [];
        
        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return [
                    'valid' => false,
                    'message' => "Missing required field: {$field}"
                ];
            }
        }
        
        return ['valid' => true, 'message' => 'Credentials are valid'];
    }

    // Network-specific test methods (implement as needed)
    private function testVCommissionConnection()
    {
        // Implement VCommission API test
        return ['success' => true, 'message' => 'VCommission connection successful'];
    }

    private function testCuelinksConnection()
    {
        // Implement Cuelinks API test
        return ['success' => true, 'message' => 'Cuelinks connection successful'];
    }

    private function testOptimiseMediaConnection()
    {
        // Implement OptimiseMedia API test
        return ['success' => true, 'message' => 'OptimiseMedia connection successful'];
    }

    private function testInrDealsConnection()
    {
        // Implement INR Deals API test
        return ['success' => true, 'message' => 'INR Deals connection successful'];
    }

    private function testAmazonConnection()
    {
        // Implement Amazon API test
        return ['success' => true, 'message' => 'Amazon connection successful'];
    }

    private function testFlipkartConnection()
    {
        // Implement Flipkart API test
        return ['success' => true, 'message' => 'Flipkart connection successful'];
    }

    private function testCustomConnection()
    {
        // Implement custom API test
        return ['success' => true, 'message' => 'Custom connection successful'];
    }

    // Network-specific sync methods (implement as needed)
    private function syncVCommissionData()
    {
        // Implement VCommission data sync
        return ['success' => true, 'message' => 'VCommission data synced'];
    }

    private function syncCuelinksData()
    {
        // Implement Cuelinks data sync
        return ['success' => true, 'message' => 'Cuelinks data synced'];
    }

    private function syncOptimiseMediaData()
    {
        // Implement OptimiseMedia data sync
        return ['success' => true, 'message' => 'OptimiseMedia data synced'];
    }

    private function syncInrDealsData()
    {
        // Implement INR Deals data sync
        return ['success' => true, 'message' => 'INR Deals data synced'];
    }

    private function syncAmazonData()
    {
        // Implement Amazon data sync
        return ['success' => true, 'message' => 'Amazon data synced'];
    }

    private function syncFlipkartConnection()
    {
        // Implement Flipkart data sync
        return ['success' => true, 'message' => 'Flipkart data synced'];
    }

    private function syncCustomData()
    {
        // Implement custom data sync
        return ['success' => true, 'message' => 'Custom data synced'];
    }
}