<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getRecent()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $notifications = $user->unreadNotifications()->take(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->data['type'] ?? 'system',
                'message' => $n->data['message'] ?? 'Notification received',
                'url' => $n->data['url'] ?? '#',
                'time_ago' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }
        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success']);
        }
        return back();
    }
}
