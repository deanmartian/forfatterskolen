<?php

namespace App\Services\Helpwise;

use App\User;
use App\HelpwiseConversation;
use App\HelpwiseMessage;
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

        return <<<PROMPT
Du er en vennlig og profesjonell kundebehandler for Forfatterskolen, Norges ledende nettbaserte skriveskole.

Du skriver ALLTID på norsk (bokmål).
Du er hjelpsom, varm og profesjonell.
Du skal ALDRI finne på informasjon - bruk kun det du vet fra konteksten.
Hvis du er usikker, si at du skal sjekke og komme tilbake.
Svar kort og presist. Ikke skriv romaner.

INBOX: {$inbox}
KUNDENS NAVN: {$customerName}
KUNDENS E-POST: {$conversation->customer_email}
{$studentInfo}
{$historySection}

KUNDENS SISTE MELDING:
{$customerMessage}

Skriv et passende svarkutkast. Husk:
- Start med å hilse på kunden ved navn hvis mulig
- Svar direkte på spørsmålet
- Vær hjelpsom og positiv
- Avslutt med en hyggelig avslutning
- Signer med "Vennlig hilsen, Forfatterskolen"
- IKKE skriv "Hei [Navn]" hvis du ikke vet navnet
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

        try {
            $courses = $user->coursesTaken()->where('is_active', 1)->with('package')->get();
            if ($courses->isNotEmpty()) {
                $context['Aktive kurs'] = $courses->map(fn($ct) => $ct->package?->name ?? 'Ukjent kurs')->implode(', ');
            }
        } catch (\Exception $e) {}

        try {
            $pendingAssignments = \App\AssignmentSubmission::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])->count();
            if ($pendingAssignments > 0) $context['Ventende oppgaver'] = $pendingAssignments;
        } catch (\Exception $e) {}

        try {
            $manuscripts = \App\ShopManuscriptsTaken::where('user_id', $user->id)
                ->where('is_active', 1)->count();
            if ($manuscripts > 0) $context['Aktive manustjenester'] = $manuscripts;
        } catch (\Exception $e) {}

        return $context;
    }

    /**
     * Get recent message history for the conversation.
     */
    private function getMessageHistory(HelpwiseConversation $conversation): string
    {
        $messages = $conversation->messages()
            ->orderBy('message_at')
            ->limit(10)
            ->get();

        if ($messages->isEmpty()) return '';

        $history = '';
        foreach ($messages as $msg) {
            $direction = $msg->direction === 'outbound' ? 'AGENT' : 'KUNDE';
            $time = $msg->message_at?->format('d.m H:i') ?? '';
            $body = \Illuminate\Support\Str::limit(strip_tags($msg->body_plain ?? $msg->body ?? ''), 300);
            $history .= "[{$time}] {$direction}: {$body}\n\n";
        }

        return $history;
    }

    private function callAi(string $prompt): ?string
    {
        // Try Anthropic first, then GPT/OpenAI
        $anthropicKey = config('services.anthropic.key') ?? config('services.anthropic.api_key');
        $gptKey = config('services.gpt.api_key') ?? config('services.openai.key') ?? config('services.openai.api_key');

        if ($anthropicKey) {
            $response = Http::withHeaders([
                'x-api-key' => $anthropicKey,
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

        if (!$gptKey) {
            Log::error('Helpwise AI: No AI API key configured');
            return null;
        }

        // Fallback: OpenAI/GPT
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $gptKey,
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
