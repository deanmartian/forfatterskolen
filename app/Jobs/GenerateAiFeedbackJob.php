<?php

namespace App\Jobs;

use App\AssignmentSubmission;
use App\Services\AiFeedbackService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAiFeedbackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300];

    protected $submissionId;

    public function __construct(int $submissionId)
    {
        $this->submissionId = $submissionId;
    }

    public function handle(AiFeedbackService $service): void
    {
        $submission = AssignmentSubmission::with('assignment.lesson')->find($this->submissionId);

        if (!$submission || $submission->status !== 'pending') {
            return;
        }

        $lesson = $submission->assignment->lesson;
        $lessonContent = $lesson->content ?? '';
        $questionText = $submission->assignment->question_text;
        $answerText = $submission->answer_text;

        $feedback = $service->generateFeedback($lessonContent, $questionText, $answerText);

        if ($feedback) {
            $submission->update([
                'ai_feedback' => $feedback,
                'status' => 'ai_generated',
            ]);

            Log::info('AI feedback generated', ['submission_id' => $this->submissionId]);
        } else {
            Log::warning('AI feedback generation failed', ['submission_id' => $this->submissionId]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateAiFeedbackJob failed', [
            'submission_id' => $this->submissionId,
            'error' => $exception->getMessage(),
        ]);
    }
}
