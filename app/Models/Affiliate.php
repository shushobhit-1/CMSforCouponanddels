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
        'network_type', // vcommission, cuelinks, optimisemedia, inrdeals, amazon, flipkart, custom
        'description',
        'logo',
        'website_url',
        'api_endpoint',
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'tracking_id',
        'affiliate_id',
        'username',
        'password',
        'commission_rate',
        'commission_type', // percentage, fixed
        'minimum_payout',
        'payout_schedule', // weekly, monthly, quarterly
        'status', // active, inactive, pending, suspended
        'verification_status', // pending, verified, rejected
        'verification_date',
        'verification_notes',
        'contact_person',
        'contact_email',
        'contact_phone',
        'support_email',
        'support_phone',
        'support_url',
        'terms_url',
        'privacy_url',
        'faq_url',
        'api_documentation_url',
        'features', // JSON array of features
        'restrictions', // JSON array of restrictions
        'categories', // JSON array of supported categories
        'countries', // JSON array of supported countries
        'currencies', // JSON array of supported currencies
        'languages', // JSON array of supported languages
        'payment_methods', // JSON array of payment methods
        'reporting_frequency', // daily, weekly, monthly
        'last_report_date',
        'last_sync_date',
        'sync_status', // success, failed, pending
        'sync_notes',
        'error_count',
        'last_error',
        'last_error_date',
        'performance_rating',
        'trust_score',
        'created_by',
        'updated_by',
        'activated_at',
        'deactivated_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'verification_date' => 'datetime',
        'last_report_date' => 'datetime',
        'last_sync_date' => 'datetime',
        'last_error_date' => 'datetime',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'commission_rate' => 'decimal:2',
        'minimum_payout' => 'decimal:2',
        'performance_rating' => 'decimal:1',
        'trust_score' => 'integer',
        'error_count' => 'integer',
        'features' => 'array',
        'restrictions' => 'array',
        'categories' => 'array',
        'countries' => 'array',
        'currencies' => 'array',
        'languages' => 'array',
        'payment_methods' => 'array',
    ];

    protected $dates = [
        'token_expires_at',
        'verification_date',
        'last_report_date',
        'last_sync_date',
        'last_error_date',
        'activated_at',
        'deactivated_at',
        'deleted_at',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
        'access_token',
        'refresh_token',
        'password',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
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

    public function reports()
    {
        return $this->hasMany(AffiliateReport::class);
    }

    public function transactions()
    {
        return $this->hasMany(AffiliateTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeByNetworkType($query, $type)
    {
        return $query->where('network_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVerificationStatus($query, $status)
    {
        return $query->where('verification_status', $status);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->whereJsonContains('categories', $category);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->whereJsonContains('countries', $country);
    }

    public function scopeByCurrency($query, $currency)
    {
        return $query->whereJsonContains('currencies', $currency);
    }

    public function scopeHighPerformance($query, $minRating = 4.0)
    {
        return $query->where('performance_rating', '>=', $minRating);
    }

    public function scopeHighTrust($query, $minScore = 80)
    {
        return $query->where('trust_score', '>=', $minScore);
    }

    public function scopeRecentSync($query, $days = 7)
    {
        return $query->where('last_sync_date', '>=', now()->subDays($days));
    }

    public function scopeNeedsSync($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('last_sync_date')
              ->orWhere('last_sync_date', '<', now()->subDays(1));
        });
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('network_type', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getIsVerifiedAttribute()
    {
        return $this->verification_status === 'verified';
    }

    public function getIsPendingAttribute()
    {
        return $this->verification_status === 'pending';
    }

    public function getIsRejectedAttribute()
    {
        return $this->verification_status === 'rejected';
    }

    public function getIsSuspendedAttribute()
    {
        return $this->status === 'suspended';
    }

    public function getIsTokenExpiredAttribute()
    {
        return $this->token_expires_at && $this->token_expires_at < now();
    }

    public function getNeedsTokenRefreshAttribute()
    {
        return $this->token_expires_at && $this->token_expires_at < now()->addDays(1);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/affiliates/' . $this->logo);
        }
        return asset('images/default-affiliate-logo.png');
    }

    public function getNetworkTypeTextAttribute()
    {
        return match ($this->network_type) {
            'vcommission' => 'vCommission',
            'cuelinks' => 'Cuelinks',
            'optimisemedia' => 'OptimiseMedia',
            'inrdeals' => 'INR Deals',
            'amazon' => 'Amazon Associates',
            'flipkart' => 'Flipkart Affiliate',
            'custom' => 'Custom Network',
            default => ucfirst($this->network_type)
        };
    }

    public function getNetworkTypeIconAttribute()
    {
        return match ($this->network_type) {
            'vcommission' => 'fas fa-chart-line',
            'cuelinks' => 'fas fa-link',
            'optimisemedia' => 'fas fa-bullhorn',
            'inrdeals' => 'fas fa-rupee-sign',
            'amazon' => 'fab fa-amazon',
            'flipkart' => 'fas fa-shopping-bag',
            'custom' => 'fas fa-network-wired',
            default => 'fas fa-network-wired'
        };
    }

    public function getNetworkTypeColorAttribute()
    {
        return match ($this->network_type) {
            'vcommission' => 'primary',
            'cuelinks' => 'success',
            'optimisemedia' => 'info',
            'inrdeals' => 'warning',
            'amazon' => 'dark',
            'flipkart' => 'danger',
            'custom' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
            'suspended' => 'Suspended',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'pending' => 'warning',
            'suspended' => 'danger',
            default => 'secondary'
        };
    }

    public function getVerificationStatusTextAttribute()
    {
        return match ($this->verification_status) {
            'pending' => 'Pending Verification',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    public function getVerificationStatusColorAttribute()
    {
        return match ($this->verification_status) {
            'pending' => 'warning',
            'verified' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function getCommissionTextAttribute()
    {
        if ($this->commission_type === 'percentage') {
            return "{$this->commission_rate}%";
        } elseif ($this->commission_type === 'fixed') {
            return "₹{$this->commission_rate}";
        }
        return 'Variable';
    }

    public function getFormattedMinimumPayoutAttribute()
    {
        return "₹{$this->minimum_payout}";
    }

    public function getPayoutScheduleTextAttribute()
    {
        return match ($this->payout_schedule) {
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            default => 'Variable'
        };
    }

    public function getFormattedPerformanceRatingAttribute()
    {
        if ($this->performance_rating) {
            return number_format($this->performance_rating, 1) . '/5.0';
        }
        return 'Not Rated';
    }

    public function getFormattedTrustScoreAttribute()
    {
        if ($this->trust_score) {
            return "{$this->trust_score}/100";
        }
        return 'Not Rated';
    }

    public function getLastSyncTextAttribute()
    {
        if ($this->last_sync_date) {
            return $this->last_sync_date->diffForHumans();
        }
        return 'Never';
    }

    public function getLastReportTextAttribute()
    {
        if ($this->last_report_date) {
            return $this->last_report_date->diffForHumans();
        }
        return 'Never';
    }

    public function getLastErrorTextAttribute()
    {
        if ($this->last_error_date) {
            return $this->last_error_date->diffForHumans();
        }
        return 'Never';
    }

    public function getSyncStatusTextAttribute()
    {
        return match ($this->sync_status) {
            'success' => 'Successful',
            'failed' => 'Failed',
            'pending' => 'Pending',
            default => 'Unknown'
        };
    }

    public function getSyncStatusColorAttribute()
    {
        return match ($this->sync_status) {
            'success' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary'
        };
    }

    public function getFeaturesListAttribute()
    {
        if ($this->features && is_array($this->features)) {
            return implode(', ', $this->features);
        }
        return 'No features specified';
    }

    public function getRestrictionsListAttribute()
    {
        if ($this->restrictions && is_array($this->restrictions)) {
            return implode(', ', $this->restrictions);
        }
        return 'No restrictions';
    }

    public function getCategoriesListAttribute()
    {
        if ($this->categories && is_array($this->categories)) {
            return implode(', ', $this->categories);
        }
        return 'All categories';
    }

    public function getCountriesListAttribute()
    {
        if ($this->countries && is_array($this->countries)) {
            return implode(', ', $this->countries);
        }
        return 'All countries';
    }

    public function getCurrenciesListAttribute()
    {
        if ($this->currencies && is_array($this->currencies)) {
            return implode(', ', $this->currencies);
        }
        return 'INR';
    }

    public function getLanguagesListAttribute()
    {
        if ($this->languages && is_array($this->languages)) {
            return implode(', ', $this->languages);
        }
        return 'English';
    }

    public function getPaymentMethodsListAttribute()
    {
        if ($this->payment_methods && is_array($this->payment_methods)) {
            return implode(', ', $this->payment_methods);
        }
        return 'Bank Transfer';
    }

    // Methods
    public function activate()
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    public function deactivate()
    {
        $this->update([
            'status' => 'inactive',
            'deactivated_at' => now(),
        ]);
    }

    public function suspend()
    {
        $this->update([
            'status' => 'suspended',
            'deactivated_at' => now(),
        ]);
    }

    public function verify()
    {
        $this->update([
            'verification_status' => 'verified',
            'verification_date' => now(),
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'verification_status' => 'rejected',
            'verification_notes' => $notes,
        ]);
    }

    public function updateToken($accessToken, $refreshToken = null, $expiresAt = null)
    {
        $this->update([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken ?: $this->refresh_token,
            'token_expires_at' => $expiresAt,
        ]);
    }

    public function updateSyncStatus($status, $notes = null)
    {
        $this->update([
            'sync_status' => $status,
            'sync_notes' => $notes,
            'last_sync_date' => now(),
        ]);
    }

    public function updateLastReport()
    {
        $this->update(['last_report_date' => now()]);
    }

    public function recordError($error)
    {
        $this->update([
            'last_error' => $error,
            'last_error_date' => now(),
            'error_count' => $this->error_count + 1,
        ]);
    }

    public function resetErrorCount()
    {
        $this->update(['error_count' => 0]);
    }

    public function updatePerformanceRating($rating)
    {
        $this->update(['performance_rating' => $rating]);
    }

    public function updateTrustScore($score)
    {
        $this->update(['trust_score' => $score]);
    }

    public function hasFeature($feature)
    {
        return $this->features && in_array($feature, $this->features);
    }

    public function supportsCategory($category)
    {
        return !$this->categories || in_array($category, $this->categories);
    }

    public function supportsCountry($country)
    {
        return !$this->countries || in_array($country, $this->countries);
    }

    public function supportsCurrency($currency)
    {
        return !$this->currencies || in_array($currency, $this->currencies);
    }

    public function supportsLanguage($language)
    {
        return !$this->languages || in_array($language, $this->languages);
    }

    public function supportsPaymentMethod($method)
    {
        return !$this->payment_methods || in_array($method, $this->payment_methods);
    }

    public function canSync()
    {
        return $this->is_active && 
               $this->is_verified && 
               $this->api_key && 
               $this->api_endpoint;
    }

    public function needsTokenRefresh()
    {
        return $this->token_expires_at && $this->token_expires_at < now()->addDays(1);
    }

    public function getApiCredentials()
    {
        return [
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
            'access_token' => $this->access_token,
            'refresh_token' => $this->refresh_token,
            'tracking_id' => $this->tracking_id,
            'affiliate_id' => $this->affiliate_id,
        ];
    }

    public function getApiConfig()
    {
        return [
            'endpoint' => $this->api_endpoint,
            'network_type' => $this->network_type,
            'credentials' => $this->getApiCredentials(),
            'features' => $this->features,
            'restrictions' => $this->restrictions,
        ];
    }

    // Static Methods
    public static function getActiveNetworks()
    {
        return static::active()->verified()->get();
    }

    public static function getNetworksByType($type)
    {
        return static::active()->verified()->byNetworkType($type)->get();
    }

    public static function getNetworksByCategory($category)
    {
        return static::active()->verified()->byCategory($category)->get();
    }

    public static function getHighPerformanceNetworks($minRating = 4.0)
    {
        return static::active()->verified()->highPerformance($minRating)->get();
    }

    public static function getNetworksNeedingSync()
    {
        return static::active()->verified()->needsSync()->get();
    }

    public static function getNetworkStats()
    {
        $total = static::count();
        $active = static::active()->count();
        $verified = static::verified()->count();
        $pending = static::byVerificationStatus('pending')->count();
        $suspended = static::byStatus('suspended')->count();

        $byType = static::selectRaw('network_type, COUNT(*) as count')
                        ->groupBy('network_type')
                        ->get()
                        ->keyBy('network_type');

        return [
            'total' => $total,
            'active' => $active,
            'verified' => $verified,
            'pending' => $pending,
            'suspended' => $suspended,
            'by_type' => $byType,
        ];
    }

    public static function cleanupExpiredTokens()
    {
        return static::where('token_expires_at', '<', now())
                    ->update([
                        'access_token' => null,
                        'refresh_token' => null,
                        'token_expires_at' => null,
                    ]);
    }

    public static function getNetworksForSync()
    {
        return static::active()
                    ->verified()
                    ->whereNotNull('api_key')
                    ->whereNotNull('api_endpoint')
                    ->where(function ($q) {
                        $q->whereNull('last_sync_date')
                          ->orWhere('last_sync_date', '<', now()->subDays(1));
                    })
                    ->get();
    }
}