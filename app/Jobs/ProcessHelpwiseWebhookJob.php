<?php

namespace App\Jobs;

use App\Models\HelpwiseWebhookLog;
use App\Services\Helpwise\HelpwiseApiService;
use App\Services\Helpwise\HelpwiseReplyAiService;
use App\Services\Helpwise\HelpwiseSimilarityService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessHelpwiseWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 120];

    protected array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle(HelpwiseApiService $helpwiseApi, HelpwiseReplyAiService $aiService, HelpwiseSimilarityService $similarityService): void
    {
        $eventId = $this->payload['event_id'] ?? $this->payload['id'] ?? null;
        $eventType = $this->payload['event_type'] ?? 'unknown';
        $conversationId = $this->payload['conversation_id']
            ?? $this->payload['data']['conversation_id']
            ?? $this->payload['data']['id']
            ?? null;
        $senderEmail = $this->payload['data']['sender']['email']
            ?? $this->payload['data']['from_email']
            ?? $this->payload['data']['customer']['email']
            ?? null;
        $senderName = $this->payload['data']['sender']['name']
            ?? $this->payload['data']['from_name']
            ?? $this->payload['data']['customer']['name']
            ?? null;

        // Duplicate protection
        if ($eventId && HelpwiseWebhookLog::where('event_id', $eventId)->exists()) {
            Log::info('Helpwise webhook: duplicate event_id, skipping', ['event_id' => $eventId]);
            return;
        }

        // Create log entry
        $log = HelpwiseWebhookLog::create([
            'event_id' => $eventId,
            'conversation_id' => $conversationId,
            'sender_email' => $senderEmail,
            'sender_name' => $senderName,
            'event_type' => $eventType,
            'draft_status' => 'processing',
            'payload' => $this->payload,
        ]);

        try {
            // Fetch conversation thread if we have a conversation ID
            $threadMessages = [];
            if ($conversationId) {
                $messagesData = $helpwiseApi->getMessages($conversationId);
                if ($messagesData && isset($messagesData['data'])) {
                    $threadMessages = $messagesData['data'];
                } elseif ($messagesData && is_array($messagesData)) {
                    $threadMessages = $messagesData;
                }
            }

            // If no messages from API, build from webhook payload
            if (empty($threadMessages)) {
                $threadMessages = [[
                    'from' => $senderEmail ?? $senderName ?? 'Unknown',
                    'body' => $this->payload['data']['body']
                        ?? $this->payload['data']['message']
                        ?? $this->payload['data']['text']
                        ?? '',
                    'created_at' => $this->payload['data']['created_at'] ?? now()->toISOString(),
                ]];
            }

            // Student enrichment
            $studentData = null;
            if ($senderEmail) {
                $studentData = $this->enrichStudent($senderEmail);
            }

            // V2: Find similar historical replies for style matching
            $historicalExamples = [];
            $matchedExamplesCount = 0;
            try {
                $incomingSubject = $this->payload['data']['subject'] ?? '';
                $incomingBody = $this->payload['data']['body']
                    ?? $this->payload['data']['message']
                    ?? $this->payload['data']['text']
                    ?? '';
                $similarReplies = $similarityService->findSimilar($incomingSubject, $incomingBody, 5);
                $matchedExamplesCount = $similarReplies->count();
                $historicalExamples = $similarReplies->toArray();

                Log::info('Helpwise V2: found historical examples', [
                    'event_id' => $eventId,
                    'matched_count' => $matchedExamplesCount,
                ]);
            } catch (\Exception $e) {
                Log::warning('Helpwise V2: similarity search failed, proceeding without examples', [
                    'error' => $e->getMessage(),
                ]);
            }

            $log->update(['matched_examples_count' => $matchedExamplesCount]);

            // Generate AI reply
            $aiResult = $aiService->generateReply($threadMessages, $studentData, $historicalExamples);

            if (!$aiResult) {
                $log->update([
                    'draft_status' => 'failed',
                    'error_message' => 'AI returned invalid or empty response',
                ]);
                return;
            }

            $log->update([
                'should_reply' => $aiResult['should_reply'] ?? false,
                'confidence' => $aiResult['confidence'] ?? 0,
                'ai_response' => $aiResult,
            ]);

            // If AI says don't reply, mark as skipped
            if (empty($aiResult['should_reply'])) {
                $log->update(['draft_status' => 'skipped']);
                Log::info('Helpwise: AI says no reply needed', [
                    'event_id' => $eventId,
                    'reasoning' => $aiResult['reasoning_summary'] ?? '',
                ]);
                return;
            }

            $draftBody = $aiResult['body'] ?? '';
            if (empty($draftBody)) {
                $log->update([
                    'draft_status' => 'failed',
                    'error_message' => 'AI response had empty body',
                ]);
                return;
            }

            // Try to create draft via Helpwise API
            if ($conversationId) {
                $draftResult = $helpwiseApi->createDraftReply($conversationId, $draftBody);

                if ($draftResult) {
                    $log->update(['draft_status' => 'created']);
                    Log::info('Helpwise: draft created successfully', [
                        'event_id' => $eventId,
                        'conversation_id' => $conversationId,
                    ]);
                    return;
                }
            }

            // Fallback: save draft locally if Helpwise API fails
            $log->update([
                'draft_status' => 'saved_locally',
                'error_message' => 'Could not create draft via Helpwise API, saved locally in ai_response',
            ]);

            Log::warning('Helpwise: draft saved locally (API failed)', [
                'event_id' => $eventId,
                'conversation_id' => $conversationId,
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessHelpwiseWebhookJob failed', [
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);

            $log->update([
                'draft_status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    protected function enrichStudent(string $email): ?array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return null;
        }

        $data = [
            'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            'email' => $user->email,
            'status' => $user->role == User::LearnerRole ? 'student' : 'other',
            'created_at' => $user->created_at?->format('Y-m-d'),
        ];

        // Try to get enrolled courses
        if (method_exists($user, 'courses') || method_exists($user, 'enrollments')) {
            try {
                $courses = method_exists($user, 'courses')
                    ? $user->courses()->pluck('name')->toArray()
                    : [];
                if (!empty($courses)) {
                    $data['courses'] = $courses;
                }
            } catch (\Exception $e) {
                // Silently skip if relationship doesn't work
            }
        }

        return $data;
    }
}
