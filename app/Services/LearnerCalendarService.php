<?php

namespace App\Services;

use App\Http\FrontendHelpers;
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
                    $submissionDate = Carbon::parse((int) $assignment->submission_date, $timezone)->startOfDay();

                    $events->push([
                        'id' => $assignment->course->id,
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
                    'title' => $note->note,
                    'className' => 'event-inverse',
                    'start' => $fromDate->copy(),
                    'end' => $toDate->copy(),
                    'color' => '#1b1b1b',
                    'all_day' => true,
                ]);
            }
        }

        $approvedCoaching = $user->coachingTimers()->whereNotNull('approved_date')->get();
        foreach ($approvedCoaching as $coaching) {
            $start = Carbon::parse($coaching->approved_date, $timezone);

            $events->push([
                'id' => $coaching->id,
                'title' => 'Coaching Session at '.date('H:i A', strtotime($coaching->approved_date)),
                'className' => 'event-inverse',
                'start' => $start->copy(),
                'end' => $start->copy(),
                'color' => '#f00',
                'all_day' => $this->isAllDayEvent($start),
            ]);
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
