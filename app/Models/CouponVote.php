<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'ip_address',
        'user_agent',
        'vote_type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Vote types
    const VOTE_UPVOTE = 'upvote';
    const VOTE_DOWNVOTE = 'downvote';
    const VOTE_LIKE = 'like';
    const VOTE_DISLIKE = 'dislike';

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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $voteType)
    {
        return $query->where('vote_type', $voteType);
    }

    public function scopeByCoupon($query, $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeUpvotes($query)
    {
        return $query->where('vote_type', self::VOTE_UPVOTE);
    }

    public function scopeDownvotes($query)
    {
        return $query->where('vote_type', self::VOTE_DOWNVOTE);
    }

    public function scopeLikes($query)
    {
        return $query->where('vote_type', self::VOTE_LIKE);
    }

    public function scopeDislikes($query)
    {
        return $query->where('vote_type', self::VOTE_DISLIKE);
    }

    // Methods
    public static function vote(Coupon $coupon, $request, $voteType, $userId = null)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check if user already voted this way
        $existingVote = self::where('coupon_id', $coupon->id)
            ->where('vote_type', $voteType)
            ->where(function($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        if ($existingVote) {
            // Toggle vote if same type
            if ($existingVote->is_active) {
                $existingVote->update(['is_active' => false]);
                self::updateCouponVoteCount($coupon, $voteType, -1);
                return ['action' => 'removed', 'vote' => $existingVote];
            } else {
                $existingVote->update(['is_active' => true]);
                self::updateCouponVoteCount($coupon, $voteType, 1);
                return ['action' => 'added', 'vote' => $existingVote];
            }
        }

        // Check if user voted the opposite way
        $oppositeVoteType = self::getOppositeVoteType($voteType);
        $oppositeVote = self::where('coupon_id', $coupon->id)
            ->where('vote_type', $oppositeVoteType)
            ->where(function($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        if ($oppositeVote && $oppositeVote->is_active) {
            // Remove opposite vote and add new vote
            $oppositeVote->update(['is_active' => false]);
            self::updateCouponVoteCount($coupon, $oppositeVoteType, -1);
        }

        // Create new vote
        $vote = self::create([
            'coupon_id' => $coupon->id,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'vote_type' => $voteType,
            'is_active' => true
        ]);

        self::updateCouponVoteCount($coupon, $voteType, 1);

        return ['action' => 'added', 'vote' => $vote];
    }

    public static function getVoteCounts(Coupon $coupon)
    {
        return [
            'upvotes' => $coupon->upvotes_count ?? 0,
            'downvotes' => $coupon->downvotes_count ?? 0,
            'likes' => $coupon->likes_count ?? 0,
            'dislikes' => $coupon->dislikes_count ?? 0,
            'total_votes' => ($coupon->upvotes_count ?? 0) + ($coupon->downvotes_count ?? 0) + ($coupon->likes_count ?? 0) + ($coupon->dislikes_count ?? 0)
        ];
    }

    public static function getUserVote(Coupon $coupon, $request, $userId = null)
    {
        $ipAddress = $request->ip();

        $vote = self::where('coupon_id', $coupon->id)
            ->where('is_active', true)
            ->where(function($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        return $vote ? $vote->vote_type : null;
    }

    public static function updateCouponVoteCount(Coupon $coupon, $voteType, $increment)
    {
        $field = $voteType . 's_count';
        
        if (Schema::hasColumn('coupons', $field)) {
            $coupon->increment($field, $increment);
        }
    }

    private static function getOppositeVoteType($voteType)
    {
        $opposites = [
            self::VOTE_UPVOTE => self::VOTE_DOWNVOTE,
            self::VOTE_DOWNVOTE => self::VOTE_UPVOTE,
            self::VOTE_LIKE => self::VOTE_DISLIKE,
            self::VOTE_DISLIKE => self::VOTE_LIKE
        ];

        return $opposites[$voteType] ?? null;
    }

    public static function getVoteStats()
    {
        return [
            'total_votes' => self::active()->count(),
            'upvotes' => self::active()->upvotes()->count(),
            'downvotes' => self::active()->downvotes()->count(),
            'likes' => self::active()->likes()->count(),
            'dislikes' => self::active()->dislikes()->count(),
            'today_votes' => self::active()->whereDate('created_at', today())->count(),
            'this_week_votes' => self::active()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_votes' => self::active()->whereMonth('created_at', now()->month)->count()
        ];
    }

    public static function getTopVotedCoupons($limit = 10, $voteType = null)
    {
        $query = Coupon::withCount(['upvotes', 'downvotes', 'likes', 'dislikes'])
            ->orderBy('upvotes_count', 'desc');

        if ($voteType) {
            $query->orderBy($voteType . 's_count', 'desc');
        }

        return $query->take($limit)->get();
    }

    public static function getVoteHistory($userId = null, $limit = 20)
    {
        $query = self::with(['coupon', 'user'])
            ->orderBy('created_at', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->take($limit)->get();
    }

    public static function cleanupOldVotes($days = 30)
    {
        return self::where('created_at', '<', now()->subDays($days))
            ->where('is_active', false)
            ->delete();
    }
}