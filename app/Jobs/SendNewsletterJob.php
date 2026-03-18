<?php

namespace App\Jobs;

use App\Models\Newsletter;
use App\Services\NewsletterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        protected int $newsletterId,
    ) {}

    public function handle(NewsletterService $service): void
    {
        $newsletter = Newsletter::find($this->newsletterId);

        if (! $newsletter || $newsletter->status === 'cancelled') {
            return;
        }

        $sent = $service->sendBatch($newsletter, 500);

        // Hvis det fortsatt er flere å sende, planlegg neste batch om 60 sekunder
        if ($sent > 0 && $newsletter->fresh()->status === 'sending') {
            self::dispatch($this->newsletterId)->delay(now()->addSeconds(60));
        }
    }
}
