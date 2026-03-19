<?php

namespace App\Services;

use App\HelpwiseConversation;
use App\HelpwiseMessage;
use App\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpwiseService
{
    private string $apiKey;
    private string $baseUrl = 'https://app.helpwise.io/api/v1';

    public function __construct()
    {
        $this->apiKey = config('services.helpwise.api_key', '');
    }

    /**
     * Get all conversations for a user (by email) from local DB.
     */
    public function getConversationsForUser(int $userId)
    {
        return HelpwiseConversation::where('user_id', $userId)
            ->with('latestMessage')
            ->orderByDesc('updated_at')
            ->get();
    }

    /**
     * Get conversation with all messages.
     */
    public function getConversationWithMessages(int $conversationId): HelpwiseConversation
    {
        return HelpwiseConversation::with(['messages' => function ($q) {
            $q->orderBy('message_at');
        }, 'user'])->findOrFail($conversationId);
    }

    /**
     * Link conversations to users by matching email addresses.
     * Run periodically or after import to ensure connections.
     */
    public function linkConversationsToUsers(): int
    {
        $unlinked = HelpwiseConversation::whereNull('user_id')
            ->whereNotNull('customer_email')
            ->get();

        $linked = 0;
        foreach ($unlinked as $conv) {
            $user = User::where('email', $conv->customer_email)->first();
            if ($user) {
                $conv->update(['user_id' => $user->id]);
                $linked++;
            }
        }

        return $linked;
    }

    /**
     * Get student/learner communication profile - all Helpwise conversations
     * grouped by inbox, with message counts and last activity.
     */
    public function getStudentProfile(int $userId): array
    {
        $user = User::findOrFail($userId);
        $conversations = HelpwiseConversation::where('user_id', $userId)
            ->with(['messages', 'latestMessage'])
            ->orderByDesc('updated_at')
            ->get();

        $byInbox = $conversations->groupBy('inbox');
        $totalMessages = 0;
        $inboxSummary = [];

        foreach ($byInbox as $inbox => $convs) {
            $msgCount = $convs->sum(fn($c) => $c->messages->count());
            $totalMessages += $msgCount;
            $inboxSummary[] = [
                'inbox' => $inbox ?? 'Ukjent',
                'conversations' => $convs->count(),
                'messages' => $msgCount,
                'last_activity' => $convs->max('updated_at'),
                'open_conversations' => $convs->where('status', 'open')->count(),
            ];
        }

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
            ],
            'summary' => [
                'total_conversations' => $conversations->count(),
                'open_conversations' => $conversations->where('status', 'open')->count(),
                'total_messages' => $totalMessages,
                'first_contact' => $conversations->min('helpwise_created_at'),
                'last_contact' => $conversations->max('updated_at'),
            ],
            'by_inbox' => $inboxSummary,
            'conversations' => $conversations,
        ];
    }

    /**
     * Search conversations across all inboxes.
     */
    public function searchConversations(array $filters = [])
    {
        $query = HelpwiseConversation::with(['user', 'latestMessage']);

        if (!empty($filters['email'])) {
            $query->where('customer_email', 'like', '%' . $filters['email'] . '%');
        }
        if (!empty($filters['inbox'])) {
            $query->where('inbox', $filters['inbox']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', 'like', '%' . $filters['assigned_to'] . '%');
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->orderByDesc('updated_at')->paginate(25);
    }

    /**
     * Get all unique inboxes from stored conversations.
     */
    public function getInboxes(): array
    {
        return HelpwiseConversation::select('inbox')
            ->distinct()
            ->whereNotNull('inbox')
            ->pluck('inbox')
            ->toArray();
    }

    /**
     * Get dashboard stats.
     */
    public function getStats(): array
    {
        return [
            'total_conversations' => HelpwiseConversation::count(),
            'open_conversations' => HelpwiseConversation::where('status', 'open')->count(),
            'linked_to_users' => HelpwiseConversation::whereNotNull('user_id')->count(),
            'unlinked' => HelpwiseConversation::whereNull('user_id')->whereNotNull('customer_email')->count(),
            'total_messages' => HelpwiseMessage::count(),
            'inbound_messages' => HelpwiseMessage::where('direction', 'inbound')->count(),
            'outbound_messages' => HelpwiseMessage::where('direction', 'outbound')->count(),
            'conversations_today' => HelpwiseConversation::whereDate('created_at', today())->count(),
        ];
    }
}
