<?php

namespace App\Services\Helpwise;

use App\User;
use App\HelpwiseConversation;
use App\HelpwiseMessage;
use App\Models\HelpwiseReplyExample;
use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpwiseReplyAiService
{
    /**
     * Generate an AI draft reply for a customer message.
     * Returns the draft text in Norwegian, never auto-sends.
     */
    public function generateDraftReply(HelpwiseConversation $conversation, ?HelpwiseMessage $latestMessage = null): ?string
    {
        $studentContext = $this->getStudentContext($conversation);
        $messageHistory = $this->getMessageHistory($conversation);
        $customerMessage = $latestMessage?->body_plain ?? $latestMessage?->body ?? '';

        $prompt = $this->buildPrompt($conversation, $customerMessage, $studentContext, $messageHistory);

        try {
            $reply = $this->callAi($prompt);

            if ($reply) {
                Log::info('Helpwise AI: draft reply generated', [
                    'conversation_id' => $conversation->id,
                    'helpwise_id' => $conversation->helpwise_id,
                    'length' => strlen($reply),
                ]);
            }

            return $reply;
        } catch (\Exception $e) {
            Log::error('Helpwise AI: draft generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Build the AI prompt with full context about the student and conversation.
     */
    private function buildPrompt(
        HelpwiseConversation $conversation,
        string $customerMessage,
        array $studentContext,
        string $messageHistory
    ): string {
        $inbox = $conversation->inbox ?? 'Ukjent';
        $customerName = $conversation->customer_name ?? 'kunde';

        $studentInfo = '';
        if (!empty($studentContext)) {
            $studentInfo = "\n\nELEVINFORMASJON FRA DATABASEN:\n";
            foreach ($studentContext as $key => $value) {
                if ($value) $studentInfo .= "- {$key}: {$value}\n";
            }
        }

        $historySection = '';
        if ($messageHistory) {
            $historySection = "\n\nTIDLIGERE MELDINGER I SAMTALEN:\n{$messageHistory}";
        }

        $examplesSection = $this->getReplyExamples();

        return <<<PROMPT
Du er en vennlig og profesjonell kundebehandler for Forfatterskolen, Norges ledende nettbaserte skriveskole.

Du skriver ALLTID på norsk (bokmål).
Du er hjelpsom, varm og profesjonell.
Du skal ALDRI finne på informasjon - bruk kun det du vet fra konteksten.
Hvis du er usikker, si at du skal sjekke og komme tilbake.
Svar kort og presist. Ikke skriv romaner.

OM FORFATTERSKOLEN:
- Forfatterskolen tilbyr nettbaserte skrivekurs (årskurs, halvårskurs, sjangerkurs, etc.)
- Elever leverer innleveringer/manuskripter som redaktører gir tilbakemelding på
- Forfatterskolen tilbyr også manustjenester (redaktør, språkvask, korrektur) for selvpublisister
- Kurs har faste innleveringsfrister, men utsettelse kan gis ved behov
- Webinarer er en del av kursopplevelsen
- Eier/daglig leder er Sven Inge Henningsen
- Kontakt: post@forfatterskolen.no / support@forfatterskolen.no
{$examplesSection}

INBOX: {$inbox}
KUNDENS NAVN: {$customerName}
KUNDENS E-POST: {$conversation->customer_email}
{$studentInfo}
{$historySection}

KUNDENS SISTE MELDING:
{$customerMessage}

Skriv et passende svarkutkast. Husk:
- Start med å hilse på kunden ved fornavn hvis mulig
- Svar direkte på spørsmålet
- Vær hjelpsom, varm og positiv - men ikke overdrevent
- Hold deg kort og direkte, lik eksemplene ovenfor
- Bruk gjerne smilefjes som :-) eller :) der det passer naturlig
- Avslutt med "Vennlig hilsen, Forfatterskolen" eller "Mvh, Forfatterskolen"
- IKKE skriv "Hei [Navn]" hvis du ikke vet navnet
- Matcher stilen og tonen fra eksemplene
PROMPT;
    }

    /**
     * Get student context from the database if the conversation is linked to a user.
     */
    private function getStudentContext(HelpwiseConversation $conversation): array
    {
        if (!$conversation->user_id) {
            // Try to find by email
            $user = $conversation->customer_email
                ? User::where('email', $conversation->customer_email)->first()
                : null;

            if ($user) {
                $conversation->update(['user_id' => $user->id]);
            } else {
                return [];
            }
        } else {
            $user = User::find($conversation->user_id);
        }

        if (!$user) return [];

        $context = [
            'Navn' => $user->first_name . ' ' . $user->last_name,
            'E-post' => $user->email,
            'Rolle' => $this->getRoleName($user->role),
        ];

        // Check active courses
        $courses = $user->coursesTaken()
            ->where('is_active', 1)
            ->with('package')
            ->get();

        if ($courses->isNotEmpty()) {
            $courseNames = $courses->map(fn($ct) => $ct->package?->name ?? 'Ukjent kurs')->implode(', ');
            $context['Aktive kurs'] = $courseNames;
        }

        // Check if they have pending assignments
        $pendingAssignments = \App\AssignmentLearner::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        if ($pendingAssignments > 0) {
            $context['Ventende oppgaver'] = $pendingAssignments;
        }

        // Check shop manuscripts
        $manuscripts = \App\ShopManuscriptsTaken::where('user_id', $user->id)
            ->where('is_active', 1)
            ->count();

        if ($manuscripts > 0) {
            $context['Aktive manustjenester'] = $manuscripts;
        }

        return $context;
    }

    /**
     * Get recent message history for the conversation.
     * Checks both HelpwiseMessage and InboxMessage tables.
     */
    private function getMessageHistory(HelpwiseConversation $conversation): string
    {
        // Try HelpwiseMessage first
        $messages = $conversation->messages()
            ->orderBy('message_at')
            ->limit(10)
            ->get();

        // If no HelpwiseMessages, try InboxMessage (used for imported conversations)
        if ($messages->isEmpty()) {
            $inboxMessages = InboxMessage::where('conversation_id', $conversation->id)
                ->where('is_draft', false)
                ->orderBy('created_at')
                ->limit(10)
                ->get();

            if ($inboxMessages->isEmpty()) return '';

            $history = '';
            foreach ($inboxMessages as $msg) {
                $direction = $msg->direction === 'outbound' ? 'AGENT' : 'KUNDE';
                $time = $msg->created_at?->format('d.m H:i') ?? '';
                $body = \Illuminate\Support\Str::limit(strip_tags($msg->body_plain ?? $msg->body ?? ''), 300);
                $history .= "[{$time}] {$direction}: {$body}\n\n";
            }
            return $history;
        }

        $history = '';
        foreach ($messages as $msg) {
            $direction = $msg->direction === 'outbound' ? 'AGENT' : 'KUNDE';
            $time = $msg->message_at?->format('d.m H:i') ?? '';
            $body = \Illuminate\Support\Str::limit(strip_tags($msg->body_plain ?? $msg->body ?? ''), 300);
            $history .= "[{$time}] {$direction}: {$body}\n\n";
        }

        return $history;
    }

    /**
     * Get real reply examples from the database to teach the AI our writing style.
     */
    private function getReplyExamples(): string
    {
        $examples = HelpwiseReplyExample::orderBy('sent_at', 'desc')
            ->limit(6)
            ->get();

        if ($examples->isEmpty()) {
            return '';
        }

        $section = "\n\nEKSEMPLER PÅ HVORDAN VI SVARER (kopier stilen, IKKE innholdet):\n";

        foreach ($examples as $i => $ex) {
            // Try to find the customer message that was replied to
            $customerMsg = '';
            $inbound = InboxMessage::where('conversation_id', $ex->conversation_id)
                ->where('direction', 'inbound')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($inbound) {
                $customerMsg = \Illuminate\Support\Str::limit(strip_tags($inbound->body ?? ''), 150);
            }

            $replyBody = \Illuminate\Support\Str::limit(strip_tags($ex->reply_body), 250);
            $num = $i + 1;

            $section .= "\nEksempel {$num}:";
            if ($customerMsg) {
                $section .= "\n  Kunde: {$customerMsg}";
            }
            $section .= "\n  Vårt svar: {$replyBody}\n";
        }

        return $section;
    }

    private function callAi(string $prompt): ?string
    {
        $provider = config('ad_os.ai_provider', 'openai');

        if ($provider === 'anthropic') {
            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return $response->json('content.0.text');
        }

        // Default: OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'Du er en kundebehandler for Forfatterskolen. Skriv alltid på norsk bokmål.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ]);

        return $response->json('choices.0.message.content');
    }

    private function getRoleName(int $role): string
    {
        return match ($role) {
            1 => 'Admin',
            2 => 'Elev',
            3 => 'Redaktør',
            4 => 'Giutbok-admin',
            default => 'Ukjent',
        };
    }
}
