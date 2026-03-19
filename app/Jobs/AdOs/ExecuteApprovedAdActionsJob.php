<?php

namespace App\Jobs\AdOs;

use App\Models\AdOs\AdAiDecision;
use App\Services\AdOs\AdDecisionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteApprovedAdActionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300];

    public function handle(AdDecisionService $decisionService): void
    {
        if (!config('ad_os.enabled')) return;

        $approvedDecisions = AdAiDecision::where('status', 'approved')
            ->whereNull('executed_at')
            ->orderBy('created_at')
            ->limit(10)
            ->get();

        foreach ($approvedDecisions as $decision) {
            try {
                $decisionService->executeDecision($decision->id);
            } catch (\Exception $e) {
                Log::error('AdOS ExecuteApprovedAction failed', [
                    'decision_id' => $decision->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
