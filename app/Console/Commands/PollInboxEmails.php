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
        $accounts = config('inbox.accounts', []);

        if (empty($accounts)) {
            $this->error('Ingen IMAP-kontoer konfigurert i config/inbox.php');
            return self::FAILURE;
        }

        $totalCount = 0;
        $accountsPolled = 0;

        foreach ($accounts as $account) {
            // Skip kontoer som ikke har username/password — trygt fallback
            if (empty($account['username']) || empty($account['password'])) {
                $this->line("Hopper over '{$account['inbox_email']}' (mangler credentials i .env)");
                continue;
            }

            $accountsPolled++;
            $this->line("Poller {$account['username']} → {$account['inbox_email']}...");

            try {
                $count = $this->pollAccount($account);
                $totalCount += $count;
            } catch (\Throwable $e) {
                $this->warn("Feil ved polling av {$account['inbox_email']}: " . $e->getMessage());
                Log::error('IMAP poll account error', [
                    'inbox' => $account['inbox_email'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($accountsPolled === 0) {
            $this->error('Ingen IMAP-kontoer hadde gyldig credentials.');
            return self::FAILURE;
        }

        $this->info("Ferdig! {$totalCount} nye e-poster importert fra {$accountsPolled} kontoer.");
        Log::info("IMAP poll: {$totalCount} emails imported from {$accountsPolled} accounts");

        return self::SUCCESS;
    }

    /**
     * Poll én enkelt IMAP-konto. Returnerer antall importerte e-poster.
     */
    private function pollAccount(array $account): int
    {
        $host = $account['host'];
        $username = $account['username'];
        $password = $account['password'];
        $accountInboxEmail = $account['inbox_email'];

        $inbox = @imap_open($host, $username, $password);

        if (!$inbox) {
            $this->warn("Kunne ikke koble til IMAP for {$accountInboxEmail}: " . imap_last_error());
            return 0;
        }

        // Resolv private_to_user_id automatisk basert på inbox_email —
        // hvis adressen matcher en aktiv admin-bruker, blir hele kontoen
        // privat for den brukeren.
        $privateToUserId = $this->resolveAdminUserIdForEmail($accountInboxEmail);

        // Get emails — filter by date if specified
        $since = $this->option('since') ?: date('Y-m-d');
        $sinceFormatted = date('d-M-Y', strtotime($since));
        $emails = imap_search($inbox, 'SINCE "' . $sinceFormatted . '" UNSEEN');

        if (!$emails) {
            imap_close($inbox);
            return 0;
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

                // For hovedkontoen (post@) bruker vi To/Cc-routing slik at
                // e-post sendt til andre admin-adresser fra felles-mailboxen
                // også havner i riktig privat inbox. For dedikerte private
                // mailbokser er routingen allerede gitt av selve mailboxen.
                if ($privateToUserId) {
                    $targetInbox = $accountInboxEmail;
                } else {
                    $routing = $this->determineInboxRouting($header);
                    $targetInbox = $routing['inbox'];
                    $privateToUserIdForMessage = $routing['private_to_user_id'];
                }
                if (!isset($privateToUserIdForMessage)) {
                    $privateToUserIdForMessage = $privateToUserId;
                }

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
                        'inbox' => $targetInbox,
                        'private_to_user_id' => $privateToUserIdForMessage,
                        // Auto-tildel privat-inbox-eieren slik at samtalene
                        // dukker opp i deres "Mine"-filter automatisk.
                        'assigned_to' => $privateToUserIdForMessage,
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
                    'to_email' => $targetInbox,
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

            // Reset per-iteration variabel slik at neste e-post starter friskt
            unset($privateToUserIdForMessage);
        }

        imap_close($inbox);

        return $count;
    }

    /**
     * Finn ut om en e-postadresse tilhører en aktiv admin-bruker.
     * Returnerer user_id hvis det matcher, ellers null.
     *
     * Brukes for å automatisk koble en dedikert IMAP-mailbox til den
     * admin-brukeren som "eier" e-postadressen — slik at samtaler i den
     * mailboxen automatisk blir private for den ene admin-brukeren.
     */
    private function resolveAdminUserIdForEmail(string $email): ?int
    {
        static $cache = null;
        if ($cache === null) {
            $cache = User::where('role', 1)
                ->where('is_active', 1)
                ->whereNotNull('email')
                ->pluck('id', 'email')
                ->mapWithKeys(function ($id, $email) {
                    return [strtolower(trim($email)) => $id];
                })
                ->toArray();
        }

        return $cache[strtolower(trim($email))] ?? null;
    }

    /**
     * Finn ut hvilken inbox en innkommende e-post tilhører basert på
     * hvem den er adressert til. Hvis en av mottakerne (To/Cc) matcher
     * e-posten til en aktiv admin-bruker, legges samtalen i den private
     * inboksen deres. Ellers havner den i den offentlige post@-inboksen.
     *
     * Cacher admin-mapping per poll for ytelse.
     *
     * @return array{inbox: string, private_to_user_id: ?int}
     */
    private function determineInboxRouting(object $header): array
    {
        static $adminEmailMap = null;
        if ($adminEmailMap === null) {
            $adminEmailMap = User::where('role', 1)
                ->where('is_active', 1)
                ->whereNotNull('email')
                ->pluck('id', 'email')
                ->mapWithKeys(function ($id, $email) {
                    return [strtolower(trim($email)) => $id];
                })
                ->toArray();
        }

        // Samle alle mottakere fra To og Cc
        $recipients = [];
        foreach (['to', 'cc'] as $field) {
            if (!empty($header->{$field}) && is_array($header->{$field})) {
                foreach ($header->{$field} as $addr) {
                    if (!empty($addr->mailbox) && !empty($addr->host)) {
                        $recipients[] = strtolower($addr->mailbox . '@' . $addr->host);
                    }
                }
            }
        }

        // Sjekk om noen mottaker matcher en admin-brukers e-post
        foreach ($recipients as $recipient) {
            if (isset($adminEmailMap[$recipient])) {
                return [
                    'inbox' => $recipient,
                    'private_to_user_id' => $adminEmailMap[$recipient],
                ];
            }
        }

        // Default: offentlig inbox, ingen privat eier
        return [
            'inbox' => 'post@forfatterskolen.no',
            'private_to_user_id' => null,
        ];
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
