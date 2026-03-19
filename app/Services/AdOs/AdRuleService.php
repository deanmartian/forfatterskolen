<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdRule;
use App\Models\AdOs\AdRuleRun;
use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdMetricSnapshot;
use App\Models\AdOs\AdActionLog;
use Illuminate\Support\Facades\Log;

class AdRuleService
{
    public function getAllRules()
    {
        return AdRule::orderBy('priority')->orderByDesc('is_active')->get();
    }

    public function createRule(array $data): AdRule
    {
        $rule = AdRule::create($data);

        AdActionLog::log('rule_created', [
            'target_type' => 'rule',
            'target_id' => $rule->id,
            'triggered_by' => 'human',
            'user_id' => auth()->id(),
            'payload' => ['name' => $rule->name, 'metric' => $rule->metric],
        ]);

        return $rule;
    }

    public function evaluateRules(): array
    {
        $rules = AdRule::where('is_active', true)->orderBy('priority')->get();
        $results = [];

        foreach ($rules as $rule) {
            $targets = $this->getTargetsForRule($rule);

            foreach ($targets as $target) {
                $actualValue = $this->getMetricValue($rule->metric, $target, $rule->evaluation_window_days);

                if ($actualValue === null) continue;

                // Check minimum data thresholds
                if (!$this->meetsMinimumData($rule, $target)) continue;

                $triggered = $rule->evaluate($actualValue);

                $run = AdRuleRun::create([
                    'rule_id' => $rule->id,
                    'triggered' => $triggered,
                    'target_type' => $rule->scope,
                    'target_id' => $target->id,
                    'actual_value' => $actualValue,
                    'result' => $triggered ? $rule->action : 'no_action',
                    'details' => [
                        'metric' => $rule->metric,
                        'threshold' => (float) $rule->threshold,
                        'actual' => $actualValue,
                        'operator' => $rule->operator,
                    ],
                ]);

                if ($triggered) {
                    $results[] = [
                        'rule' => $rule,
                        'run' => $run,
                        'target' => $target,
                        'action' => $rule->action,
                        'auto_apply' => $rule->auto_apply,
                    ];
                }
            }
        }

        return $results;
    }

    private function getTargetsForRule(AdRule $rule)
    {
        return match ($rule->scope) {
            'campaign' => $rule->campaign_id
                ? AdCampaign::where('id', $rule->campaign_id)->where('status', 'active')->get()
                : AdCampaign::where('status', 'active')->get(),
            default => collect(),
        };
    }

    private function getMetricValue(string $metric, $target, int $windowDays): ?float
    {
        $snapshots = AdMetricSnapshot::where('campaign_id', $target->id)
            ->where('level', 'campaign')
            ->where('date', '>=', now()->subDays($windowDays))
            ->get();

        if ($snapshots->isEmpty()) return null;

        return match ($metric) {
            'cpa' => (float) $snapshots->avg('cpa'),
            'ctr' => (float) $snapshots->avg('ctr'),
            'cpc' => (float) $snapshots->avg('cpc'),
            'roas' => (float) $snapshots->avg('roas'),
            'spend' => (float) $snapshots->sum('spend'),
            'conversions' => (float) $snapshots->sum('conversions'),
            'impressions' => (float) $snapshots->sum('impressions'),
            'clicks' => (float) $snapshots->sum('clicks'),
            default => null,
        };
    }

    private function meetsMinimumData(AdRule $rule, $target): bool
    {
        if ($rule->min_spend_threshold) {
            $totalSpend = AdMetricSnapshot::where('campaign_id', $target->id)
                ->where('level', 'campaign')
                ->where('date', '>=', now()->subDays($rule->evaluation_window_days))
                ->sum('spend');

            if ($totalSpend < (float) $rule->min_spend_threshold) return false;
        }

        if ($rule->min_data_points > 0) {
            $count = AdMetricSnapshot::where('campaign_id', $target->id)
                ->where('level', 'campaign')
                ->where('date', '>=', now()->subDays($rule->evaluation_window_days))
                ->count();

            if ($count < $rule->min_data_points) return false;
        }

        return true;
    }
}
