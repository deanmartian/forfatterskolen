<?php

namespace App\Jobs;

use App\HelpwiseConversation;
use App\HelpwiseMessage;
use App\HelpwiseWebhookLog;
use App\Services\Helpwise\HelpwiseApiService;
use App\Services\Helpwise\HelpwiseReplyAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessHelpwiseWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [60, 300];

    public function __construct(
        private readonly int $conversationId,
        private readonly string $eventType,
    ) {}

    public function handle(HelpwiseReplyAiService $aiService, HelpwiseApiService $apiService): void
    {
        $conversation = HelpwiseConversation::with('latestMessage')->find($this->conversationId);
        if (!$conversation) return;

        // Only generate AI drafts for inbound customer messages
        if (!in_array($this->eventType, ['conversation_created', 'reply_from_customer', 'reply_from_the_customer', 'customer_reply'])) {
            return;
        }

        $latestMessage = $conversation->latestMessage;
        if (!$latestMessage || $latestMessage->direction !== 'inbound') {
            return;
        }

        try {
            // Generate AI draft reply
            $draftText = $aiService->generateDraftReply($conversation, $latestMessage);

            if (!$draftText) {
                Log::warning('Helpwise AI: no draft generated', ['conversation_id' => $conversation->id]);
                return;
            }

            // Push draft to Helpwise (does NOT send - creates a draft for agent review)
            $result = $apiService->createDraft($conversation->helpwise_id, $draftText);

            // Store the draft as an outbound message locally
            HelpwiseMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => 'outbound',
                'from_name' => 'AI Draft',
                'body' => $draftText,
                'body_plain' => strip_tags($draftText),
                'channel' => 'ai_draft',
                'message_at' => now(),
                'raw_payload' => ['ai_generated' => true, 'helpwise_result' => $result],
            ]);

            // Tag the conversation
            $apiService->addTag($conversation->helpwise_id, 'ai-draft-ready');

            Log::info('Helpwise AI: draft pushed to Helpwise', [
                'conversation_id' => $conversation->id,
                'helpwise_id' => $conversation->helpwise_id,
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessHelpwiseWebhookJob failed', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessHelpwiseWebhookJob failed permanently', [
            'conversation_id' => $this->conversationId,
            'error' => $exception->getMessage(),
        ]);
    }
}
