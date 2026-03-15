<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use HasApiUser;

    public function index(Request $request)
    {
        $userId = $this->getApiUserId($request);

        $notifications = Notification::with('fromUser.profile')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($notification->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->read = true;
        $notification->save();

        return response()->json($notification);
    }

    public function markAllAsRead(Request $request)
    {
        $userId = $this->getApiUserId($request);

        Notification::where('user_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function destroy(Request $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($notification->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted'], 200);
    }
}
