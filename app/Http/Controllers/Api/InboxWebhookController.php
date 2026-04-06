<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessInboxWebhookJob;
use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InboxWebhookController extends Controller
{
    /**
     * Motta innkommende e-post via webhook (Resend / generisk videresending).
     */
    public function handle(Request $request)
    {
        Log::info('Inbox webhook mottatt', [
            'ip' => $request->ip(),
            'content_type' => $request->header('Content-Type'),
        ]);

        try {
            $data = $this->extractEmailData($request);

            if (empty($data['from_email'])) {
                Log::warning('Inbox webhook: mangler avsender-epost');
                return response()->json(['status' => 'error', 'message' => 'Mangler avsender-epost'], 422);
            }

            // Finn eller opprett samtale
            $conversation = InboxConversation::findOrCreateFromEmail([
                'from_email' => $data['from_email'],
                'from_name' => $data['from_name'],
                'subject' => $data['subject'],
                'to_email' => $data['to_email'],
                'source' => 'webhook',
            ]);

            // Koble til bruker hvis mulig
            $user = User::where('email', $data['from_email'])->first();
            if ($user && !$conversation->user_id) {
                $conversation->update(['user_id' => $user->id]);
            }

            // Opprett innkommende melding
            $message = InboxMessage::create([
                'conversation_id' => $conversation->id,
                'direction' => 'inbound',
                'from_email' => $data['from_email'],
                'from_name' => $data['from_name'],
                'body' => $data['body_html'],
                'body_plain' => $data['body_plain'],
                'subject' => $data['subject'],
                'is_draft' => false,
                'is_ai_draft' => false,
                'metadata' => [
                    'webhook_source' => $data['source_format'],
                    'to_email' => $data['to_email'],
                    'received_at' => now()->toISOString(),
                ],
            ]);

            Log::info('Inbox webhook: melding opprettet', [
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
                'from' => $data['from_email'],
            ]);

            // Generer AI-utkast i bakgrunnen
            ProcessInboxWebhookJob::dispatch($conversation->id, $message->id);

            return response()->json([
                'status' => 'ok',
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Inbox webhook feilet', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'Intern feil'], 500);
        }
    }

    /**
     * Tolk e-postdata fra ulike webhook-formater.
     */
    private function extractEmailData(Request $request): array
    {
        $payload = $request->all();

        // Resend inbound webhook format
        if (isset($payload['from']) && is_string($payload['from'])) {
            return [
                'from_email' => $this->extractEmail($payload['from']),
                'from_name' => $this->extractName($payload['from']),
                'subject' => $payload['subject'] ?? '(Uten emne)',
                'body_html' => $payload['html'] ?? $payload['body'] ?? '',
                'body_plain' => $payload['text'] ?? strip_tags($payload['html'] ?? ''),
                'to_email' => is_string($payload['to'] ?? null)
                    ? $this->extractEmail($payload['to'])
                    : ($payload['to'][0] ?? 'post@forfatterskolen.no'),
                'source_format' => 'resend',
            ];
        }

        // Generisk format med separate felter
        return [
            'from_email' => $payload['from_email'] ?? $payload['sender'] ?? $this->extractEmail($payload['from'] ?? ''),
            'from_name' => $payload['from_name'] ?? $payload['sender_name'] ?? $this->extractName($payload['from'] ?? ''),
            'subject' => $payload['subject'] ?? '(Uten emne)',
            'body_html' => $payload['html'] ?? $payload['body_html'] ?? $payload['body'] ?? '',
            'body_plain' => $payload['text'] ?? $payload['body_plain'] ?? strip_tags($payload['body'] ?? ''),
            'to_email' => $payload['to_email'] ?? $payload['recipient'] ?? $payload['to'] ?? 'post@forfatterskolen.no',
            'source_format' => 'generic',
        ];
    }

    /**
     * Hent e-postadresse fra "Navn <epost@example.com>" format.
     */
    private function extractEmail(string $from): string
    {
        if (preg_match('/<([^>]+)>/', $from, $matches)) {
            return $matches[1];
        }
        return trim($from);
    }

    /**
     * Hent navn fra "Navn <epost@example.com>" format.
     */
    private function extractName(string $from): ?string
    {
        if (preg_match('/^(.+?)\s*</', $from, $matches)) {
            return trim($matches[1], ' "\'');
        }
        return null;
    }
}
