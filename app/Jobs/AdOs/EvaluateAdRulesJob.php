<?php

namespace App\Jobs\AdOs;

use App\Services\AdOs\AdRuleService;
use App\Services\AdOs\AdDecisionService;
use App\Services\AdOs\AdApprovalService;
use App\Models\AdOs\AdAiDecision;
use App\Models\AdOs\AdActionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateAdRulesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [120];

    public function handle(
        AdRuleService $ruleService,
        AdDecisionService $decisionService,
        AdApprovalService $approvalService,
    ): void {
        if (!config('ad_os.enabled')) return;

        try {
            $triggeredRules = $ruleService->evaluateRules();

            foreach ($triggeredRules as $result) {
                $rule = $result['rule'];
                $target = $result['target'];

                $decision = AdAiDecision::create([
                    'decision_type' => $rule->action,
                    'confidence' => 0.85,
                    'reasoning_summary' => "Regel '{$rule->name}' utløst: {$rule->metric} {$rule->operator} {$rule->threshold}",
                    'risk_level' => $rule->risk_level,
                    'requires_approval' => !$rule->auto_apply,
                    'proposed_action' => [
                        'campaign_id' => $target->id,
                        'change' => $rule->action,
                        'triggered_by_rule' => $rule->id,
                    ],
                    'campaign_id' => $target->id,
                    'rule_id' => $rule->id,
                    'context_data' => $result['run']->details,
                ]);

                if ($rule->auto_apply && config('ad_os.auto_apply_enabled')) {
                    $decision->update(['status' => 'approved']);
                    $decisionService->executeDecision($decision->id);
                } elseif ($decision->requires_approval) {
                    $approvalService->createApprovalRequest($decision->id);
                }
            }

            AdActionLog::log('rules_evaluated', [
                'triggered_by' => 'system',
                'payload' => ['rules_triggered' => count($triggeredRules)],
            ]);
        } catch (\Exception $e) {
            Log::error('AdOS EvaluateAdRulesJob failed', ['error' => $e->getMessage()]);
        }
    }
}
