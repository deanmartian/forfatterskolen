<?php

namespace App\Services\AdOs;

use App\Models\AdOs\AdCampaign;
use App\Models\AdOs\AdMetricSnapshot;
use App\Models\AdOs\AdActionLog;
use App\Models\AdOs\AdAiDecision;

class AdReportService
{
    public function getDailySummary(?string $date = null): array
    {
        $date = $date ? \Carbon\Carbon::parse($date) : today();

        $metrics = AdMetricSnapshot::where('date', $date)
            ->where('level', 'campaign')
            ->get();

        $actions = AdActionLog::whereDate('created_at', $date)->get();
        $decisions = AdAiDecision::whereDate('created_at', $date)->get();

        return [
            'date' => $date->toDateString(),
            'metrics' => [
                'total_spend' => $metrics->sum('spend'),
                'total_impressions' => $metrics->sum('impressions'),
                'total_clicks' => $metrics->sum('clicks'),
                'total_conversions' => $metrics->sum('conversions'),
                'avg_cpa' => $metrics->avg('cpa'),
                'avg_ctr' => $metrics->avg('ctr'),
                'avg_roas' => $metrics->avg('roas'),
            ],
            'actions' => [
                'total' => $actions->count(),
                'by_ai' => $actions->where('triggered_by', 'ai')->count(),
                'by_human' => $actions->where('triggered_by', 'human')->count(),
                'by_rule' => $actions->where('triggered_by', 'rule')->count(),
            ],
            'decisions' => [
                'total' => $decisions->count(),
                'pending' => $decisions->where('status', 'pending')->count(),
                'executed' => $decisions->where('status', 'executed')->count(),
                'rejected' => $decisions->where('status', 'rejected')->count(),
            ],
            'campaigns_active' => AdCampaign::where('status', 'active')->count(),
        ];
    }

    public function getWeeklySummary(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $metrics = AdMetricSnapshot::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('level', 'campaign')
            ->get();

        $dailyBreakdown = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dayMetrics = $metrics->where('date', $day->toDateString());
            $dailyBreakdown[] = [
                'date' => $day->toDateString(),
                'spend' => $dayMetrics->sum('spend'),
                'conversions' => $dayMetrics->sum('conversions'),
                'clicks' => $dayMetrics->sum('clicks'),
            ];
        }

        return [
            'period' => $startOfWeek->toDateString() . ' - ' . $endOfWeek->toDateString(),
            'totals' => [
                'spend' => $metrics->sum('spend'),
                'impressions' => $metrics->sum('impressions'),
                'clicks' => $metrics->sum('clicks'),
                'conversions' => $metrics->sum('conversions'),
            ],
            'daily_breakdown' => $dailyBreakdown,
        ];
    }
}
