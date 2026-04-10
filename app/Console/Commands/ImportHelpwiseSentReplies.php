<?php

namespace App\Console\Commands;

use App\Models\HelpwiseReplyExample;
use App\Services\HelpwiseImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportHelpwiseSentReplies extends Command
{
    protected $signature = 'helpwise:import-sent-replies
                            {--inbox-id=213732 : Helpwise inbox ID}
                            {--label-id=1 : Helpwise label ID (1=Sent, 7=Closed, 14=All)}
                            {--pages=0 : Max pages to fetch (0 = all)}';

    protected $description = 'Import sent replies from Helpwise API into helpwise_reply_examples for AI style matching';

    public function handle(): int
    {
        $inboxId = (int) $this->option('inbox-id');
        $labelId = (int) $this->option('label-id');
        $maxPages = (int) $this->option('pages');

        $service = new HelpwiseImportService();

        $this->info("Importing sent replies from Helpwise inbox {$inboxId}...");

        $imported = 0;
        $skipped = 0;
        $page = 0;
        $pageToken = null;

        do {
            $page++;
            $this->line("Fetching conversations page {$page}...");

            $response = $service->getConversations($inboxId, $pageToken, $labelId);
            $conversations = $response['threads'] ?? $response['data'] ?? $response['conversations'] ?? [];
            $pageToken = $response['nextPageToken'] ?? null;

            if (empty($conversations)) {
                $this->warn("No conversations on page {$page}, stopping.");
                break;
            }

            $this->info("  Found " . count($conversations) . " conversations");

            foreach ($conversations as $conversation) {
                $conversationId = $conversation['id'] ?? $conversation['uuid'] ?? null;
                if (!$conversationId) {
                    $skipped++;
                    continue;
                }

                // Fetch full conversation with messages
                $convData = $service->getConversationMessages($inboxId, (string) $conversationId);
                $messages = $convData['messages'] ?? $convData['data'] ?? [];
                sleep(1); // Rate limit

                if (!is_array($messages)) {
                    $skipped++;
                    continue;
                }

                foreach ($messages as $msg) {
                    // Only import outbound/sent messages (agent replies)
                    $type = $msg['type'] ?? $msg['message_type'] ?? '';
                    $direction = $msg['direction'] ?? '';
                    $isOutbound = in_array($type, ['reply', 'outbound', 'sent'])
                        || $direction === 'outbound'
                        || !empty($msg['is_reply'])
                        || ($msg['from_type'] ?? '') === 'agent';

                    if (!$isOutbound) {
                        continue;
                    }

                    $body = $msg['body'] ?? $msg['text'] ?? $msg['content'] ?? '';
                    if (empty(trim(strip_tags($body)))) {
                        continue;
                    }

                    $bodyHash = hash('sha256', trim(strip_tags(strtolower($body))));
                    $externalId = (string) ($msg['id'] ?? $msg['message_id'] ?? uniqid('hw_'));

                    // Dedup by body_hash
                    if (HelpwiseReplyExample::where('body_hash', $bodyHash)->exists()) {
                        continue;
                    }

                    $subject = $conversation['subject'] ?? $conversation['title'] ?? null;

                    HelpwiseReplyExample::updateOrCreate(
                        ['external_message_id' => $externalId],
                        [
                            'conversation_id' => (string) $conversationId,
                            'subject' => $subject,
                            'sender_email' => $msg['from'] ?? $msg['from_email'] ?? null,
                            'reply_body' => $body,
                            'sent_at' => $msg['created_at'] ?? $msg['sent_at'] ?? null,
                            'category' => $conversation['category'] ?? $conversation['tag'] ?? null,
                            'body_hash' => $bodyHash,
                        ]
                    );

                    $imported++;
                }
            }

            if ($maxPages > 0 && $page >= $maxPages) {
                $this->info("Reached page limit ({$maxPages})");
                break;
            }

            sleep(2); // Rate limit between pages

        } while ($pageToken);

        $this->newLine();
        $this->info("Done! Imported: {$imported}, Skipped: {$skipped}, Pages: {$page}");

        Log::info('ImportHelpwiseSentReplies completed', [
            'imported' => $imported,
            'skipped' => $skipped,
        ]);

        return self::SUCCESS;
    }
}
