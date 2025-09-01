<?php

namespace App\Services;

use App\Models\Affiliate;

interface AffiliateAdapterInterface
{
    /**
     * Test connection to the affiliate network
     */
    public function testConnection(Affiliate $affiliate): bool;

    /**
     * Sync products from the affiliate network
     */
    public function syncProducts(Affiliate $affiliate): array;

    /**
     * Generate affiliate link with tracking parameters
     */
    public function generateAffiliateLink(string $originalUrl, Affiliate $affiliate, array $params = []): string;

    /**
     * Get commission rates for different categories
     */
    public function getCommissionRates(Affiliate $affiliate): array;

    /**
     * Get click statistics for a date range
     */
    public function getClickStatistics(Affiliate $affiliate, string $startDate, string $endDate): array;

    /**
     * Get product details by ID
     */
    public function getProductDetails(Affiliate $affiliate, string $productId): ?array;

    /**
     * Search products with filters
     */
    public function searchProducts(Affiliate $affiliate, array $filters = []): array;

    /**
     * Get available categories
     */
    public function getCategories(Affiliate $affiliate): array;

    /**
     * Get conversion tracking data
     */
    public function getConversions(Affiliate $affiliate, string $startDate, string $endDate): array;
}