<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'clicked_at',
        'session_id',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
        'timezone',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid',
        'fbclid',
        'msclkid',
        'tracking_data'
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'tracking_data' => 'array'
    ];

    protected $dates = [
        'clicked_at',
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

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('clicked_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('clicked_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('clicked_at', now()->month);
    }

    public function scopeByCoupon($query, $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Accessors & Mutators
    public function getDeviceTypeAttribute($value)
    {
        if (!$value) {
            $userAgent = $this->user_agent ?? '';
            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini))/i', strtolower($userAgent))) {
                return 'tablet';
            }
            if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
                return 'mobile';
            }
            return 'desktop';
        }
        return $value;
    }

    public function getBrowserAttribute($value)
    {
        if (!$value) {
            $userAgent = $this->user_agent ?? '';
            if (preg_match('/MSIE|Trident/i', $userAgent)) {
                return 'Internet Explorer';
            } elseif (preg_match('/Firefox/i', $userAgent)) {
                return 'Firefox';
            } elseif (preg_match('/Chrome/i', $userAgent)) {
                return 'Chrome';
            } elseif (preg_match('/Safari/i', $userAgent)) {
                return 'Safari';
            } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
                return 'Opera';
            }
            return 'Unknown';
        }
        return $value;
    }

    public function getOsAttribute($value)
    {
        if (!$value) {
            $userAgent = $this->user_agent ?? '';
            if (preg_match('/windows|win32/i', $userAgent)) {
                return 'Windows';
            } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
                return 'macOS';
            } elseif (preg_match('/linux/i', $userAgent)) {
                return 'Linux';
            } elseif (preg_match('/android/i', $userAgent)) {
                return 'Android';
            } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
                return 'iOS';
            }
            return 'Unknown';
        }
        return $value;
    }

    // Methods
    public static function trackClick(Coupon $coupon, $request, $userId = null)
    {
        $click = self::create([
            'coupon_id' => $coupon->id,
            'user_id' => $userId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'clicked_at' => now(),
            'session_id' => $request->session()->getId(),
            'device_type' => self::detectDeviceType($request->userAgent()),
            'browser' => self::detectBrowser($request->userAgent()),
            'os' => self::detectOs($request->userAgent()),
            'country' => self::getCountryFromIp($request->ip()),
            'city' => self::getCityFromIp($request->ip()),
            'timezone' => self::getTimezoneFromIp($request->ip()),
            'utm_source' => $request->get('utm_source'),
            'utm_medium' => $request->get('utm_medium'),
            'utm_campaign' => $request->get('utm_campaign'),
            'utm_term' => $request->get('utm_term'),
            'utm_content' => $request->get('utm_content'),
            'gclid' => $request->get('gclid'),
            'fbclid' => $request->get('fbclid'),
            'msclkid' => $request->get('msclkid'),
            'tracking_data' => [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'query_params' => $request->query(),
                'post_data' => $request->post(),
            ]
        ]);

        // Increment coupon click count
        $coupon->increment('click_count');

        return $click;
    }

    private static function detectDeviceType($userAgent)
    {
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini))/i', strtolower($userAgent))) {
            return 'tablet';
        }
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($userAgent))) {
            return 'mobile';
        }
        return 'desktop';
    }

    private static function detectBrowser($userAgent)
    {
        if (preg_match('/MSIE|Trident/i', $userAgent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            return 'Opera';
        }
        return 'Unknown';
    }

    private static function detectOs($userAgent)
    {
        if (preg_match('/windows|win32/i', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/linux/i', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            return 'iOS';
        }
        return 'Unknown';
    }

    private static function getCountryFromIp($ip)
    {
        // You can implement IP geolocation here using services like MaxMind, IP2Location, etc.
        // For now, returning null
        return null;
    }

    private static function getCityFromIp($ip)
    {
        // You can implement IP geolocation here
        return null;
    }

    private static function getTimezoneFromIp($ip)
    {
        // You can implement IP geolocation here
        return null;
    }
}