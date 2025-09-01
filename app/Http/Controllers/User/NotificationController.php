<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display user's notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filter = $request->get('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->latest()->paginate(20);

        return view('user.notifications', compact('notifications', 'filter'));
    }

    /**
     * Mark notifications as read
     */
    public function markAsRead(Request $request)
    {
        $user = Auth::user();

        if ($request->has('notification_id')) {
            // Mark specific notification as read
            $notification = $user->notifications()->find($request->notification_id);
            if ($notification) {
                $notification->markAsRead();
            }
        } else {
            // Mark all notifications as read
            $user->unreadNotifications->markAsRead();
        }

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            abort(404);
        }

        $notification->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted');
    }

    /**
     * Get unread notification count (for AJAX)
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications->count();
        
        return response()->json(['count' => $count]);
    }
}