<?php

namespace App\Http\Controllers\Api\V1;

use App\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'data' => collect($notifications->items())->map(function ($n) {
                return $this->formatNotification($n);
            })->values(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return $this->errorResponse('Notification not found.', 'not_found', 404);
        }

        $notification->update(['is_read' => 1]);

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $this->apiUser($request);

        Notification::where('user_id', $user->id)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $this->apiUser($request);

        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return $this->errorResponse('Notification not found.', 'not_found', 404);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }

    private function formatNotification(Notification $n): array
    {
        return [
            'id' => $n->id,
            'message' => $n->message,
            'is_read' => (bool) $n->is_read,
            'book_id' => $n->book_id,
            'chapter_id' => $n->chapter_id,
            'is_group' => (bool) $n->is_group,
            'created_at' => $n->created_at?->toIso8601String(),
        ];
    }
}
