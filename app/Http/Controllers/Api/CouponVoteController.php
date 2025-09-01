<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponVote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CouponVoteController extends Controller
{
    /**
     * Vote on a coupon (upvote, downvote, like, dislike)
     */
    public function vote(Request $request, Coupon $coupon): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vote_type' => 'required|in:upvote,downvote,like,dislike'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid vote type',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $voteType = $request->vote_type;
            $userId = auth()->id(); // Will be null for guests

            // Check if coupon is active
            if (!$coupon->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot vote on inactive coupon'
                ], 400);
            }

            // Process the vote
            $result = CouponVote::vote($coupon, $request, $voteType, $userId);

            // Refresh coupon to get updated vote counts
            $coupon->refresh();

            // Get updated vote counts
            $voteCounts = CouponVote::getVoteCounts($coupon);

            return response()->json([
                'success' => true,
                'message' => 'Vote ' . $result['action'] . ' successfully',
                'action' => $result['action'],
                'vote_type' => $voteType,
                'coupon' => [
                    'id' => $coupon->id,
                    'upvotes_count' => $voteCounts['upvotes'],
                    'downvotes_count' => $voteCounts['downvotes'],
                    'likes_count' => $voteCounts['likes'],
                    'dislikes_count' => $voteCounts['dislikes'],
                    'total_votes' => $voteCounts['total_votes']
                ],
                'vote_counts' => $voteCounts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process vote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get vote statistics for a coupon
     */
    public function getVoteStats(Coupon $coupon): JsonResponse
    {
        try {
            $voteCounts = CouponVote::getVoteCounts($coupon);
            $userVote = null;

            if (auth()->check()) {
                $userVote = CouponVote::getUserVote($coupon, request(), auth()->id());
            }

            return response()->json([
                'success' => true,
                'coupon_id' => $coupon->id,
                'vote_counts' => $voteCounts,
                'user_vote' => $userVote
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get vote statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's voting history
     */
    public function getUserVoteHistory(Request $request): JsonResponse
    {
        try {
            $userId = auth()->id();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            $limit = $request->get('limit', 20);
            $voteHistory = CouponVote::getVoteHistory($userId, $limit);

            return response()->json([
                'success' => true,
                'vote_history' => $voteHistory,
                'total_votes' => $voteHistory->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get vote history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get overall voting statistics
     */
    public function getOverallStats(): JsonResponse
    {
        try {
            $stats = CouponVote::getVoteStats();
            $topVotedCoupons = CouponVote::getTopVotedCoupons(10);

            return response()->json([
                'success' => true,
                'overall_stats' => $stats,
                'top_voted_coupons' => $topVotedCoupons
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get overall statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove user's vote on a coupon
     */
    public function removeVote(Request $request, Coupon $coupon): JsonResponse
    {
        try {
            $userId = auth()->id();
            $ipAddress = $request->ip();

            // Find and remove the vote
            $vote = CouponVote::where('coupon_id', $coupon->id)
                ->where(function($query) use ($userId, $ipAddress) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('ip_address', $ipAddress);
                    }
                })
                ->where('is_active', true)
                ->first();

            if (!$vote) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active vote found'
                ], 404);
            }

            $voteType = $vote->vote_type;
            $vote->update(['is_active' => false]);

            // Update coupon vote count
            CouponVote::updateCouponVoteCount($coupon, $voteType, -1);

            // Refresh coupon to get updated vote counts
            $coupon->refresh();
            $voteCounts = CouponVote::getVoteCounts($coupon);

            return response()->json([
                'success' => true,
                'message' => 'Vote removed successfully',
                'action' => 'removed',
                'vote_type' => $voteType,
                'coupon' => [
                    'id' => $coupon->id,
                    'upvotes_count' => $voteCounts['upvotes'],
                    'downvotes_count' => $voteCounts['downvotes'],
                    'likes_count' => $voteCounts['likes'],
                    'dislikes_count' => $voteCounts['dislikes'],
                    'total_votes' => $voteCounts['total_votes']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove vote',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get voting analytics for admin dashboard
     */
    public function getAdminAnalytics(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!auth()->user() || !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required'
                ], 403);
            }

            $period = $request->get('period', 'month'); // day, week, month, year
            $startDate = null;
            $endDate = now();

            switch ($period) {
                case 'day':
                    $startDate = now()->startOfDay();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    break;
            }

            $query = CouponVote::with(['coupon', 'user'])
                ->when($startDate, function($q) use ($startDate, $endDate) {
                    return $q->whereBetween('created_at', [$startDate, $endDate]);
                });

            $analytics = [
                'total_votes' => $query->count(),
                'upvotes' => $query->clone()->upvotes()->count(),
                'downvotes' => $query->clone()->downvotes()->count(),
                'likes' => $query->clone()->likes()->count(),
                'dislikes' => $query->clone()->dislikes()->count(),
                'by_coupon' => $query->clone()
                    ->selectRaw('coupon_id, COUNT(*) as vote_count')
                    ->groupBy('coupon_id')
                    ->orderBy('vote_count', 'desc')
                    ->limit(10)
                    ->get(),
                'by_user' => $query->clone()
                    ->selectRaw('user_id, COUNT(*) as vote_count')
                    ->whereNotNull('user_id')
                    ->groupBy('user_id')
                    ->orderBy('vote_count', 'desc')
                    ->limit(10)
                    ->get(),
                'by_type' => $query->clone()
                    ->selectRaw('vote_type, COUNT(*) as vote_count')
                    ->groupBy('vote_type')
                    ->get(),
                'daily_trends' => $query->clone()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as vote_count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'analytics' => $analytics,
                'period' => $period,
                'start_date' => $startDate?->toDateString(),
                'end_date' => $endDate->toDateString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export voting data for admin
     */
    public function exportVotingData(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!auth()->user() || !auth()->user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required'
                ], 403);
            }

            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $voteType = $request->get('vote_type');

            $query = CouponVote::with(['coupon', 'user']);

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($voteType) {
                $query->where('vote_type', $voteType);
            }

            $votes = $query->orderBy('created_at', 'desc')->get();

            // Format data for export
            $exportData = $votes->map(function($vote) {
                return [
                    'id' => $vote->id,
                    'coupon_id' => $vote->coupon_id,
                    'coupon_title' => $vote->coupon->title ?? 'N/A',
                    'user_id' => $vote->user_id,
                    'user_name' => $vote->user->name ?? 'Guest',
                    'vote_type' => $vote->vote_type,
                    'ip_address' => $vote->ip_address,
                    'user_agent' => $vote->user_agent,
                    'is_active' => $vote->is_active ? 'Yes' : 'No',
                    'created_at' => $vote->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $vote->updated_at->format('Y-m-d H:i:s')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $exportData,
                'total_records' => $exportData->count(),
                'export_format' => 'json'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to export voting data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}