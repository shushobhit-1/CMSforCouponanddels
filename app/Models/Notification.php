<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type', // system, coupon, deal, product, store, favorite, general
        'data', // JSON data for additional information
        'is_read',
        'is_sent',
        'sent_at',
        'read_at',
        'scheduled_at',
        'delivery_method', // email, push, sms, in_app
        'onesignal_id',
        'email_sent',
        'push_sent',
        'sms_sent',
        'in_app_sent',
        'priority', // low, normal, high, urgent
        'category',
        'action_url',
        'action_text',
        'expires_at',
        'created_by',
        'status' // pending, sent, failed, cancelled
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_sent' => 'boolean',
        'email_sent' => 'boolean',
        'push_sent' => 'boolean',
        'sms_sent' => 'boolean',
        'in_app_sent' => 'boolean',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = [
        'sent_at',
        'read_at',
        'scheduled_at',
        'expires_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeUnsent($query)
    {
        return $query->where('is_sent', false);
    }

    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<', now());
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function getIsScheduledAttribute()
    {
        return $this->scheduled_at && $this->scheduled_at > now();
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending' && !$this->is_scheduled;
    }

    public function getStatusTextAttribute()
    {
        if ($this->is_expired) return 'Expired';
        if ($this->is_scheduled) return 'Scheduled';
        if ($this->is_sent) return 'Sent';
        if ($this->status === 'failed') return 'Failed';
        if ($this->status === 'cancelled') return 'Cancelled';
        return 'Pending';
    }

    public function getStatusColorAttribute()
    {
        if ($this->is_expired) return 'secondary';
        if ($this->is_scheduled) return 'info';
        if ($this->is_sent) return 'success';
        if ($this->status === 'failed') return 'danger';
        if ($this->status === 'cancelled') return 'warning';
        return 'primary';
    }

    public function getPriorityTextAttribute()
    {
        return match($this->priority) {
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
            default => 'Normal'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => 'secondary',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'primary'
        };
    }

    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'system' => 'System',
            'coupon' => 'Coupon',
            'deal' => 'Deal',
            'product' => 'Product',
            'store' => 'Store',
            'favorite' => 'Favorite',
            'general' => 'General',
            default => 'General'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'system' => 'fas fa-cog',
            'coupon' => 'fas fa-ticket-alt',
            'deal' => 'fas fa-tags',
            'product' => 'fas fa-box',
            'store' => 'fas fa-store',
            'favorite' => 'fas fa-heart',
            'general' => 'fas fa-bell',
            default => 'fas fa-bell'
        };
    }

    public function getDeliveryMethodsAttribute()
    {
        $methods = [];
        
        if ($this->email_sent) $methods[] = 'email';
        if ($this->push_sent) $methods[] = 'push';
        if ($this->sms_sent) $methods[] = 'sms';
        if ($this->in_app_sent) $methods[] = 'in_app';
        
        return $methods;
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    public function markAsSent()
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
            'status' => 'sent'
        ]);
    }

    public function markAsFailed()
    {
        $this->update([
            'status' => 'failed'
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled'
        ]);
    }

    public function reschedule($newDate)
    {
        $this->update([
            'scheduled_at' => $newDate,
            'status' => 'pending'
        ]);
    }

    public function extendExpiry($days)
    {
        $this->update([
            'expires_at' => now()->addDays($days)
        ]);
    }

    // Static Methods
    public static function createForUser($userId, $title, $message, $type = 'general', $data = [], $options = [])
    {
        $defaults = [
            'priority' => 'normal',
            'delivery_method' => 'in_app',
            'scheduled_at' => null,
            'expires_at' => now()->addDays(30),
            'action_url' => null,
            'action_text' => null,
            'category' => null,
            'created_by' => auth()->id()
        ];

        $options = array_merge($defaults, $options);

        return static::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'priority' => $options['priority'],
            'delivery_method' => $options['delivery_method'],
            'scheduled_at' => $options['scheduled_at'],
            'expires_at' => $options['expires_at'],
            'action_url' => $options['action_url'],
            'action_text' => $options['action_text'],
            'category' => $options['category'],
            'created_by' => $options['created_by'],
            'status' => $options['scheduled_at'] ? 'pending' : 'pending'
        ]);
    }

    public static function createForMultipleUsers($userIds, $title, $message, $type = 'general', $data = [], $options = [])
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = static::createForUser($userId, $title, $message, $type, $data, $options);
        }
        
        return collect($notifications);
    }

    public static function createForFavoriteStores($userId, $title, $message, $type, $data = [], $options = [])
    {
        $user = User::find($userId);
        if (!$user) return collect();

        $favoriteStoreIds = $user->favorites()
                                ->where('favoritable_type', Store::class)
                                ->pluck('favoritable_id');

        if ($favoriteStoreIds->isEmpty()) return collect();

        return static::createForMultipleUsers(
            $favoriteStoreIds,
            $title,
            $message,
            $type,
            $data,
            $options
        );
    }

    public static function sendBulkNotifications($notifications)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($notifications as $notification) {
            try {
                // Send via OneSignal
                if ($notification->delivery_method === 'push' || $notification->delivery_method === 'all') {
                    $notification->sendPushNotification();
                }

                // Send via email
                if ($notification->delivery_method === 'email' || $notification->delivery_method === 'all') {
                    $notification->sendEmailNotification();
                }

                // Send via SMS
                if ($notification->delivery_method === 'sms' || $notification->delivery_method === 'all') {
                    $notification->sendSmsNotification();
                }

                $notification->markAsSent();
                $results['success']++;

            } catch (\Exception $e) {
                $notification->markAsFailed();
                $results['failed']++;
                $results['errors'][] = [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    // OneSignal Integration Methods
    public function sendPushNotification()
    {
        if (!$this->user || !$this->user->onesignal_player_id) {
            throw new \Exception('User has no OneSignal player ID');
        }

        // Implementation for OneSignal push notification
        // This would integrate with the OneSignal API
        
        $this->update(['push_sent' => true]);
    }

    public function sendEmailNotification()
    {
        // Implementation for email notification
        // This would use Laravel's mail system
        
        $this->update(['email_sent' => true]);
    }

    public function sendSmsNotification()
    {
        // Implementation for SMS notification
        // This would integrate with an SMS service provider
        
        $this->update(['sms_sent' => true]);
    }

    // Events
    protected static function booted()
    {
        static::created(function ($notification) {
            // Log activity
            activity()
                ->performedOn($notification)
                ->causedBy($notification->creator)
                ->log('created notification');
        });

        static::updated(function ($notification) {
            if ($notification->wasChanged('is_read')) {
                activity()
                    ->performedOn($notification)
                    ->causedBy($notification->user)
                    ->log($notification->is_read ? 'read notification' : 'marked notification as unread');
            }
        });
    }
}