<?php

namespace App\Console\Commands;

use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use App\Services\HelpwiseImportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportHelpwiseHistory extends Command
{
    protected $signature = 'helpwise:import-history
        {--inbox-id=213732 : Helpwise inbox ID}
        {--pages=0 : Limit number of pages (0 = all)}';

    protected $description = 'Import all conversations and messages from Helpwise API into the inbox system';

    protected int $conversationsCreated = 0;
    protected int $conversationsSkipped = 0;
    protected int $messagesCreated = 0;
    protected int $apiCalls = 0;

    public function handle(): int
    {
        $inboxId = (int) $this->option('inbox-id');
        $maxPages = (int) $this->option('pages');

        $service = new HelpwiseImportService();

        $this->info("Starting Helpwise import for inbox {$inboxId}...");
        Log::info("HelpwiseImport: Starting import for inbox {$inboxId}");

        $pageToken = null;
        $page = 0;

        do {
            $page++;
            $this->line("Fetching conversations page {$page}...");

            $response = $service->getConversations($inboxId, $pageToken);
            $this->apiCalls++;
            $this->throttle();

            $conversations = $response['threads'] ?? $response['data'] ?? $response['conversations'] ?? [];
            $pageToken = $response['nextPageToken'] ?? null;

            if (empty($conversations)) {
                $this->warn("No conversations found on page {$page}");
                break;
            }

            $this->info("  Found " . count($conversations) . " conversations on page {$page}");

            foreach ($conversations as $conv) {
                $this->processConversation($conv, $inboxId, $service);
            }

            if ($maxPages > 0 && $page >= $maxPages) {
                $this->info("Reached page limit ({$maxPages})");
                break;
            }

        } while ($pageToken);

        $this->newLine();
        $this->info('=== Import Complete ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Conversations created', $this->conversationsCreated],
                ['Conversations skipped (existing)', $this->conversationsSkipped],
                ['Messages created', $this->messagesCreated],
                ['API calls made', $this->apiCalls],
                ['Pages processed', $page],
            ]
        );

        Log::info("HelpwiseImport: Complete", [
            'conversations_created' => $this->conversationsCreated,
            'conversations_skipped' => $this->conversationsSkipped,
            'messages_created' => $this->messagesCreated,
        ]);

        return self::SUCCESS;
    }

    protected function processConversation(array $conv, int $inboxId, HelpwiseImportService $service): void
    {
        $helpwiseId = (string) ($conv['id'] ?? '');
        if (!$helpwiseId) return;

        // Skip if already imported
        $existing = InboxConversation::where('helpwise_id', $helpwiseId)->first();
        if ($existing) {
            $this->conversationsSkipped++;
            return;
        }

        // Parse customer info - displayContact is a string name
        // We get the actual email from the conversation messages later
        $customerName = $conv['displayContact'] ?? null;
        $customerEmail = null;

        // Try to get email from contacts if available
        $contacts = $conv['contacts'] ?? [];
        if (!empty($contacts)) {
            $firstContact = reset($contacts);
            $emails = $firstContact['emails'] ?? [];
            $customerEmail = $emails[0] ?? null;
            $customerName = $firstContact['displayName'] ?? $customerName;
        }

        // Use snippet to extract email if still missing
        if (!$customerEmail) {
            $customerEmail = $customerName; // displayContact might be an email
        }

        // Create conversation
        $conversation = InboxConversation::create([
            'subject' => $conv['subject'] ?? '(no subject)',
            'customer_email' => $customerEmail,
            'customer_name' => $customerName,
            'status' => 'closed',
            'source' => 'helpwise_import',
            'helpwise_id' => $helpwiseId,
            'inbox' => 'post@forfatterskolen.no',
            'created_at' => isset($conv['date']) ? Carbon::parse($conv['date']) : now(),
            'updated_at' => isset($conv['date']) ? Carbon::parse($conv['date']) : now(),
        ]);

        $this->conversationsCreated++;
        $this->output->write('.');

        // Fetch full messages
        $messagesResponse = $service->getConversationMessages($inboxId, $helpwiseId);
        $this->apiCalls++;
        $this->throttle();

        $items = $messagesResponse['items'] ?? $messagesResponse['data']['items'] ?? $messagesResponse['data'] ?? [];

        foreach ($items as $item) {
            if (($item['type'] ?? '') !== 'email') continue;

            $data = $item['data'] ?? $item;
            $this->createMessage($conversation, $data);
        }
    }

    protected function createMessage(InboxConversation $conversation, array $data): void
    {
        // Determine direction: sentBy = outbound, no sentBy = inbound
        $isOutbound = isset($data['sentBy']) && !empty($data['sentBy']);
        $direction = $isOutbound ? 'outbound' : 'inbound';

        // Parse from field - format is {email: name} or object with email/name
        $from = $data['from'] ?? [];
        $fromEmail = null;
        $fromName = null;

        if (is_array($from) && !empty($from)) {
            // Could be {email: name} format or {email: "x", name: "y"}
            if (isset($from['email'])) {
                $fromEmail = $from['email'];
                $fromName = $from['name'] ?? null;
            } else {
                // {email: name} key-value format
                $fromEmail = array_key_first($from);
                $fromName = $from[$fromEmail] ?? null;
            }
        }

        // Parse to field
        $to = $data['to'] ?? [];
        $toEmail = null;

        if (is_array($to) && !empty($to)) {
            if (isset($to['email'])) {
                $toEmail = $to['email'];
            } else {
                $toEmail = array_key_first($to);
            }
        }

        // Use snippet as body since full content is remote
        $snippet = $data['snippet'] ?? $data['body'] ?? '';
        $subject = $data['subject'] ?? $conversation->subject;
        $date = isset($data['date']) ? Carbon::parse($data['date']) : $conversation->created_at;

        $metadata = [
            'source' => 'helpwise_import',
            'helpwise_data' => array_intersect_key($data, array_flip(['sentBy', 'messageId', 'id'])),
        ];

        // Tag outbound messages for AI learning
        if ($isOutbound) {
            $metadata['ai_training'] = true;
            $metadata['agent_name'] = $data['sentBy']['displayName'] ?? $data['sentBy']['firstname'] ?? null;
        }

        InboxMessage::create([
            'conversation_id' => $conversation->id,
            'type' => 'reply',
            'direction' => $direction,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'to_email' => $toEmail,
            'subject' => $subject,
            'body_plain' => $snippet,
            'body' => $snippet,
            'metadata' => $metadata,
            'sent_at' => $date,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        $this->messagesCreated++;
    }

    /**
     * Rate limit: ~100 req/min => sleep ~700ms between calls.
     */
    protected function throttle(): void
    {
        usleep(700000); // 700ms
    }
}
