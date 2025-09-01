<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateNetwork extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'api_endpoint',
        'api_key',
        'api_secret',
        'publisher_id',
        'tracking_id',
        'commission_rate',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'settings' => 'array',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    // Supported affiliate networks
    const NETWORKS = [
        'vcommission' => [
            'name' => 'vCommission',
            'endpoint' => 'https://api.vcommission.com',
            'requires' => ['api_key', 'publisher_id'],
        ],
        'cuelinks' => [
            'name' => 'CueLinks',
            'endpoint' => 'https://api.cuelinks.com',
            'requires' => ['api_key', 'publisher_id'],
        ],
        'optimisemedia' => [
            'name' => 'OptimiseMedia',
            'endpoint' => 'https://api.optimisemedia.com',
            'requires' => ['api_key', 'api_secret'],
        ],
        'inrdeals' => [
            'name' => 'INR Deals',
            'endpoint' => 'https://api.inrdeals.com',
            'requires' => ['api_key'],
        ],
        'amazon' => [
            'name' => 'Amazon Associates',
            'endpoint' => 'https://webservices.amazon.com/paapi5',
            'requires' => ['api_key', 'api_secret', 'tracking_id'],
        ],
        'flipkart' => [
            'name' => 'Flipkart Affiliate',
            'endpoint' => 'https://affiliate-api.flipkart.net',
            'requires' => ['api_key', 'tracking_id'],
        ],
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getNetworkConfigAttribute()
    {
        return self::NETWORKS[$this->slug] ?? null;
    }

    public function generateAffiliateLink($originalUrl, $productId = null)
    {
        switch ($this->slug) {
            case 'vcommission':
                return $this->generateVCommissionLink($originalUrl, $productId);
            case 'cuelinks':
                return $this->generateCueLinksLink($originalUrl, $productId);
            case 'amazon':
                return $this->generateAmazonLink($originalUrl, $productId);
            case 'flipkart':
                return $this->generateFlipkartLink($originalUrl, $productId);
            default:
                return $originalUrl;
        }
    }

    private function generateVCommissionLink($originalUrl, $productId = null)
    {
        return "https://tracking.vcommission.com/aff_c?offer_id={$this->publisher_id}&aff_id={$this->api_key}&url=" . urlencode($originalUrl);
    }

    private function generateCueLinksLink($originalUrl, $productId = null)
    {
        return "https://linksredirect.com/?pub_id={$this->publisher_id}&source=linkkit&url=" . urlencode($originalUrl);
    }

    private function generateAmazonLink($originalUrl, $productId = null)
    {
        if ($productId) {
            return "https://www.amazon.in/dp/{$productId}?tag={$this->tracking_id}";
        }
        return $originalUrl . (strpos($originalUrl, '?') ? '&' : '?') . "tag={$this->tracking_id}";
    }

    private function generateFlipkartLink($originalUrl, $productId = null)
    {
        return "https://dl.flipkart.com/dl/product/p/itm?pid={$productId}&affid={$this->tracking_id}";
    }

    public function fetchProducts($category = null, $limit = 20)
    {
        switch ($this->slug) {
            case 'amazon':
                return $this->fetchAmazonProducts($category, $limit);
            case 'flipkart':
                return $this->fetchFlipkartProducts($category, $limit);
            default:
                return [];
        }
    }

    private function fetchAmazonProducts($category, $limit)
    {
        // Implementation for Amazon Product Advertising API
        // This would require proper Amazon PA-API implementation
        return [];
    }

    private function fetchFlipkartProducts($category, $limit)
    {
        // Implementation for Flipkart Affiliate API
        return [];
    }
}