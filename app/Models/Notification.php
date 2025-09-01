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
        'type', // system, coupon, deal, product, store, favorite, reminder, etc.
        'title',
        'message',
        'data', // JSON data for additional information
        'notifiable_type',
        'notifiable_id',
        'read_at',
        'sent_at',
        'onesignal_id',
        'onesignal_status',
        'priority', // low, normal, high, urgent
        'category',
        'action_url',
        'action_text',
        'icon',
        'color',
        'expires_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'data' => 'array',
        'priority' => 'string',
    ];

    protected $dates = [
        'read_at',
        'sent_at',
        'expires_at',
        'deleted_at',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeSent($query)
    {
        return $query->whereNotNull('sent_at');
    }

    public function scopeUnsent($query)
    {
        return $query->whereNull('sent_at');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->latest()->limit($limit);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('message', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getIsReadAttribute()
    {
        return !is_null($this->read_at);
    }

    public function getIsUnreadAttribute()
    {
        return is_null($this->read_at);
    }

    public function getIsSentAttribute()
    {
        return !is_null($this->sent_at);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function getIsActiveAttribute()
    {
        return !$this->is_expired;
    }

    public function getPriorityColorAttribute()
    {
        return match ($this->priority) {
            'low' => 'secondary',
            'normal' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'primary'
        };
    }

    public function getPriorityIconAttribute()
    {
        return match ($this->priority) {
            'low' => 'fas fa-info-circle',
            'normal' => 'fas fa-bell',
            'high' => 'fas fa-exclamation-triangle',
            'urgent' => 'fas fa-exclamation-circle',
            default => 'fas fa-bell'
        };
    }

    public function getTypeIconAttribute()
    {
        return match ($this->type) {
            'coupon' => 'fas fa-ticket-alt',
            'deal' => 'fas fa-tags',
            'product' => 'fas fa-box',
            'store' => 'fas fa-store',
            'favorite' => 'fas fa-heart',
            'reminder' => 'fas fa-clock',
            'system' => 'fas fa-cog',
            'security' => 'fas fa-shield-alt',
            'update' => 'fas fa-sync-alt',
            default => 'fas fa-bell'
        };
    }

    public function getTypeColorAttribute()
    {
        return match ($this->type) {
            'coupon' => 'success',
            'deal' => 'warning',
            'product' => 'primary',
            'store' => 'info',
            'favorite' => 'danger',
            'reminder' => 'secondary',
            'system' => 'dark',
            'security' => 'danger',
            'update' => 'info',
            default => 'primary'
        };
    }

    public function getFormattedCreatedAtAttribute()
    {
        if ($this->created_at->diffInDays(now()) > 7) {
            return $this->created_at->format('M j, Y');
        } elseif ($this->created_at->diffInDays(now()) > 1) {
            return $this->created_at->diffForHumans();
        } else {
            return $this->created_at->diffForHumans();
        }
    }

    public function getFormattedReadAtAttribute()
    {
        if ($this->read_at) {
            return $this->read_at->diffForHumans();
        }
        return null;
    }

    public function getFormattedSentAtAttribute()
    {
        if ($this->sent_at) {
            return $this->sent_at->diffForHumans();
        }
        return null;
    }

    public function getFormattedExpiresAtAttribute()
    {
        if ($this->expires_at) {
            if ($this->expires_at->isPast()) {
                return 'Expired';
            }
            return $this->expires_at->diffForHumans();
        }
        return null;
    }

    public function getActionButtonAttribute()
    {
        if ($this->action_url && $this->action_text) {
            return [
                'url' => $this->action_url,
                'text' => $this->action_text,
                'color' => $this->color ?: 'primary',
            ];
        }
        return null;
    }

    public function getNotificationDataAttribute()
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
            'category' => $this->category,
            'icon' => $this->icon ?: $this->type_icon,
            'color' => $this->color ?: $this->type_color,
            'action_url' => $this->action_url,
            'action_text' => $this->action_text,
            'created_at' => $this->formatted_created_at,
            'is_read' => $this->is_read,
        ];

        if ($this->data) {
            $data = array_merge($data, $this->data);
        }

        return $data;
    }

    // Methods
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread()
    {
        $this->update(['read_at' => null]);
    }

    public function markAsSent()
    {
        if (!$this->is_sent) {
            $this->update(['sent_at' => now()]);
        }
    }

    public function markAsUnsent()
    {
        $this->update(['sent_at' => null]);
    }

    public function updateOneSignalStatus($status, $onesignalId = null)
    {
        $this->update([
            'onesignal_status' => $status,
            'onesignal_id' => $onesignalId ?: $this->onesignal_id,
        ]);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function shouldSend()
    {
        return !$this->is_sent && !$this->is_expired;
    }

    public function canMarkAsRead()
    {
        return $this->is_unread;
    }

    public function canMarkAsUnread()
    {
        return $this->is_read;
    }

    // Static Methods
    public static function createForUser($userId, $type, $title, $message, $data = [], $options = [])
    {
        $notification = static::create(array_merge([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $options['priority'] ?? 'normal',
            'category' => $options['category'] ?? 'general',
            'action_url' => $options['action_url'] ?? null,
            'action_text' => $options['action_text'] ?? null,
            'icon' => $options['icon'] ?? null,
            'color' => $options['color'] ?? null,
            'expires_at' => $options['expires_at'] ?? null,
            'created_by' => $options['created_by'] ?? auth()->id(),
        ], $options));

        return $notification;
    }

    public static function createForMultipleUsers($userIds, $type, $title, $message, $data = [], $options = [])
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = static::createForUser($userId, $type, $title, $message, $data, $options);
        }
        
        return collect($notifications);
    }

    public static function createForAllUsers($type, $title, $message, $data = [], $options = [])
    {
        $userIds = User::active()->pluck('id');
        return static::createForMultipleUsers($userIds, $type, $title, $message, $data, $options);
    }

    public static function createForFavorites($type, $title, $message, $data = [], $options = [])
    {
        $favorites = Favorite::getFavoritesForNotification(null, $type);
        $userIds = $favorites->pluck('user_id')->unique();
        
        return static::createForMultipleUsers($userIds, $type, $title, $message, $data, $options);
    }

    public static function markAllAsRead($userId)
    {
        return static::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
    }

    public static function deleteExpired()
    {
        return static::expired()->delete();
    }

    public static function getUnreadCount($userId)
    {
        return static::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->count();
    }

    public static function getNotificationStats($userId)
    {
        $total = static::where('user_id', $userId)->count();
        $unread = static::where('user_id', $userId)->whereNull('read_at')->count();
        $read = $total - $unread;
        
        $byType = static::where('user_id', $userId)
                        ->selectRaw('type, COUNT(*) as count')
                        ->groupBy('type')
                        ->get()
                        ->keyBy('type');
        
        $byPriority = static::where('user_id', $userId)
                            ->selectRaw('priority, COUNT(*) as count')
                            ->groupBy('priority')
                            ->get()
                            ->keyBy('priority');
        
        return [
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
            'by_type' => $byType,
            'by_priority' => $byPriority,
        ];
    }

    public static function sendOneSignalNotification($notification)
    {
        // This would integrate with OneSignal API
        // Implementation depends on OneSignal configuration
        try {
            // OneSignal API call would go here
            $notification->updateOneSignalStatus('sent');
            return true;
        } catch (\Exception $e) {
            $notification->updateOneSignalStatus('failed');
            return false;
        }
    }

    public static function cleanupOldNotifications($days = 90)
    {
        return static::where('created_at', '<', now()->subDays($days))
                    ->where('is_read', true)
                    ->delete();
    }
}