<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class OneSignalService
{
    protected $appId;
    protected $apiKey;
    protected $baseUrl = 'https://onesignal.com/api/v1';

    public function __construct()
    {
        $this->appId = Setting::where('key', 'onesignal_app_id')->value('value');
        $this->apiKey = Setting::where('key', 'onesignal_rest_api_key')->value('value');
    }

    public function sendNotification(array $data)
    {
        if (!$this->appId || !$this->apiKey) {
            Log::error('OneSignal credentials not configured');
            return false;
        }

        try {
            $payload = [
                'app_id' => $this->appId,
                'included_segments' => ['All'],
                'headings' => ['en' => $data['title']],
                'contents' => ['en' => $data['message']],
                'url' => $data['url'] ?? url('/'),
                'large_icon' => $data['large_icon'] ?? asset('images/logo.png'),
                'big_picture' => $data['big_picture'] ?? null,
                'buttons' => $data['buttons'] ?? null,
                'data' => $data['custom_data'] ?? null
            ];

            if (isset($data['filters'])) {
                unset($payload['included_segments']);
                $payload['filters'] = $data['filters'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/notifications', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('OneSignal notification sent', ['id' => $result['id'] ?? null]);
                return $result;
            } else {
                Log::error('OneSignal API error', ['response' => $response->body()]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('OneSignal service error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendToUser($userId, array $data)
    {
        $data['filters'] = [
            ['field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $userId]
        ];

        return $this->sendNotification($data);
    }

    public function sendNewCouponNotification($coupon, $userIds = null)
    {
        $data = [
            'title' => '🎉 New Coupon Alert!',
            'message' => "Save with {$coupon->title} from {$coupon->store->name}",
            'url' => route('coupons.show', $coupon->slug),
            'large_icon' => $coupon->store->getFirstMediaUrl('logo'),
            'buttons' => [
                ['id' => 'view_coupon', 'text' => 'View Coupon'],
                ['id' => 'view_store', 'text' => 'View Store']
            ],
            'custom_data' => [
                'type' => 'new_coupon',
                'coupon_id' => $coupon->id,
                'store_id' => $coupon->store_id
            ]
        ];

        if ($userIds) {
            // Send to specific users who favorited the store
            foreach ($userIds as $userId) {
                $this->sendToUser($userId, $data);
            }
        } else {
            // Send to all users
            return $this->sendNotification($data);
        }
    }

    public function sendNewDealNotification($deal, $userIds = null)
    {
        $data = [
            'title' => '🔥 Hot Deal Alert!',
            'message' => "Don't miss: {$deal->title} - {$deal->discount_percentage}% OFF",
            'url' => route('deals.show', $deal->slug),
            'large_icon' => $deal->store->getFirstMediaUrl('logo'),
            'big_picture' => $deal->getFirstMediaUrl('image'),
            'buttons' => [
                ['id' => 'get_deal', 'text' => 'Get Deal'],
                ['id' => 'view_store', 'text' => 'View Store']
            ],
            'custom_data' => [
                'type' => 'new_deal',
                'deal_id' => $deal->id,
                'store_id' => $deal->store_id
            ]
        ];

        if ($userIds) {
            foreach ($userIds as $userId) {
                $this->sendToUser($userId, $data);
            }
        } else {
            return $this->sendNotification($data);
        }
    }

    public function sendFlashSaleNotification($title, $message, $url = null)
    {
        $data = [
            'title' => '⚡ Flash Sale Alert!',
            'message' => $message,
            'url' => $url ?? route('deals.index'),
            'buttons' => [
                ['id' => 'view_deals', 'text' => 'View Deals'],
                ['id' => 'later', 'text' => 'Remind Later']
            ],
            'custom_data' => [
                'type' => 'flash_sale'
            ]
        ];

        return $this->sendNotification($data);
    }

    public function getDeliveryStats($notificationId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey
            ])->get($this->baseUrl . "/notifications/{$notificationId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get OneSignal stats: ' . $e->getMessage());
            return null;
        }
    }

    public function createSegment($name, $filters)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/apps/' . $this->appId . '/segments', [
                'name' => $name,
                'filters' => $filters
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to create OneSignal segment: ' . $e->getMessage());
            return null;
        }
    }

    public function testConnection()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey
            ])->get($this->baseUrl . '/apps/' . $this->appId);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('OneSignal connection test failed: ' . $e->getMessage());
            return false;
        }
    }
}