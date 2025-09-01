<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'click_id',
        'order_id',
        'order_value',
        'commission_amount',
        'commission_rate',
        'conversion_date',
        'status',
        'tracking_id',
        'affiliate_network',
        'ip_address',
        'user_agent',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
        'fbclid',
        'msclkid',
        'conversion_data',
        'notes'
    ];

    protected $casts = [
        'conversion_date' => 'datetime',
        'order_value' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'conversion_data' => 'array'
    ];

    protected $dates = [
        'conversion_date',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function click(): BelongsTo
    {
        return $this->belongsTo(CouponClick::class, 'click_id');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('conversion_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('conversion_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('conversion_date', now()->month);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCoupon($query, $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByAffiliateNetwork($query, $network)
    {
        return $query->where('affiliate_network', $network);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors & Mutators
    public function getRevenueAttribute()
    {
        return $this->commission_amount;
    }

    public function getFormattedOrderValueAttribute()
    {
        return '$' . number_format($this->order_value, 2);
    }

    public function getFormattedCommissionAttribute()
    {
        return '$' . number_format($this->commission_amount, 2);
    }

    public function getFormattedCommissionRateAttribute()
    {
        return $this->commission_rate . '%';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary'
        ];

        $badge = $badges[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$badge}'>{$this->status}</span>";
    }

    // Methods
    public static function trackConversion(Coupon $coupon, $request, $orderData, $userId = null, $clickId = null)
    {
        $conversion = self::create([
            'coupon_id' => $coupon->id,
            'user_id' => $userId,
            'click_id' => $clickId,
            'order_id' => $orderData['order_id'] ?? null,
            'order_value' => $orderData['order_value'] ?? 0,
            'commission_amount' => $orderData['commission_amount'] ?? 0,
            'commission_rate' => $orderData['commission_rate'] ?? $coupon->store->commission_rate ?? 0,
            'conversion_date' => now(),
            'status' => 'pending',
            'tracking_id' => $orderData['tracking_id'] ?? null,
            'affiliate_network' => $orderData['affiliate_network'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
            'utm_term' => $request->get('utm_term'),
            'utm_content' => $request->get('utm_content'),
            'gclid' => $request->get('gclid'),
            'fbclid' => $request->get('fbclid'),
            'msclkid' => $request->get('msclkid'),
            'conversion_data' => $orderData,
            'notes' => $orderData['notes'] ?? null
        ]);

        // Increment coupon conversion count
        $coupon->increment('conversion_count');

        // Update store revenue
        if ($coupon->store) {
            $coupon->store->increment('revenue', $conversion->commission_amount);
        }

        return $conversion;
    }

    public function approve()
    {
        $this->update(['status' => 'approved']);
        
        // You can add additional logic here like:
        // - Sending notifications
        // - Updating affiliate network status
        // - Triggering payout processes
    }

    public function reject($reason = null)
    {
        $this->update([
            'status' => 'rejected',
            'notes' => $reason
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getConversionRate()
    {
        if ($this->click) {
            return ($this->order_value > 0) ? ($this->commission_amount / $this->order_value) * 100 : 0;
        }
        return 0;
    }

    public function getRoi()
    {
        // Return on Investment calculation
        // This would depend on your business model
        return 0;
    }
}