<?php

namespace App\Console\Commands;

use App\Jobs\ProcessInboxWebhookJob;
use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReceiveEmailCommand extends Command
{
    protected $signature = 'inbox:receive-email';
    protected $description = 'Motta rå e-post fra STDIN (for cPanel mail piping)';

    public function handle(): int
    {
        $raw = file_get_contents('php://stdin');

        if (empty($raw)) {
            Log::warning('inbox:receive-email: ingen data mottatt fra STDIN');
            return 1;
        }

        Log::info('inbox:receive-email: e-post mottatt', ['bytes' => strlen($raw)]);

        try {
            $parsed = $this->parseEmail($raw);

            if (empty($parsed['from_email'])) {
                Log::warning('inbox:receive-email: kunne ikke hente avsender-epost');
                return 1;
            }

            // Finn eller opprett samtale
            $conversation = InboxConversation::findOrCreateFromEmail([
                'from_email' => $parsed['from_email'],
                'from_name' => $parsed['from_name'],
                'subject' => $parsed['subject'],
                'to_email' => $parsed['to_email'],
                'source' => 'pipe',
            ]);

            // Koble til bruker
            $user = User::where('email', $parsed['from_email'])->first();
            if ($user && !$conversation->user_id) {
                $conversation->update(['user_id' => $user->id]);
            }

            // Opprett melding
            $message = InboxMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => 'inbound',
                'from_email' => $parsed['from_email'],
                'from_name' => $parsed['from_name'],
                'body' => $parsed['body_html'] ?: $parsed['body_plain'],
                'body_plain' => $parsed['body_plain'],
                'subject' => $parsed['subject'],
                'is_draft' => false,
                'is_ai_draft' => false,
                'metadata' => [
                    'source' => 'cpanel_pipe',
                    'to_email' => $parsed['to_email'],
                    'received_at' => now()->toISOString(),
                ],
            ]);

            Log::info('inbox:receive-email: melding opprettet', [
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
                'from' => $parsed['from_email'],
            ]);

            // Generer AI-utkast
            ProcessInboxWebhookJob::dispatch($conversation->id, $message->id);

            $this->info("E-post mottatt og lagret (samtale #{$conversation->id})");
            return 0;
        } catch (\Exception $e) {
            Log::error('inbox:receive-email feilet', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error('Feil ved mottak av e-post: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Enkel parsing av rå e-post (headers + body).
     */
    private function parseEmail(string $raw): array
    {
        // Splitt headers og body
        $parts = preg_split('/\r?\n\r?\n/', $raw, 2);
        $headerBlock = $parts[0] ?? '';
        $body = $parts[1] ?? '';

        // Parse headers
        $headers = $this->parseHeaders($headerBlock);

        $fromRaw = $headers['from'] ?? '';
        $toRaw = $headers['to'] ?? '';

        // Forsok a hente HTML fra multipart
        $bodyHtml = '';
        $bodyPlain = $body;

        $contentType = $headers['content-type'] ?? '';
        if (str_contains($contentType, 'multipart/')) {
            $parsed = $this->parseMultipart($contentType, $body);
            $bodyPlain = $parsed['plain'] ?: strip_tags($parsed['html'] ?: $body);
            $bodyHtml = $parsed['html'] ?: '';
        }

        return [
            'from_email' => $this->extractEmail($fromRaw),
            'from_name' => $this->extractName($fromRaw),
            'to_email' => $this->extractEmail($toRaw),
            'subject' => $headers['subject'] ?? '(Uten emne)',
            'body_html' => $bodyHtml,
            'body_plain' => $bodyPlain,
        ];
    }

    private function parseHeaders(string $headerBlock): array
    {
        $headers = [];
        $current = '';
        foreach (preg_split('/\r?\n/', $headerBlock) as $line) {
            if (preg_match('/^\s+/', $line) && $current) {
                // Continuation of previous header
                $headers[$current] .= ' ' . trim($line);
            } elseif (preg_match('/^([^:]+):\s*(.*)$/', $line, $m)) {
                $current = strtolower(trim($m[1]));
                $headers[$current] = trim($m[2]);
            }
        }
        return $headers;
    }

    private function parseMultipart(string $contentType, string $body): array
    {
        $result = ['plain' => '', 'html' => ''];

        if (preg_match('/boundary=["\']?([^"\';\s]+)/i', $contentType, $m)) {
            $boundary = $m[1];
            $sections = explode('--' . $boundary, $body);

            foreach ($sections as $section) {
                if (str_contains($section, 'text/plain')) {
                    $parts = preg_split('/\r?\n\r?\n/', $section, 2);
                    $result['plain'] = trim($parts[1] ?? '');
                } elseif (str_contains($section, 'text/html')) {
                    $parts = preg_split('/\r?\n\r?\n/', $section, 2);
                    $result['html'] = trim($parts[1] ?? '');
                }
            }
        }

        return $result;
    }

    private function extractEmail(string $from): string
    {
        if (preg_match('/<([^>]+)>/', $from, $matches)) {
            return $matches[1];
        }
        return trim($from);
    }

    private function extractName(string $from): ?string
    {
        if (preg_match('/^(.+?)\s*</', $from, $matches)) {
            return trim($matches[1], ' "\'');
        }
        return null;
    }
}
