<?php

namespace App\Console\Commands;

use App\Models\HelpwiseReplyExample;
use App\Services\Helpwise\HelpwiseApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportHelpwiseSentReplies extends Command
{
    protected $signature = 'helpwise:import-sent-replies
                            {--limit=100 : Maximum number of conversations to fetch}
                            {--status=closed : Conversation status filter (closed, all)}';

    protected $description = 'Import sent replies from Helpwise API into helpwise_reply_examples for AI style matching';

    public function handle(HelpwiseApiService $api): int
    {
        $limit = (int) $this->option('limit');
        $status = $this->option('status');

        $this->info("Importing sent replies from Helpwise (status={$status}, limit={$limit})...");

        $imported = 0;
        $skipped = 0;
        $page = 1;
        $perPage = 50;

        $bar = $this->output->createProgressBar($limit);
        $bar->start();

        while ($imported + $skipped < $limit) {
            $params = [
                'page' => $page,
                'per_page' => min($perPage, $limit - $imported - $skipped),
            ];
            if ($status !== 'all') {
                $params['status'] = $status;
            }

            $conversations = $api->listConversations($params);

            if (!$conversations || empty($conversations['data'])) {
                break;
            }

            foreach ($conversations['data'] as $conversation) {
                if ($imported + $skipped >= $limit) {
                    break;
                }

                $conversationId = $conversation['id'] ?? null;
                if (!$conversationId) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $messagesData = $api->getMessages((string) $conversationId);
                $messages = $messagesData['data'] ?? $messagesData ?? [];

                if (!is_array($messages)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                foreach ($messages as $msg) {
                    // Only import outbound/sent messages (agent replies)
                    $type = $msg['type'] ?? $msg['message_type'] ?? '';
                    $direction = $msg['direction'] ?? '';
                    $isOutbound = in_array($type, ['reply', 'outbound', 'sent'])
                        || $direction === 'outbound'
                        || !empty($msg['is_reply']);

                    if (!$isOutbound) {
                        continue;
                    }

                    $body = $msg['body'] ?? $msg['text'] ?? '';
                    if (empty(trim(strip_tags($body)))) {
                        continue;
                    }

                    $bodyHash = hash('sha256', trim(strip_tags(strtolower($body))));
                    $externalId = (string) ($msg['id'] ?? $msg['message_id'] ?? uniqid('hw_'));

                    // Dedup by body_hash
                    if (HelpwiseReplyExample::where('body_hash', $bodyHash)->exists()) {
                        continue;
                    }

                    HelpwiseReplyExample::updateOrCreate(
                        ['external_message_id' => $externalId],
                        [
                            'conversation_id' => (string) $conversationId,
                            'subject' => $conversation['subject'] ?? null,
                            'sender_email' => $msg['from'] ?? $msg['from_email'] ?? null,
                            'reply_body' => $body,
                            'sent_at' => $msg['created_at'] ?? $msg['sent_at'] ?? null,
                            'category' => $conversation['category'] ?? $conversation['tag'] ?? null,
                            'body_hash' => $bodyHash,
                        ]
                    );

                    $imported++;
                }

                $bar->advance();
            }

            // Check if there's a next page
            if (!isset($conversations['meta']['next_page']) && !isset($conversations['next_page_url'])) {
                break;
            }

            $page++;
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done! Imported: {$imported}, Skipped: {$skipped}");

        Log::info('ImportHelpwiseSentReplies completed', [
            'imported' => $imported,
            'skipped' => $skipped,
        ]);

        return self::SUCCESS;
    }
}
