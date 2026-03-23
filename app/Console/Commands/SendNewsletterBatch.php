<?php

namespace App\Console\Commands;

use App\Models\Newsletter;
use App\Services\NewsletterService;
use Illuminate\Console\Command;

class SendNewsletterBatch extends Command
{
    protected $signature = 'newsletter:send-batch {--batch-size=200} {--waves=0 : Number of waves (0=until done)} {--pause=60 : Seconds between waves}';

    protected $description = 'Send newsletter in batches with pauses between waves';

    public function handle(NewsletterService $service): int
    {
        $newsletter = Newsletter::where('status', 'sending')->first();

        if (!$newsletter) {
            $this->info('Ingen nyhetsbrev i sending-status.');
            return self::SUCCESS;
        }

        $batchSize = (int) $this->option('batch-size');
        $maxWaves = (int) $this->option('waves');
        $pauseSeconds = (int) $this->option('pause');
        $wave = 0;

        $pending = $newsletter->pendingSends()->count();
        $this->info("Nyhetsbrev: {$newsletter->subject}");
        $this->info("Gjenstår: {$pending} av {$newsletter->total_recipients}");
        $this->info("Batch: {$batchSize}, Pause: {$pauseSeconds}s mellom bølger");
        $this->newLine();

        while (true) {
            $wave++;
            $sent = $service->sendBatch($newsletter, $batchSize);

            if ($sent === 0) {
                $this->info("Ferdig! Alle mailer er sendt.");
                break;
            }

            $remaining = $newsletter->pendingSends()->count();
            $this->info("Bølge {$wave}: {$sent} sendt, {$remaining} gjenstår");

            if ($maxWaves > 0 && $wave >= $maxWaves) {
                $this->info("Stoppet etter {$maxWaves} bølger. Kjør kommandoen igjen for å fortsette.");
                break;
            }

            if ($remaining > 0) {
                $this->info("Venter {$pauseSeconds}s før neste bølge...");
                sleep($pauseSeconds);
            }
        }

        $newsletter->refresh();
        $this->newLine();
        $this->info("Status: {$newsletter->status}");
        $this->info("Sendt: {$newsletter->total_sent}, Feilet: {$newsletter->total_failed}");

        return self::SUCCESS;
    }
}
