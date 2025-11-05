<?php

namespace App\Console\Commands;

use App\CoachingTimerManuscript;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FinalizeCoachingTimerManuscripts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'coachingtimer:finalize';

    /**
     * The console command description.
     */
    protected $description = 'Mark coaching timer manuscripts as finished when their session has passed and feedback is available';

    public function handle(): void
    {
        $cutoffDate = Carbon::yesterday('UTC');

        $manuscripts = CoachingTimerManuscript::with('timeSlot')
            ->where('status', '!=', CoachingTimerManuscript::STATUS_FINISHED)
            ->whereNotNull('editor_time_slot_id')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('replay_link')
                        ->where('replay_link', '!=', '');
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('comment')
                        ->where('comment', '!=', '');
                })
                ->orWhere(function ($q) {
                    $q->whereNotNull('document')
                        ->where('document', '!=', '');
                });
            })
            ->get();

        $updated = 0;

        foreach ($manuscripts as $manuscript) {
            $slot = $manuscript->timeSlot;

            if (!$slot) {
                continue;
            }

            if (empty($slot->date)) {
                continue;
            }

            $slotDate = Carbon::parse($slot->date, 'UTC');

            if ($slotDate->lessThanOrEqualTo($cutoffDate)) {
                $manuscript->status = CoachingTimerManuscript::STATUS_FINISHED;
                $manuscript->save();
                $updated++;
            }
        }

        $this->info(sprintf('Updated %d coaching timer manuscripts.', $updated));
    }
}
