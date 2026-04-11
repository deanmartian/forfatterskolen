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
            ->notSpam()
            ->visibleToUser(auth()->id());

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
        $conversation = InboxConversation::with([
            'messages' => fn($q) => $q->orderBy('created_at'),
            'comments.user',
            'customer',
            'assignee',
            'assignments.assignedBy',
            'assignments.assignedTo',
        ])->findOrFail($id);

        // Sikkerhet: hvis samtalen er privat og ikke tilhører innlogget admin,
        // nekt tilgang — selv om de gjetter ID-en eller bruker URL direkte.
        if ($conversation->private_to_user_id && $conversation->private_to_user_id !== auth()->id()) {
            abort(403, 'Denne samtalen tilhører en annen admins private inbox');
        }

        return $conversation;
    }

    public function sendReply(int $conversationId, string $body, int $userId, bool $isDraft = false, array $attachments = []): InboxMessage
    {
        $conversation = InboxConversation::findOrFail($conversationId);
        $user = User::findOrFail($userId);

        // Add signature — but only if the body doesn't already have one
        // (e.g. when sending an AI-generated draft that already includes it).
        // We check for both "Mvh" and the older "Skrivevarm hilsen" så gamle
        // utkast fortsatt funker. Hver bruker kan ha sin egen signatur lagret
        // i users.inbox_signature; ellers brukes en standardvariant med
        // brukerens fulle navn.
        if (!preg_match('/(Mvh\s|Skrivevarm hilsen|Med vennlig hilsen|Med venlig helsning)/i', $body)) {
            $signature = "\n\n" . $user->getInboxSignature();
            $body = rtrim($body) . $signature;
        } else {
            $body = rtrim($body);
        }

        // Convert markdown links + bare URLs to clickable HTML for the email.
        $bodyHtml = \App\Helpers\InboxBodyFormatter::toHtml($body);
        $htmlPlainNoTags = strip_tags($bodyHtml);

        $message = InboxMessage::create([
            'conversation_id' => $conversation->id,
            'type' => 'reply',
            'direction' => 'outbound',
            'from_email' => $conversation->inbox ?? 'post@forfatterskolen.no',
            'from_name' => $user->full_name . ' — Forfatterskolen / Easywrite / Indiemoon Publishing',
            'to_email' => $conversation->customer_email,
            'subject' => 'Re: ' . $conversation->subject,
            'body' => $body,
            'body_plain' => $htmlPlainNoTags,
            'body_html' => $bodyHtml,
            'sent_by_user_id' => $userId,
            'is_draft' => $isDraft,
            'sent_at' => $isDraft ? null : now(),
        ]);

        if (!$isDraft) {
            // Send branded email
            try {
                $htmlBody = $bodyHtml;
                $fromEmail = $conversation->inbox ?? 'post@forfatterskolen.no';

                $attachmentPaths = !empty($attachments) ? array_column($attachments, 'path') : null;

                // Sett Reply-To = from-adressen slik at kundens svar går
                // tilbake til samme inbox (særlig viktig for private inbokser
                // som sven.inge@ — uten dette ville svar havnet i support@).
                dispatch(new \App\Jobs\AddMailToQueueJob(
                    $conversation->customer_email,
                    'Re: ' . $conversation->subject,
                    $htmlBody,
                    $fromEmail,
                    $user->full_name . ' — Forfatterskolen',
                    $attachmentPaths,
                    'inbox-reply',
                    $conversation->id,
                    'emails.mail_to_queue',
                    $fromEmail,
                    $user->full_name . ' — Forfatterskolen'
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

    /**
     * Gjør en privat samtale offentlig. Kun eieren kan gjøre dette.
     * Etter at den er offentlig, ser alle admins den, og inbox-feltet
     * resettes til post@forfatterskolen.no slik at den havner i den
     * felles inboxen visuelt.
     */
    public function makePublic(int $conversationId, int $userId): bool
    {
        $conversation = InboxConversation::findOrFail($conversationId);

        // Sikkerhet: kun eieren av en privat samtale kan gjøre den offentlig
        if (!$conversation->private_to_user_id) {
            // Allerede offentlig — ingen endring
            return true;
        }

        if ($conversation->private_to_user_id !== $userId) {
            // Forsøker å gjøre noen andres private samtale offentlig — nektes
            return false;
        }

        $conversation->update([
            'private_to_user_id' => null,
            'inbox' => 'post@forfatterskolen.no',
        ]);

        Log::info('Inbox: privat samtale gjort offentlig', [
            'conversation_id' => $conversationId,
            'by_user_id' => $userId,
        ]);

        return true;
    }

    /**
     * Gjør en offentlig samtale privat for en bestemt admin-bruker.
     * Bare innloggede admins kan gjøre dette, og kun til seg selv.
     */
    public function makePrivate(int $conversationId, int $userId): bool
    {
        $conversation = InboxConversation::findOrFail($conversationId);

        // Hvis allerede privat for noen, nekt (med mindre det er samme user)
        if ($conversation->private_to_user_id && $conversation->private_to_user_id !== $userId) {
            return false;
        }

        $conversation->update([
            'private_to_user_id' => $userId,
        ]);

        Log::info('Inbox: samtale gjort privat', [
            'conversation_id' => $conversationId,
            'private_to_user_id' => $userId,
        ]);

        return true;
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

        // Build a HelpwiseConversation-compatible object for the AI service.
        // Vi inkluderer private_to_user_id slik at AI-tjenesten kan bruke
        // eierens signatur når draften genereres fra polleren (der auth()
        // er null).
        $helpwiseConv = new \App\HelpwiseConversation([
            'customer_email' => $conversation->customer_email,
            'customer_name' => $conversation->customer_name,
            'user_id' => $conversation->user_id,
            'subject' => $conversation->subject,
            'inbox' => $conversation->inbox,
            'private_to_user_id' => $conversation->private_to_user_id,
        ]);
        $helpwiseConv->id = $conversation->id;

        // Get latest inbound message
        $latestInbound = $conversation->messages()
            ->where('direction', 'inbound')
            ->latest()
            ->first();

        $helpwiseMsg = null;
        $fullEmailBody = null;
        if ($latestInbound) {
            // Strip e-post-sitater slik at AI fokuserer på det nye, ikke
            // den gamle e-postkjeden som henger med fra Gmail/Outlook.
            $cleanBody = \App\Helpers\EmailQuoteStripper::strip($latestInbound->body);
            $cleanPlain = \App\Helpers\EmailQuoteStripper::strip($latestInbound->body_plain);

            $helpwiseMsg = new \App\HelpwiseMessage([
                'body' => $cleanBody ?: $latestInbound->body,
                'body_plain' => $cleanPlain ?: $latestInbound->body_plain,
            ]);

            // Behold også den FULLE versjonen (med sitater) slik at AI-en
            // har tilgang til kontekst som ikke ligger i inbox_messages —
            // f.eks. svar fra Kristine sendt direkte fra hennes Gmail.
            $fullEmailBody = strip_tags($latestInbound->body_plain ?? $latestInbound->body ?? '');
        }

        try {
            $aiService = app(HelpwiseReplyAiService::class);
            $result = $aiService->generateDraftReply($helpwiseConv, $helpwiseMsg, $fullEmailBody);

            if (!$result) return null;

            $draftText = $result['text'] ?? '';
            $toolUses = $result['tool_uses'] ?? [];

            // Hvis AI kun foreslo verktøy uten tekst, lag en minimal kroppstekst
            if ($draftText === '' && !empty($toolUses)) {
                $draftText = 'Se foreslåtte handlinger under.';
            }

            if ($draftText === '' && empty($toolUses)) {
                return null;
            }

            // Slett tidligere AI-utkast OG tidligere suggested actions for denne samtalen
            $oldDrafts = InboxMessage::where('conversation_id', $conversationId)
                ->where('is_ai_draft', true)
                ->where('is_draft', true)
                ->pluck('id');

            if ($oldDrafts->isNotEmpty()) {
                \App\Models\AiToolAction::whereIn('inbox_message_id', $oldDrafts)
                    ->where('status', \App\Enums\AiToolActionStatus::Suggested->value)
                    ->delete();

                InboxMessage::whereIn('id', $oldDrafts)->delete();
            }

            // Opprett ny draft
            $draft = InboxMessage::create([
                'conversation_id' => $conversationId,
                'type' => 'reply',
                'direction' => 'outbound',
                'from_name' => 'AI Draft',
                'body' => $draftText,
                'body_plain' => $draftText,
                'is_ai_draft' => true,
                'is_draft' => true,
            ]);

            // Lagre suggested actions fra AI-en
            if (!empty($toolUses)) {
                $executor = app(\App\Services\AiTools\AiToolExecutor::class);
                foreach ($toolUses as $toolUse) {
                    $toolName = $toolUse['name'] ?? null;
                    $input = $toolUse['input'] ?? [];
                    if (!$toolName || !is_array($input)) continue;

                    try {
                        $executor->suggest($toolName, $input, $conversationId, $draft->id);
                    } catch (\Throwable $e) {
                        Log::warning('Inbox: kunne ikke lagre suggested action', [
                            'tool' => $toolName,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            return $draft;
        } catch (\Exception $e) {
            Log::error('Inbox: AI draft failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function getStats(): array
    {
        $userId = auth()->id();
        $base = fn() => InboxConversation::notSpam()->visibleToUser($userId);

        return [
            'open' => (clone $base())->where('status', 'open')->count(),
            'pending' => (clone $base())->where('status', 'pending')->count(),
            'unassigned' => (clone $base())->where('status', 'open')->whereNull('assigned_to')->count(),
            'closed_today' => (clone $base())->where('status', 'closed')->whereDate('resolved_at', today())->count(),
            'total' => (clone $base())->count(),
            'starred' => (clone $base())->where('is_starred', true)->count(),
            'mentions' => $this->getMentionsCount(),
            'awaiting' => (clone $base())->where('status', 'open')
                ->whereHas('latestMessage', fn($q) => $q->where('direction', 'inbound'))->count(),
            'snoozed' => (clone $base())->where('status', 'snoozed')
                ->where('snoozed_until', '>', now())->count(),
            'categories' => (clone $base())->whereIn('status', ['open', 'pending'])
                ->whereNotNull('category')->where('category', '!=', '')
                ->selectRaw('category, count(*) as cnt')
                ->groupBy('category')
                ->pluck('cnt', 'category')
                ->toArray(),
        ];
    }

    private function getMentionsCount(): int
    {
        $userId = auth()->id();
        $userName = auth()->user()->first_name ?? '';

        return InboxConversation::notSpam()
            ->visibleToUser($userId)
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
            ->visibleToUser(auth()->id())
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
