<?php

namespace App\Services;

use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use App\Models\Inbox\InboxComment;
use App\Models\Inbox\InboxAssignment;
use App\Models\Inbox\InboxCannedResponse;
use App\User;
use App\Services\Helpwise\HelpwiseReplyAiService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InboxService
{
    public function getConversations(array $filters = [])
    {
        $query = InboxConversation::with(['customer', 'assignee', 'latestMessage', 'latestInbound'])
            ->notSpam();

        if (!empty($filters['sent'])) {
            // Show all statuses for sent filter
        } elseif (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->whereIn('status', ['open', 'pending']);
        }

        if (!empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $query->unassigned();
            } else {
                $query->assignedTo((int) $filters['assigned_to']);
            }
        }

        if (!empty($filters['inbox'])) {
            $query->where('inbox', $filters['inbox']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['starred'])) {
            $query->where('is_starred', true);
        }

        if (!empty($filters['sent'])) {
            $query->whereHas('messages', fn($q) => $q->where('direction', 'outbound')->where('is_draft', false));
        }

        if (!empty($filters['follow_up'])) {
            $query->whereNotNull('follow_up_at')->where('follow_up_at', '<=', now());
        }

        if (!empty($filters['mentions'])) {
            $userId = auth()->id();
            $userName = auth()->user()->first_name;
            $query->whereHas('comments', function ($q) use ($userId, $userName) {
                $q->where(function ($q2) use ($userId, $userName) {
                    $q2->whereJsonContains('mentioned_user_ids', $userId)
                       ->orWhereJsonContains('mentioned_user_ids', (string) $userId)
                       ->orWhere('body', 'like', '%@' . $userName . '%');
                });
            });
        }

        if (!empty($filters['awaiting'])) {
            $query->where('status', 'open')
                ->whereHas('latestMessage', fn($q) => $q->where('direction', 'inbound'));
        }

        return $query->orderByDesc('updated_at')->paginate(25);
    }

    public function getConversation(int $id): InboxConversation
    {
        return InboxConversation::with([
            'messages' => fn($q) => $q->orderBy('created_at'),
            'comments.user',
            'customer',
            'assignee',
            'assignments.assignedBy',
            'assignments.assignedTo',
        ])->findOrFail($id);
    }

    public function sendReply(int $conversationId, string $body, int $userId, bool $isDraft = false, array $attachments = []): InboxMessage
    {
        $conversation = InboxConversation::findOrFail($conversationId);
        $user = User::findOrFail($userId);

        // Add signature — but only if the body doesn't already have one
        // (e.g. when sending an AI-generated draft that already includes it).
        if (!preg_match('/Skrivevarm hilsen/i', $body)) {
            $signature = "\n\nSkrivevarm hilsen,\n{$user->full_name}\nForfatterskolen / Easywrite / Indiemoon Publishing";
            $body = rtrim($body) . $signature;
        } else {
            $body = rtrim($body);
        }

        $message = InboxMessage::create([
            'conversation_id' => $conversation->id,
            'type' => 'reply',
            'direction' => 'outbound',
            'from_email' => $conversation->inbox ?? 'post@forfatterskolen.no',
            'from_name' => $user->full_name . ' — Forfatterskolen / Easywrite / Indiemoon Publishing',
            'to_email' => $conversation->customer_email,
            'subject' => 'Re: ' . $conversation->subject,
            'body' => $body,
            'body_plain' => strip_tags($body),
            'body_html' => collect(preg_split('/\r?\n\r?\n/', e($body)))->map(fn($p) => '<p style="margin:0 0 4px;">' . str_replace("\n", '<br>', trim($p)) . '</p>')->implode(''),
            'sent_by_user_id' => $userId,
            'is_draft' => $isDraft,
            'sent_at' => $isDraft ? null : now(),
        ]);

        if (!$isDraft) {
            // Send branded email
            try {
                $htmlBody = collect(preg_split('/\r?\n\r?\n/', e($body)))->map(fn($p) => '<p style="margin:0 0 4px;">' . str_replace("\n", '<br>', trim($p)) . '</p>')->implode('');
                $fromEmail = $conversation->inbox ?? 'post@forfatterskolen.no';

                $attachmentPaths = !empty($attachments) ? array_column($attachments, 'path') : null;

                dispatch(new \App\Jobs\AddMailToQueueJob(
                    $conversation->customer_email,
                    'Re: ' . $conversation->subject,
                    $htmlBody,
                    $fromEmail,
                    $user->full_name . ' — Forfatterskolen',
                    $attachmentPaths,
                    'inbox-reply',
                    $conversation->id
                ));

                Log::info('Inbox: email queued', [
                    'conversation_id' => $conversation->id,
                    'to' => $conversation->customer_email,
                    'by' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Inbox: email send failed', [
                    'conversation_id' => $conversation->id,
                    'error' => $e->getMessage(),
                ]);

                $message->update(['metadata' => ['send_error' => $e->getMessage()]]);
            }

            // Update conversation
            if (!$conversation->first_response_at) {
                $conversation->update(['first_response_at' => now()]);
            }
            $conversation->update(['status' => 'pending']);

            // Save as AI training example — every sent reply teaches the AI
            try {
                $latestInbound = $conversation->messages()
                    ->where('direction', 'inbound')
                    ->latest()
                    ->first();

                if ($latestInbound) {
                    $hash = md5($body);
                    $exists = \DB::table('helpwise_reply_examples')->where('body_hash', $hash)->exists();
                    if (!$exists) {
                        \DB::table('helpwise_reply_examples')->insert([
                            'external_message_id' => 'inbox-reply-' . $message->id,
                            'conversation_id' => $conversation->id,
                            'subject' => $latestInbound->subject ?? $conversation->subject,
                            'sender_email' => $latestInbound->from_email ?? $conversation->customer_email,
                            'reply_body' => $body,
                            'sent_at' => now(),
                            'category' => 'general',
                            'body_hash' => $hash,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Training example is optional
            }
        }

        return $message;
    }

    public function addComment(int $conversationId, int $userId, string $body, array $mentionedUserIds = []): InboxComment
    {
        return InboxComment::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'body' => $body,
            'mentioned_user_ids' => $mentionedUserIds,
        ]);
    }

    public function assignConversation(int $conversationId, int $assignToUserId, ?int $assignedByUserId = null, ?string $note = null): void
    {
        $conversation = InboxConversation::findOrFail($conversationId);
        $conversation->update(['assigned_to' => $assignToUserId]);

        InboxAssignment::create([
            'conversation_id' => $conversationId,
            'assigned_by' => $assignedByUserId ?? auth()->id(),
            'assigned_to' => $assignToUserId,
            'note' => $note,
            'created_at' => now(),
        ]);
    }

    public function updateStatus(int $conversationId, string $status): void
    {
        $conversation = InboxConversation::findOrFail($conversationId);
        $updateData = ['status' => $status];

        if ($status === 'closed') {
            $updateData['resolved_at'] = now();
        }

        $conversation->update($updateData);
    }

    public function toggleStar(int $conversationId): void
    {
        $conversation = InboxConversation::findOrFail($conversationId);
        $conversation->update(['is_starred' => !$conversation->is_starred]);
    }

    public function markAsSpam(int $conversationId): void
    {
        InboxConversation::findOrFail($conversationId)->update(['is_spam' => true, 'status' => 'closed']);
    }

    public function generateAiDraft(int $conversationId): ?InboxMessage
    {
        $conversation = InboxConversation::with('messages')->findOrFail($conversationId);

        // Build a HelpwiseConversation-compatible object for the AI service
        $helpwiseConv = new \App\HelpwiseConversation([
            'customer_email' => $conversation->customer_email,
            'customer_name' => $conversation->customer_name,
            'user_id' => $conversation->user_id,
            'subject' => $conversation->subject,
            'inbox' => $conversation->inbox,
        ]);
        $helpwiseConv->id = $conversation->id;

        // Get latest inbound message
        $latestInbound = $conversation->messages()
            ->where('direction', 'inbound')
            ->latest()
            ->first();

        $helpwiseMsg = null;
        if ($latestInbound) {
            $helpwiseMsg = new \App\HelpwiseMessage([
                'body' => $latestInbound->body,
                'body_plain' => $latestInbound->body_plain,
            ]);
        }

        try {
            $aiService = app(HelpwiseReplyAiService::class);
            $draftText = $aiService->generateDraftReply($helpwiseConv, $helpwiseMsg);

            if (!$draftText) return null;

            // Delete previous AI drafts
            InboxMessage::where('conversation_id', $conversationId)
                ->where('is_ai_draft', true)
                ->where('is_draft', true)
                ->delete();

            return InboxMessage::create([
                'conversation_id' => $conversationId,
                'type' => 'reply',
                'direction' => 'outbound',
                'from_name' => 'AI Draft',
                'body' => $draftText,
                'body_plain' => $draftText,
                'is_ai_draft' => true,
                'is_draft' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Inbox: AI draft failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getStats(): array
    {
        return [
            'open' => InboxConversation::where('status', 'open')->notSpam()->count(),
            'pending' => InboxConversation::where('status', 'pending')->notSpam()->count(),
            'unassigned' => InboxConversation::where('status', 'open')->whereNull('assigned_to')->notSpam()->count(),
            'closed_today' => InboxConversation::where('status', 'closed')->whereDate('resolved_at', today())->count(),
            'total' => InboxConversation::notSpam()->count(),
            'starred' => InboxConversation::where('is_starred', true)->notSpam()->count(),
            'mentions' => $this->getMentionsCount(),
            'awaiting' => InboxConversation::where('status', 'open')->notSpam()
                ->whereHas('latestMessage', fn($q) => $q->where('direction', 'inbound'))->count(),
        ];
    }

    private function getMentionsCount(): int
    {
        $userId = auth()->id();
        $userName = auth()->user()->first_name ?? '';

        return InboxConversation::notSpam()
            ->whereIn('status', ['open', 'pending'])
            ->whereHas('comments', function ($q) use ($userId, $userName) {
                $q->where(function ($q2) use ($userId, $userName) {
                    $q2->whereJsonContains('mentioned_user_ids', $userId)
                       ->orWhereJsonContains('mentioned_user_ids', (string) $userId)
                       ->orWhere('body', 'like', '%@' . $userName . '%');
                });
            })->count();
    }

    public function getTeamMembers()
    {
        return User::where('role', 1)
            ->whereIn('id', [5749, 1064, 6058, 1376, 5003]) // Annina, Kristine, Reservekonto, Sven I, Taran
            ->orderBy('first_name')->get();
    }

    public function getInboxes(): array
    {
        return InboxConversation::select('inbox')
            ->distinct()
            ->whereNotNull('inbox')
            ->pluck('inbox')
            ->toArray();
    }

    public function getCannedResponses()
    {
        return InboxCannedResponse::orderBy('title')->get();
    }

    public function importFromHelpwise(): int
    {
        $helpwiseConversations = \App\HelpwiseConversation::all();
        $imported = 0;

        foreach ($helpwiseConversations as $hw) {
            $existing = InboxConversation::where('helpwise_id', $hw->helpwise_id)->first();
            if ($existing) continue;

            $conv = InboxConversation::create([
                'subject' => $hw->subject,
                'customer_email' => $hw->customer_email,
                'customer_name' => $hw->customer_name,
                'user_id' => $hw->user_id,
                'status' => $hw->status,
                'inbox' => $hw->inbox,
                'source' => 'helpwise',
                'helpwise_id' => $hw->helpwise_id,
                'created_at' => $hw->helpwise_created_at ?? $hw->created_at,
            ]);

            // Import messages
            foreach ($hw->messages as $msg) {
                InboxMessage::create([
                    'conversation_id' => $conv->id,
                    'type' => $msg->channel === 'ai_draft' ? 'reply' : 'reply',
                    'direction' => $msg->direction,
                    'from_email' => $msg->from_email,
                    'from_name' => $msg->from_name,
                    'to_email' => $msg->to_email,
                    'subject' => $msg->subject,
                    'body' => $msg->body,
                    'body_plain' => $msg->body_plain,
                    'is_ai_draft' => $msg->channel === 'ai_draft',
                    'is_draft' => $msg->channel === 'ai_draft',
                    'created_at' => $msg->message_at ?? $msg->created_at,
                ]);
            }

            $imported++;
        }

        return $imported;
    }
}
