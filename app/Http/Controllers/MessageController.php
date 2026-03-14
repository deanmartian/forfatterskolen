<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\DirectMessage;
use App\Models\Notification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    use HasApiUser;

    public function index(Request $request)
    {
        $userId = $this->getApiUserId($request);

        $messages = DirectMessage::with(['sender.profile', 'recipient.profile'])
            ->where('recipient_id', $userId)
            ->orWhere('sender_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($messages);
    }

    public function conversations(Request $request)
    {
        $userId = $this->getApiUserId($request);

        // Get all unique conversation partners
        $conversations = DirectMessage::select([
            DB::raw("CASE
                WHEN sender_id = {$userId} THEN recipient_id
                ELSE sender_id
            END as partner_id"),
            DB::raw('MAX(created_at) as last_message_at'),
            DB::raw("SUM(CASE WHEN recipient_id = {$userId} AND `read` = 0 THEN 1 ELSE 0 END) as unread_count")
        ])
        ->where('sender_id', $userId)
        ->orWhere('recipient_id', $userId)
        ->groupBy('partner_id')
        ->orderByDesc('last_message_at')
        ->get();

        // Load partner profiles
        foreach ($conversations as $conversation) {
            $conversation->partner = User::with('profile')
                ->find($conversation->partner_id);
        }

        return response()->json($conversations);
    }

    public function getConversation(Request $request, $partnerId)
    {
        $userId = $this->getApiUserId($request);

        $messages = DirectMessage::with(['sender.profile', 'recipient.profile'])
            ->where(function ($query) use ($userId, $partnerId) {
                $query->where('sender_id', $userId)
                    ->where('recipient_id', $partnerId);
            })
            ->orWhere(function ($query) use ($userId, $partnerId) {
                $query->where('sender_id', $partnerId)
                    ->where('recipient_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        DirectMessage::where('sender_id', $partnerId)
            ->where('recipient_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $userId = $this->getApiUserId($request);
        $user = $this->getApiUser($request);

        $message = DirectMessage::create([
            'id' => Str::uuid(),
            'sender_id' => $userId,
            'recipient_id' => $validated['recipient_id'],
            'content' => $validated['content'],
        ]);

        // Create notification for recipient
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => $validated['recipient_id'],
            'type' => 'message',
            'content' => ($user->first_name ?? 'Someone') . ' sent you a message',
            'from_user_id' => $userId,
            'link' => '/meldinger',
        ]);

        $message->load(['sender.profile', 'recipient.profile']);

        return response()->json($message, 201);
    }

    public function markAsRead(Request $request, $id)
    {
        $message = DirectMessage::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($message->recipient_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->read = true;
        $message->save();

        return response()->json($message);
    }
}
