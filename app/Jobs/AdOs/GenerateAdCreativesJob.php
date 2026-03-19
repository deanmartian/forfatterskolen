<?php

namespace App\Jobs\AdOs;

use App\Services\AdOs\AdCreativeService;
use App\Models\AdOs\AdActionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateAdCreativesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $backoff = [120];

    public function __construct(
        private readonly array $brief,
    ) {}

    public function handle(AdCreativeService $creativeService): void
    {
        try {
            $creatives = $creativeService->generateCreatives($this->brief);

            AdActionLog::log('creatives_batch_generated', [
                'triggered_by' => 'ai',
                'payload' => [
                    'brief_product' => $this->brief['product'] ?? 'unknown',
                    'count' => count($creatives),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('AdOS GenerateAdCreativesJob failed', ['error' => $e->getMessage()]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('AdOS GenerateAdCreativesJob failed permanently', ['error' => $exception->getMessage()]);
    }
}
