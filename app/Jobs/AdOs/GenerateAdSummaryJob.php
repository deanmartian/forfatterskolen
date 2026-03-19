<?php

namespace App\Jobs\AdOs;

use App\Services\AdOs\AdReportService;
use App\Models\AdOs\AdActionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAdSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [120];

    public function __construct(
        private readonly string $type = 'daily',
    ) {}

    public function handle(AdReportService $reportService): void
    {
        try {
            $summary = match ($this->type) {
                'weekly' => $reportService->getWeeklySummary(),
                default => $reportService->getDailySummary(),
            };

            AdActionLog::log('summary_generated', [
                'triggered_by' => 'system',
                'payload' => [
                    'type' => $this->type,
                    'total_spend' => $summary['metrics']['total_spend'] ?? $summary['totals']['spend'] ?? 0,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AdOS GenerateAdSummaryJob failed', ['error' => $e->getMessage()]);
        }
    }
}
