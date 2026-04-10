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
        {--pages=0 : Limit number of pages (0 = all)}
        {--phase=all : Phase to run: list, messages, or all}
        {--batch=40 : Number of message fetches per run (respects rate limit)}
        {--label=14 : Label filter: 14=All, 7=Closed, 1=Sent, 0=Assigned}';

    protected $description = 'Import all conversations and messages from Helpwise API into the inbox system';

    protected int $conversationsCreated = 0;
    protected int $conversationsSkipped = 0;
    protected int $messagesCreated = 0;
    protected int $apiCalls = 0;

    public function handle(): int
    {
        $inboxId = (int) $this->option('inbox-id');
        $maxPages = (int) $this->option('pages');
        $phase = $this->option('phase');
        $batch = (int) $this->option('batch');

        $labelId = (int) $this->option('label');
        $service = new HelpwiseImportService();

        if ($phase === 'list' || $phase === 'all') {
            $this->phaseListConversations($service, $inboxId, $maxPages, $labelId);
        }

        if ($phase === 'messages' || $phase === 'all') {
            $this->phaseFetchMessages($service, $inboxId, $batch);
        }

        $this->newLine();
        $this->info('=== Import Complete ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Conversations created', $this->conversationsCreated],
                ['Conversations skipped (existing)', $this->conversationsSkipped],
                ['Messages created', $this->messagesCreated],
                ['API calls made', $this->apiCalls],
            ]
        );

        Log::info("HelpwiseImport: Complete", [
            'conversations_created' => $this->conversationsCreated,
            'conversations_skipped' => $this->conversationsSkipped,
            'messages_created' => $this->messagesCreated,
        ]);

        return self::SUCCESS;
    }

    /**
     * Phase 1: List all conversations (1 API call per page, no message fetching).
     */
    protected function phaseListConversations(HelpwiseImportService $service, int $inboxId, int $maxPages, int $labelId = 14): void
    {
        $labels = [14 => 'All', 0 => 'Assigned', 7 => 'Closed', 1 => 'Sent', 8 => 'Spam', 5 => 'Trash'];
        $this->info("=== Phase 1: Listing conversations (label: " . ($labels[$labelId] ?? $labelId) . ") ===");
        $pageToken = null;
        $page = 0;

        do {
            $page++;
            $this->line("Fetching page {$page}...");

            $response = $service->getConversations($inboxId, $pageToken, $labelId);
            $this->apiCalls++;

            $conversations = $response['threads'] ?? $response['data'] ?? [];
            $pageToken = $response['nextPageToken'] ?? null;

            if (empty($conversations)) {
                $this->warn("No conversations on page {$page}");
                break;
            }

            $this->info("  Found " . count($conversations) . " conversations");

            foreach ($conversations as $conv) {
                $this->createConversationFromList($conv);
            }

            if ($maxPages > 0 && $page >= $maxPages) {
                $this->info("Reached page limit ({$maxPages})");
                break;
            }

            usleep(600000); // 600ms between pages

        } while ($pageToken);

        $total = InboxConversation::count();
        $this->info("Phase 1 done. Total conversations in DB: {$total}");
    }

    /**
     * Phase 2: Fetch messages for conversations that don't have any yet.
     */
    protected function phaseFetchMessages(HelpwiseImportService $service, int $inboxId, int $batch): void
    {
        $this->info("=== Phase 2: Fetching messages (batch of {$batch}) ===");

        // Find conversations without messages
        $needMessages = InboxConversation::whereDoesntHave('messages')
            ->whereNotNull('helpwise_id')
            ->limit($batch)
            ->get();

        if ($needMessages->isEmpty()) {
            $this->info("All conversations already have messages!");
            return;
        }

        $this->info("Found {$needMessages->count()} conversations needing messages");

        $bar = $this->output->createProgressBar($needMessages->count());
        $bar->start();

        foreach ($needMessages as $conversation) {
            $messagesResponse = $service->getConversationMessages($inboxId, $conversation->helpwise_id);
            $this->apiCalls++;

            $items = $messagesResponse['items'] ?? $messagesResponse['data']['items'] ?? $messagesResponse['data'] ?? [];

            foreach ($items as $item) {
                if (($item['type'] ?? '') !== 'email') continue;
                $data = $item['data'] ?? $item;
                $this->createMessage($conversation, $data);
            }

            $bar->advance();
            sleep(2); // 2s between calls = ~30 req/min, well under 100/period
        }

        $bar->finish();
        $this->newLine();

        $remaining = InboxConversation::whereDoesntHave('messages')
            ->whereNotNull('helpwise_id')
            ->count();

        if ($remaining > 0) {
            $this->warn("{$remaining} conversations still need messages. Run again with --phase=messages");
        }
    }

    /**
     * Create a conversation from list data (no message fetching).
     */
    protected function createConversationFromList(array $conv): void
    {
        $helpwiseId = (string) ($conv['id'] ?? '');
        if (!$helpwiseId) return;

        if (InboxConversation::where('helpwise_id', $helpwiseId)->exists()) {
            $this->conversationsSkipped++;
            return;
        }

        $customerName = $conv['displayContact'] ?? null;
        $customerEmail = null;

        // Try contacts array (old API format)
        $contacts = $conv['contacts'] ?? [];
        if (!empty($contacts)) {
            $firstContact = reset($contacts);
            $emails = $firstContact['emails'] ?? [];
            $customerEmail = $emails[0] ?? null;
            $customerName = $firstContact['displayName'] ?? $customerName;
        }

        // Try latestEmail from/to (dev-apis format)
        if (!$customerEmail && isset($conv['latestEmail'])) {
            $from = $conv['latestEmail']['from'] ?? [];
            if (is_array($from)) {
                $customerEmail = array_key_first($from);
                $customerName = $customerName ?? reset($from);
            }
        }

        if (!$customerEmail) {
            $customerEmail = $customerName ?? 'unknown@unknown.com';
        }

        $subject = $conv['subject'] ?? $conv['latestEmail']['subject'] ?? '(no subject)';
        $date = $conv['date'] ?? $conv['latestEmail']['date'] ?? null;

        InboxConversation::create([
            'subject' => $subject,
            'customer_email' => $customerEmail,
            'customer_name' => $customerName,
            'status' => 'closed',
            'source' => 'helpwise_import',
            'helpwise_id' => $helpwiseId,
            'inbox' => 'post@forfatterskolen.no',
            'created_at' => $date ? Carbon::parse($date) : now(),
            'updated_at' => $date ? Carbon::parse($date) : now(),
        ]);

        $this->conversationsCreated++;
        $this->output->write('.');
    }

    protected function createMessage(InboxConversation $conversation, array $data): void
    {
        $isOutbound = isset($data['sentBy']) && !empty($data['sentBy']);
        $direction = $isOutbound ? 'outbound' : 'inbound';

        $from = $data['from'] ?? [];
        $fromEmail = null;
        $fromName = null;

        if (is_array($from) && !empty($from)) {
            if (isset($from['email'])) {
                $fromEmail = $from['email'];
                $fromName = $from['name'] ?? null;
            } else {
                $fromEmail = array_key_first($from);
                $fromName = $from[$fromEmail] ?? null;
            }
        }

        $to = $data['to'] ?? [];
        $toEmail = null;
        if (is_array($to) && !empty($to)) {
            $toEmail = isset($to['email']) ? $to['email'] : array_key_first($to);
        }

        $snippet = $data['snippet'] ?? $data['body'] ?? '';
        $subject = $data['subject'] ?? $conversation->subject;
        $date = isset($data['date']) ? Carbon::parse($data['date']) : $conversation->created_at;

        $metadata = [
            'source' => 'helpwise_import',
            'helpwise_data' => array_intersect_key($data, array_flip(['sentBy', 'messageId', 'id'])),
        ];

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
}
