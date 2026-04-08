<?php

namespace App\Console\Commands;

use App\Enums\AiToolActionStatus;
use App\Models\AiToolAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Markerer AI-forslag som har stått urørt for lenge som "expired".
 * Kjøres fra Kernel via scheduler én gang i døgnet.
 *
 * Forslagene slettes IKKE — bare statusen endres, slik at audit-siden
 * fortsatt kan vise historikken.
 */
class ExpireOldAiToolActions extends Command
{
    protected $signature = 'ai-tools:expire-old';

    protected $description = 'Markerer gamle suggested AI-tool-handlinger som expired';

    public function handle(): int
    {
        $now = now();

        $query = AiToolAction::where('status', AiToolActionStatus::Suggested->value)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now);

        $count = (clone $query)->count();

        if ($count === 0) {
            $this->info('Ingen gamle forslag å markere som expired.');
            return self::SUCCESS;
        }

        $query->update([
            'status' => AiToolActionStatus::Expired->value,
        ]);

        $this->info("Markerte {$count} forslag som expired.");
        Log::info('AI tool actions expired', ['count' => $count]);

        return self::SUCCESS;
    }
}
