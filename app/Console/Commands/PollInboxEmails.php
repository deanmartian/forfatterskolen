<?php

namespace App\Console\Commands;

use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollInboxEmails extends Command
{
    protected $signature = 'inbox:poll {--mark-read : Mark fetched emails as read} {--since= : Only fetch emails since date (Y-m-d)}';
    protected $description = 'Poll IMAP mailbox for new emails and import to Inbox';

    public function handle(): int
    {
        $host = '{imap.domeneshop.no:993/imap/ssl}INBOX';
        $username = env('IMAP_USERNAME', 'forfatterskolen3');
        $password = env('IMAP_PASSWORD', '');

        if (!$password) {
            $this->error('IMAP_PASSWORD not set in .env');
            return self::FAILURE;
        }

        $inbox = @imap_open($host, $username, $password);

        if (!$inbox) {
            $this->error('Kunne ikke koble til IMAP: ' . imap_last_error());
            return self::FAILURE;
        }

        // Get emails — filter by date if specified
        $since = $this->option('since') ?: date('Y-m-d');
        $sinceFormatted = date('d-M-Y', strtotime($since));
        $emails = imap_search($inbox, 'SINCE "' . $sinceFormatted . '" UNSEEN');

        if (!$emails) {
            $this->info('Ingen nye e-poster.');
            imap_close($inbox);
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($emails as $emailNumber) {
            try {
                $header = imap_headerinfo($inbox, $emailNumber);
                $structure = imap_fetchstructure($inbox, $emailNumber);

                // Extract sender
                $fromEmail = $header->from[0]->mailbox . '@' . $header->from[0]->host;
                $fromName = isset($header->from[0]->personal)
                    ? imap_utf8($header->from[0]->personal)
                    : $fromEmail;

                // Extract subject
                $subject = isset($header->subject) ? imap_utf8($header->subject) : '(Ingen emne)';

                // Extract body
                $body = $this->getBody($inbox, $emailNumber, $structure);

                // Skip auto-replies and mailer-daemon
                if (str_contains(strtolower($fromEmail), 'mailer-daemon') ||
                    str_contains(strtolower($fromEmail), 'noreply') ||
                    str_contains(strtolower($subject), 'returned mail') ||
                    str_contains(strtolower($subject), 'delivery status')) {
                    if ($this->option('mark-read')) {
                        imap_setflag_full($inbox, (string) $emailNumber, '\\Seen');
                    }
                    continue;
                }

                // Find or create conversation
                $conversation = InboxConversation::where('customer_email', $fromEmail)
                    ->where('subject', $subject)
                    ->whereIn('status', ['open', 'pending'])
                    ->first();

                if (!$conversation) {
                    // Check for Re: subjects
                    $cleanSubject = preg_replace('/^(Re|Sv|Fwd|VS):\s*/i', '', $subject);
                    $conversation = InboxConversation::where('customer_email', $fromEmail)
                        ->where(function ($q) use ($subject, $cleanSubject) {
                            $q->where('subject', $subject)
                              ->orWhere('subject', $cleanSubject)
                              ->orWhere('subject', 'Re: ' . $cleanSubject)
                              ->orWhere('subject', 'Sv: ' . $cleanSubject)
                              ->orWhere('subject', 'VS: ' . $cleanSubject);
                        })
                        ->latest()
                        ->first();
                }

                if (!$conversation) {
                    $conversation = InboxConversation::create([
                        'subject' => $subject,
                        'customer_email' => $fromEmail,
                        'customer_name' => $fromName,
                        'status' => 'open',
                        'source' => 'imap',
                        'inbox' => 'post@forfatterskolen.no',
                    ]);

                    // Link to user
                    $user = User::where('email', $fromEmail)->first();
                    if ($user) {
                        $conversation->update([
                            'user_id' => $user->id,
                            'customer_name' => $user->full_name,
                        ]);
                    }
                } else {
                    // Reopen if closed
                    if ($conversation->status === 'closed') {
                        $conversation->update(['status' => 'open']);
                    }
                }

                // Check for duplicate
                $messageId = isset($header->message_id) ? $header->message_id : null;
                if ($messageId) {
                    $exists = InboxMessage::where('message_id_header', $messageId)->exists();
                    if ($exists) {
                        if ($this->option('mark-read')) {
                            imap_setflag_full($inbox, (string) $emailNumber, '\\Seen');
                        }
                        continue;
                    }
                }

                // Extract attachments
                $attachments = $this->getAttachments($inbox, $emailNumber, $structure);

                // Create message
                $date = isset($header->date) ? \Carbon\Carbon::parse($header->date) : now();

                InboxMessage::create([
                    'conversation_id' => $conversation->id,
                    'type' => 'reply',
                    'direction' => 'inbound',
                    'from_email' => $fromEmail,
                    'from_name' => $fromName,
                    'to_email' => 'post@forfatterskolen.no',
                    'subject' => $subject,
                    'body' => $body,
                    'body_plain' => strip_tags($body),
                    'message_id_header' => $messageId,
                    'attachments' => !empty($attachments) ? $attachments : null,
                    'sent_at' => $date,
                ]);

                // Mark as read
                if ($this->option('mark-read')) {
                    imap_setflag_full($inbox, (string) $emailNumber, '\\Seen');
                }

                // Generate AI draft
                try {
                    dispatch(new \App\Jobs\ProcessInboxWebhookJob($conversation->id, $conversation->messages()->latest()->first()->id));
                } catch (\Exception $e) {
                    // AI draft is optional
                }

                $count++;
                $this->line("Importert: {$fromName} — {$subject}");

            } catch (\Exception $e) {
                $this->warn("Feil ved import av e-post #{$emailNumber}: " . $e->getMessage());
                Log::error('IMAP import error', ['email' => $emailNumber, 'error' => $e->getMessage()]);
            }
        }

        imap_close($inbox);

        $this->info("Ferdig! {$count} nye e-poster importert.");
        Log::info("IMAP poll: {$count} emails imported");

        return self::SUCCESS;
    }

    private function getBody($inbox, $emailNumber, $structure): string
    {
        // Simple message
        if (!isset($structure->parts)) {
            $body = imap_fetchbody($inbox, $emailNumber, '1');
            return $this->decodeBody($body, $structure->encoding ?? 0);
        }

        // Multipart — find text/plain or text/html
        $plainBody = '';
        $htmlBody = '';

        foreach ($structure->parts as $i => $part) {
            $partNumber = (string) ($i + 1);

            if ($part->type === 0) { // TEXT
                $subtype = strtolower($part->subtype ?? '');
                $content = imap_fetchbody($inbox, $emailNumber, $partNumber);
                $decoded = $this->decodeBody($content, $part->encoding ?? 0);

                // Check charset
                if (isset($part->parameters)) {
                    foreach ($part->parameters as $param) {
                        if (strtolower($param->attribute) === 'charset' && strtolower($param->value) !== 'utf-8') {
                            $decoded = mb_convert_encoding($decoded, 'UTF-8', $param->value);
                        }
                    }
                }

                if ($subtype === 'plain') {
                    $plainBody = $decoded;
                } elseif ($subtype === 'html') {
                    $htmlBody = $decoded;
                }
            }

            // Check nested multipart
            if (isset($part->parts)) {
                foreach ($part->parts as $j => $subPart) {
                    $subPartNumber = $partNumber . '.' . ($j + 1);
                    if ($subPart->type === 0) {
                        $subtype = strtolower($subPart->subtype ?? '');
                        $content = imap_fetchbody($inbox, $emailNumber, $subPartNumber);
                        $decoded = $this->decodeBody($content, $subPart->encoding ?? 0);

                        if ($subtype === 'plain' && !$plainBody) {
                            $plainBody = $decoded;
                        } elseif ($subtype === 'html' && !$htmlBody) {
                            $htmlBody = $decoded;
                        }
                    }
                }
            }
        }

        // Prefer plain text, fallback to stripped HTML
        if ($plainBody) {
            return $plainBody;
        }
        if ($htmlBody) {
            return strip_tags($htmlBody);
        }

        return imap_fetchbody($inbox, $emailNumber, '1');
    }

    private function getAttachments($inbox, $emailNumber, $structure): array
    {
        $attachments = [];
        if (!isset($structure->parts)) return $attachments;

        $storagePath = storage_path('app/inbox-attachments');
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        foreach ($structure->parts as $i => $part) {
            $partNumber = (string) ($i + 1);

            // Check disposition for attachment
            $isAttachment = false;
            $filename = null;

            if (isset($part->disposition) && strtolower($part->disposition) === 'attachment') {
                $isAttachment = true;
            }

            // Get filename from parameters
            if (isset($part->dparameters)) {
                foreach ($part->dparameters as $param) {
                    if (strtolower($param->attribute) === 'filename') {
                        $filename = imap_utf8($param->value);
                        $isAttachment = true;
                    }
                }
            }
            if (!$filename && isset($part->parameters)) {
                foreach ($part->parameters as $param) {
                    if (strtolower($param->attribute) === 'name') {
                        $filename = imap_utf8($param->value);
                        $isAttachment = true;
                    }
                }
            }

            if (!$isAttachment || !$filename) continue;

            try {
                $content = imap_fetchbody($inbox, $emailNumber, $partNumber);
                $decoded = $this->decodeBody($content, $part->encoding ?? 0);

                $safeFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
                $filePath = $storagePath . '/' . $safeFilename;
                file_put_contents($filePath, $decoded);

                $attachments[] = [
                    'filename' => $filename,
                    'path' => 'inbox-attachments/' . $safeFilename,
                    'size' => strlen($decoded),
                    'mime' => $this->getMimeType($part),
                ];
            } catch (\Exception $e) {
                Log::warning('Kunne ikke lagre vedlegg: ' . $filename . ' - ' . $e->getMessage());
            }
        }

        return $attachments;
    }

    private function getMimeType($part): string
    {
        $types = ['text', 'multipart', 'message', 'application', 'audio', 'image', 'video', 'model', 'other'];
        $type = $types[$part->type] ?? 'application';
        $subtype = strtolower($part->subtype ?? 'octet-stream');
        return $type . '/' . $subtype;
    }

    private function decodeBody(string $body, int $encoding): string
    {
        return match ($encoding) {
            3 => base64_decode($body),        // BASE64
            4 => quoted_printable_decode($body), // QUOTED-PRINTABLE
            default => $body,
        };
    }
}
