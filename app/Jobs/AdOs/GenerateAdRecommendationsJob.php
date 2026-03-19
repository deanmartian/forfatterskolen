<?php

namespace App\Jobs\AdOs;

use App\Services\AdOs\AdDecisionService;
use App\Services\AdOs\AdApprovalService;
use App\Models\AdOs\AdActionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAdRecommendationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [120];

    public function handle(AdDecisionService $decisionService, AdApprovalService $approvalService): void
    {
        if (!config('ad_os.enabled')) return;

        try {
            $decisions = $decisionService->generateDecisions();

            foreach ($decisions as $decision) {
                if ($decision->requires_approval) {
                    $approvalService->createApprovalRequest($decision->id);
                }
            }

            AdActionLog::log('recommendations_generated', [
                'triggered_by' => 'ai',
                'payload' => ['count' => count($decisions)],
            ]);
        } catch (\Exception $e) {
            Log::error('AdOS GenerateAdRecommendationsJob failed', ['error' => $e->getMessage()]);
        }
    }
}
