<?php

namespace App\Http\Controllers;

use App\HelpwiseConversation;
use App\HelpwiseMessage;
use App\HelpwiseWebhookLog;
use App\Jobs\ProcessHelpwiseWebhookJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HelpwiseWebhookController extends Controller
{
    /**
     * Handle all Helpwise webhook events.
     *
     * Helpwise sends JSON payloads for events like:
     * - Conversation created
     * - Conversation assigned
     * - Conversation closed/reopened/deleted
     * - Reply from agent / Reply from customer
     * - Note added
     * - Tag applied
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $eventType = $this->detectEventType($request);

        // Verify webhook secret if configured
        $secret = config('services.helpwise.webhook_secret');
        if ($secret) {
            $providedSecret = $request->header('X-Helpwise-Secret')
                ?? $request->header('X-Webhook-Secret')
                ?? $request->input('secret_key')
                ?? $request->input('webhook_secret');

            if ($providedSecret !== $secret) {
                Log::warning('Helpwise webhook: invalid secret', ['ip' => $request->ip()]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        // Log everything
        $log = HelpwiseWebhookLog::create([
            'event_type' => $eventType,
            'payload' => $payload,
            'status' => 'received',
            'ip_address' => $request->ip(),
        ]);

        try {
            $conversationModel = $this->processEvent($eventType, $payload);
            $log->update(['status' => 'processed']);

            // Dispatch AI draft reply job for inbound messages
            if ($conversationModel) {
                ProcessHelpwiseWebhookJob::dispatch($conversationModel->id, $eventType);
            }
        } catch (\Exception $e) {
            Log::error('Helpwise webhook processing failed', [
                'event' => $eventType,
                'error' => $e->getMessage(),
            ]);
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function detectEventType(Request $request): string
    {
        // Try various header/payload locations Helpwise might use
        return $request->header('X-Helpwise-Event')
            ?? $request->header('X-Event-Type')
            ?? $request->input('event')
            ?? $request->input('event_type')
            ?? $request->input('type')
            ?? $request->input('webhook_type')
            ?? 'unknown';
    }

    private function processEvent(string $eventType, array $payload): ?HelpwiseConversation
    {
        // Normalize - the entire payload might BE the conversation, or it might be nested
        $conversationData = $payload['conversation'] ?? $payload['data'] ?? $payload;
        $messageData = $payload['message'] ?? $payload['reply'] ?? $payload['note'] ?? null;

        return match (strtolower(str_replace([' ', '-'], '_', $eventType))) {
            'conversation_created', 'conversation.created', 'new_conversation' =>
                $this->handleConversationCreated($conversationData, $messageData),

            'conversation_closed', 'conversation.closed' =>
                $this->handleConversationClosed($conversationData),

            'conversation_reopened', 'conversation.reopened' =>
                $this->handleConversationReopened($conversationData),

            'conversation_assigned', 'conversation.assigned' =>
                $this->handleConversationAssigned($conversationData),

            'conversation_deleted', 'conversation.deleted' =>
                $this->handleConversationDeleted($conversationData),

            'reply_from_agent', 'reply_from_the_agent', 'agent_reply', 'reply.agent' =>
                $this->handleAgentReply($conversationData, $messageData ?? $conversationData),

            'reply_from_customer', 'reply_from_the_customer', 'customer_reply', 'reply.customer' =>
                $this->handleCustomerReply($conversationData, $messageData ?? $conversationData),

            'added_note_in_conversation', 'note_added', 'note.created' =>
                $this->handleNoteAdded($conversationData, $messageData ?? $conversationData),

            'applied_tag_in_conversation', 'tag_applied', 'tag.applied' =>
                $this->handleTagApplied($conversationData),

            default => $this->handleUnknownEvent($eventType, $payload),
        };
    }

    private function handleConversationCreated(array $data, ?array $messageData): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($data);

        if ($messageData || !empty($data['body']) || !empty($data['message'])) {
            HelpwiseMessage::createFromWebhook($conversation->id, $messageData ?? $data);
        }

        Log::info('Helpwise: conversation created', [
            'helpwise_id' => $conversation->helpwise_id,
            'email' => $conversation->customer_email,
            'user_id' => $conversation->user_id,
        ]);

        return $conversation;
    }

    private function handleConversationClosed(array $data): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($data);
        $conversation->update(['status' => 'closed', 'helpwise_closed_at' => now()]);
        return $conversation;
    }

    private function handleConversationReopened(array $data): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($data);
        $conversation->update(['status' => 'open', 'helpwise_closed_at' => null]);
        return $conversation;
    }

    private function handleConversationAssigned(array $data): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($data);
        $conversation->update(['assigned_to' => $data['assigned_to'] ?? $data['assignee'] ?? $data['assignee_name'] ?? null]);
        return $conversation;
    }

    private function handleConversationDeleted(array $data): ?HelpwiseConversation
    {
        $helpwiseId = $data['id'] ?? $data['conversation_id'] ?? null;
        if ($helpwiseId) {
            $conversation = HelpwiseConversation::where('helpwise_id', (string) $helpwiseId)->first();
            $conversation?->update(['status' => 'closed']);
            return $conversation;
        }
        return null;
    }

    private function handleAgentReply(array $conversationData, array $messageData): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($conversationData);
        HelpwiseMessage::createFromWebhook($conversation->id, array_merge($messageData, ['type' => 'outbound']));
        return $conversation;
    }

    private function handleCustomerReply(array $conversationData, array $messageData): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($conversationData);
        $conversation->update(['status' => 'open']);
        HelpwiseMessage::createFromWebhook($conversation->id, array_merge($messageData, ['type' => 'inbound']));
        return $conversation;
    }

    private function handleNoteAdded(array $conversationData, array $noteData): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($conversationData);
        HelpwiseMessage::createFromWebhook($conversation->id, array_merge($noteData, ['type' => 'outbound', 'channel' => 'note']));
        return $conversation;
    }

    private function handleTagApplied(array $data): HelpwiseConversation
    {
        $conversation = HelpwiseConversation::findOrCreateFromWebhook($data);
        $newTags = $data['tags'] ?? $data['tag'] ?? null;
        if ($newTags) {
            $existing = $conversation->tags ?? [];
            if (is_string($newTags)) $newTags = [$newTags];
            $conversation->update(['tags' => array_unique(array_merge($existing, $newTags))]);
        }
        return $conversation;
    }

    private function handleUnknownEvent(string $eventType, array $payload): ?HelpwiseConversation
    {
        Log::info('Helpwise webhook: unknown event type', [
            'event' => $eventType,
            'keys' => array_keys($payload),
        ]);

        // Still try to create/update a conversation from the data
        $conversationData = $payload['conversation'] ?? $payload['data'] ?? $payload;
        if (isset($conversationData['id']) || isset($conversationData['conversation_id'])) {
            return HelpwiseConversation::findOrCreateFromWebhook($conversationData);
        }
        return null;
    }
}
