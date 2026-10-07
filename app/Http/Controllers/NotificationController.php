<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAllAsRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function markAsReadAndRedirect($id)
    {
        $notification = AppNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back();
    }

    public function markAsRead($id)
    {
        $notification = AppNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAsUnread($id)
    {
        $notification = AppNotification::where('user_id', auth()->id())->findOrFail($id);
        // Mark as unread only - preserve the notification record
        $notification->update(['is_read' => false]);

        return back()->with('success', 'Notification marked as unread.');
    }

    public function destroy($id)
    {
        $notification = AppNotification::where('user_id', auth()->id())->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }

    public function destroyAll()
    {
        AppNotification::where('user_id', auth()->id())->delete();

        return back()->with('success', 'All notifications deleted.');
    }
}
