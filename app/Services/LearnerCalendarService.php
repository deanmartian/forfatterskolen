<?php

namespace App\Services;

use App\Http\FrontendHelpers;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LearnerCalendarService
{
    public function eventsForUser(User $user): Collection
    {
        $events = collect();
        $timezone = config('app.timezone');

        foreach ($user->coursesTaken as $courseTaken) {
            foreach ($courseTaken->package->course->lessons as $lesson) {
                $availabilityText = FrontendHelpers::lessonAvailability(
                    $courseTaken->started_at,
                    $lesson->delay,
                    $lesson->period
                );

                if ($availabilityText === 'Course not started') {
                    continue;
                }

                try {
                    $availability = Carbon::parse($availabilityText, $timezone)->startOfDay();
                } catch (\Throwable $exception) {
                    continue;
                }

                $events->push([
                    'id' => $lesson->course->id,
                    'type' => 'lesson',
                    'title' => trans('site.learner.lesson') . ': '.$lesson->title.' fra '.$lesson->course->title,
                    'className' => 'event-important',
                    'start' => $availability->copy(),
                    'end' => $availability->copy(),
                    'color' => '#d95e66',
                    'all_day' => true,
                ]);
            }

            foreach ($courseTaken->package->course->webinars as $webinar) {
                $start = Carbon::parse($webinar->start_date, $timezone);
                $end = $start->copy()->addHour();

                $events->push([
                    'id' => $webinar->course->id,
                    'type' => 'webinar',
                    'title' => trans('site.learner.webinar') .': '.$webinar->title.' fra '.$webinar->course->title,
                    'className' => 'event-warning',
                    'start' => $start->copy(),
                    'end' => $end,
                    'color' => '#f7d046',
                    'textColor' => '#2e3a59',
                    'all_day' => false,
                ]);
            }

            foreach ($courseTaken->manuscripts as $manuscript) {
                $finishDate = Carbon::parse($manuscript->expected_finish, $timezone)->startOfDay();

                $events->push([
                    'id' => $courseTaken->package->course->id,
                    'type' => 'manuscript',
                    'title' => trans('site.learner.script') .': '.basename($manuscript->filename).' fra '.$courseTaken->package->course->title,
                    'className' => 'event-info',
                    'start' => $finishDate->copy(),
                    'end' => $finishDate->copy(),
                    'color' => '#29b5f5',
                    'all_day' => true,
                ]);
            }

            foreach ($courseTaken->package->course->assignments as $assignment) {
                $allowedPackage = json_decode($assignment->allowed_package, true);

                if (is_null($allowedPackage) || in_array($courseTaken->package_id, (array) $allowedPackage)) {
                    // submission_date can be a date string or days-offset integer
                    if (is_numeric($assignment->submission_date) && (int) $assignment->submission_date < 10000) {
                        // Days offset from course start
                        $submissionDate = Carbon::parse($courseTaken->started_at, $timezone)->addDays((int) $assignment->submission_date)->startOfDay();
                    } else {
                        // Actual date string
                        $submissionDate = Carbon::parse($assignment->submission_date, $timezone)->startOfDay();
                    }

                    $events->push([
                        'id' => $assignment->course->id,
                        'type' => 'assignment',
                        'title' => trans('site.learner.assignment') . ': ' . $assignment->title . ' fra ' . $assignment->course->title,
                        'className' => 'event-success-new',
                        'start' => $submissionDate->copy(),
                        'end' => $submissionDate->copy(),
                        'color' => '#44af5e',
                        'all_day' => true,
                    ]);
                }
            }

            foreach ($courseTaken->package->course->notes as $note) {
                $fromDate = Carbon::parse($note->from_date, $timezone)->startOfDay();
                $toDate = Carbon::parse($note->to_date, $timezone)->startOfDay();

                $events->push([
                    'id' => $note->id,
                    'type' => 'note',
                    'title' => $note->note,
                    'className' => 'event-inverse',
                    'start' => $fromDate->copy(),
                    'end' => $toDate->copy(),
                    'color' => '#1b1b1b',
                    'all_day' => true,
                ]);
            }
        }

        // Påbygg-treff / Samlinger
        $pabyggCourse = $user->coursesTaken->first(function ($ct) {
            return $ct->package && $ct->package->course_id == 120 && $ct->is_active;
        });
        if ($pabyggCourse && $pabyggCourse->pabygg_treff_day && $pabyggCourse->pabygg_treff_day !== 'digital') {
            $treffDate = $pabyggCourse->pabygg_treff_day === 'friday'
                ? Carbon::parse('2026-05-08', $timezone)->startOfDay()
                : Carbon::parse('2026-05-09', $timezone)->startOfDay();
            $dayLabel = $pabyggCourse->pabygg_treff_day === 'friday' ? 'Fredag 8. mai' : 'Lørdag 9. mai';

            $events->push([
                'id' => 'pabygg-treff',
                'type' => 'samling',
                'title' => '📍 Samling: Påbyggingstreff – ' . $dayLabel,
                'className' => 'event-samling',
                'start' => $treffDate->copy(),
                'end' => $treffDate->copy(),
                'color' => '#8e44ad',
                'all_day' => true,
            ]);
        }

        $approvedCoaching = $user->coachingTimers()->whereNotNull('approved_date')->get();
        foreach ($approvedCoaching as $coaching) {
            $start = Carbon::parse($coaching->approved_date, $timezone);

            $events->push([
                'id' => $coaching->id,
                'type' => 'coaching',
                'title' => 'Coaching Session at '.date('H:i A', strtotime($coaching->approved_date)),
                'className' => 'event-inverse',
                'start' => $start->copy(),
                'end' => $start->copy(),
                'color' => '#f00',
                'all_day' => $this->isAllDayEvent($start),
            ]);
        }

        // Selvpublisering
        foreach ($user->selfPublishingList as $sp) {
            foreach ($sp->tasks ?? [] as $task) {
                if (!empty($task->deadline)) {
                    try {
                        $deadline = Carbon::parse($task->deadline, $timezone)->startOfDay();
                        $events->push([
                            'id' => $sp->id,
                            'type' => 'self-publishing',
                            'title' => 'Selvpublisering: ' . ($task->title ?? $sp->title ?? 'Oppgave'),
                            'className' => 'event-sp',
                            'start' => $deadline->copy(),
                            'end' => $deadline->copy(),
                            'color' => '#7c4dff',
                            'all_day' => true,
                        ]);
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }
        }

        // Språkvask
        $addedCeIds = [];
        foreach ($user->copyEditings as $ce) {
            if (!empty($ce->getRawOriginal('expected_finish')) && !in_array($ce->id, $addedCeIds)) {
                try {
                    $finish = Carbon::parse($ce->getRawOriginal('expected_finish'), $timezone)->startOfDay();
                    $addedCeIds[] = $ce->id;
                    $events->push([
                        'id' => $ce->id,
                        'type' => 'copy-editing',
                        'title' => 'Språkvask: ' . basename($ce->file ?? 'Manus'),
                        'className' => 'event-teal',
                        'start' => $finish->copy(),
                        'end' => $finish->copy(),
                        'color' => '#26a69a',
                        'all_day' => true,
                    ]);
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }
        // Språkvask via prosjekter
        $projectCe = CopyEditingManuscript::where('user_id', $user->id)
            ->whereNotNull('expected_finish')
            ->whereNotIn('id', $addedCeIds)
            ->get();
        foreach ($projectCe as $ce) {
            try {
                $finish = Carbon::parse($ce->getRawOriginal('expected_finish'), $timezone)->startOfDay();
                $events->push([
                    'id' => $ce->id,
                    'type' => 'copy-editing',
                    'title' => 'Språkvask: ' . basename($ce->file ?? 'Manus'),
                    'className' => 'event-teal',
                    'start' => $finish->copy(),
                    'end' => $finish->copy(),
                    'color' => '#26a69a',
                    'all_day' => true,
                ]);
            } catch (\Throwable $e) {
                continue;
            }
        }

        // Korrektur
        $addedCrIds = [];
        foreach ($user->corrections as $cr) {
            if (!empty($cr->getRawOriginal('expected_finish')) && !in_array($cr->id, $addedCrIds)) {
                try {
                    $finish = Carbon::parse($cr->getRawOriginal('expected_finish'), $timezone)->startOfDay();
                    $addedCrIds[] = $cr->id;
                    $events->push([
                        'id' => $cr->id,
                        'type' => 'correction',
                        'title' => 'Korrektur: ' . basename($cr->file ?? 'Manus'),
                        'className' => 'event-magenta',
                        'start' => $finish->copy(),
                        'end' => $finish->copy(),
                        'color' => '#e91e63',
                        'all_day' => true,
                    ]);
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }
        // Korrektur via prosjekter
        $projectCr = CorrectionManuscript::where('user_id', $user->id)
            ->whereNotNull('expected_finish')
            ->whereNotIn('id', $addedCrIds)
            ->get();
        foreach ($projectCr as $cr) {
            try {
                $finish = Carbon::parse($cr->getRawOriginal('expected_finish'), $timezone)->startOfDay();
                $events->push([
                    'id' => $cr->id,
                    'type' => 'correction',
                    'title' => 'Korrektur: ' . basename($cr->file ?? 'Manus'),
                    'className' => 'event-magenta',
                    'start' => $finish->copy(),
                    'end' => $finish->copy(),
                    'color' => '#e91e63',
                    'all_day' => true,
                ]);
            } catch (\Throwable $e) {
                continue;
            }
        }

        return $events;
    }

    public function formattedEventsForUser(User $user): Collection
    {
        return $this->eventsForUser($user)->map(function (array $event) {
            $allDay = (bool) $event['all_day'];
            $start = $event['start'];
            $end = $event['end'];

            return [
                'id' => $event['id'],
                'type' => $event['type'] ?? 'event',
                'title' => $event['title'],
                'className' => $event['className'],
                'start' => $this->formatCalendarDateTime($start, $allDay),
                'end' => $this->formatCalendarEnd($start, $end, $allDay),
                'all_day' => $allDay,
                'allDay' => $allDay,
                'color' => $event['color'],
            ];
        });
    }

    private function formatCalendarDateTime(Carbon $dateTime, bool $allDay): string
    {
        return $allDay
            ? $dateTime->toDateString()
            : $dateTime
                ->copy()
                ->toIso8601String();
    }

    private function formatCalendarEnd(Carbon $start, Carbon $end, bool $allDay): string
    {
        if ($allDay) {
            return $end->copy()->addDay()->toDateString();
        }

        $adjustedEnd = $end->copy();

        if ($adjustedEnd->lessThanOrEqualTo($start)) {
            $adjustedEnd = $start->copy()->addHour();
        }

        return $adjustedEnd->toIso8601String();
    }

    private function isAllDayEvent(Carbon $start): bool
    {
        return $start->isStartOfDay();
    }
}
