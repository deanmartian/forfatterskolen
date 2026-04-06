<?php

namespace App\Jobs;

use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use App\Services\Helpwise\HelpwiseReplyAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessInboxWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [60, 300];

    public function __construct(
        private readonly int $conversationId,
        private readonly int $messageId,
    ) {}

    public function handle(HelpwiseReplyAiService $aiService): void
    {
        $conversation = InboxConversation::with('latestInbound')->find($this->conversationId);
        if (!$conversation) {
            Log::warning('ProcessInboxWebhookJob: samtale ikke funnet', ['conversation_id' => $this->conversationId]);
            return;
        }

        $message = InboxMessage::find($this->messageId);
        if (!$message || $message->direction !== 'inbound') {
            Log::warning('ProcessInboxWebhookJob: melding ikke funnet eller ikke inbound', ['message_id' => $this->messageId]);
            return;
        }

        try {
            $draftText = $aiService->generateInboxDraftReply($conversation, $message);

            if (!$draftText) {
                Log::warning('ProcessInboxWebhookJob: AI genererte ikke utkast', ['conversation_id' => $conversation->id]);
                return;
            }

            // Lagre AI-utkast som melding (sendes IKKE automatisk)
            InboxMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => 'outbound',
                'from_name' => 'AI-utkast',
                'body' => $draftText,
                'body_plain' => strip_tags($draftText),
                'is_draft' => true,
                'is_ai_draft' => true,
                'metadata' => ['ai_generated' => true, 'source_message_id' => $this->messageId],
            ]);

            Log::info('ProcessInboxWebhookJob: AI-utkast opprettet', [
                'conversation_id' => $conversation->id,
                'message_id' => $this->messageId,
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessInboxWebhookJob feilet', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessInboxWebhookJob feilet permanent', [
            'conversation_id' => $this->conversationId,
            'message_id' => $this->messageId,
            'error' => $exception->getMessage(),
        ]);
    }
}
