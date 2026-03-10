<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\LearnerCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CalendarController extends ApiController
{
    public function events(Request $request, LearnerCalendarService $calendarService): JsonResponse
    {
        $user = $this->apiUser($request);
        $events = $calendarService->formattedEventsForUser($user)->values()->toArray();

        return response()->json([
            'events' => $events,
        ]);
    }

    public function export(Request $request, LearnerCalendarService $calendarService): Response
    {
        $user = $this->apiUser($request);
        $events = $calendarService->eventsForUser($user);
        $timezone = config('app.timezone');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Forfatterskolen//Learner Calendar//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-TIMEZONE:'.$timezone,
        ];

        $lines = array_merge($lines, $this->buildVTimezoneComponent($timezone));

        foreach ($events as $event) {
            $start = $event['start'];
            $end = $event['end'];

            if (! $event['all_day'] && $end->equalTo($start)) {
                $end = $end->copy()->addHour();
            }

            if ($event['all_day']) {
                $dtStart = 'DTSTART;VALUE=DATE:'.$start->format('Ymd');
                $dtEnd = 'DTEND;VALUE=DATE:'.$end->copy()->addDay()->format('Ymd');
            } else {
                $dtStart = 'DTSTART;TZID='.$timezone.':'.$start->copy()->format('Ymd\THis');
                $dtEnd = 'DTEND;TZID='.$timezone.':'.$end->copy()->format('Ymd\THis');
            }

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID='.Str::uuid();
            $lines[] = 'DTSTAMP='.Carbon::now('UTC')->format('Ymd\THis\Z');
            $lines[] = 'SUMMARY='.$this->escapeIcsText($event['title']);
            $lines[] = $dtStart;
            $lines[] = $dtEnd;
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        $icsContent = implode("\r\n", $lines);

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="learner-calendar.ics"',
        ]);
    }

    private function escapeIcsText(string $text): string
    {
        return str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $text);
    }

    private function buildVTimezoneComponent(string $timezone): array
    {
        try {
            $timeZoneObject = new \DateTimeZone($timezone);
        } catch (\Throwable $exception) {
            return [];
        }

        $now = new \DateTimeImmutable('now', $timeZoneObject);
        $from = $now->modify('-1 year')->setTime(0, 0);
        $to = $now->modify('+1 year')->setTime(23, 59, 59);
        $transitions = $timeZoneObject->getTransitions($from->getTimestamp(), $to->getTimestamp());

        if (! $transitions || count($transitions) === 0) {
            return [
                'BEGIN:VTIMEZONE',
                'TZID:'.$timezone,
                'END:VTIMEZONE',
            ];
        }

        $lines = [
            'BEGIN:VTIMEZONE',
            'TZID:'.$timezone,
        ];

        foreach ($transitions as $index => $transition) {
            $next = $transitions[$index + 1] ?? null;
            if ($next === null) {
                continue;
            }

            $componentType = $next['isdst'] ? 'DAYLIGHT' : 'STANDARD';
            $startDate = Carbon::createFromTimestampUTC($next['ts'])->setTimezone($timezone)->format('Ymd\THis');
            $offsetFrom = $this->formatIcsOffset((int) $transition['offset']);
            $offsetTo = $this->formatIcsOffset((int) $next['offset']);
            $tzName = $next['abbr'] ?? $timezone;

            $lines[] = 'BEGIN:'.$componentType;
            $lines[] = 'DTSTART:'.$startDate;
            $lines[] = 'TZOFFSETFROM:'.$offsetFrom;
            $lines[] = 'TZOFFSETTO:'.$offsetTo;
            $lines[] = 'TZNAME:'.$tzName;
            $lines[] = 'END:'.$componentType;
        }

        $lines[] = 'END:VTIMEZONE';

        return $lines;
    }

    private function formatIcsOffset(int $offsetInSeconds): string
    {
        $sign = $offsetInSeconds >= 0 ? '+' : '-';
        $absolute = abs($offsetInSeconds);
        $hours = str_pad((string) intdiv($absolute, 3600), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad((string) intdiv($absolute % 3600, 60), 2, '0', STR_PAD_LEFT);

        return $sign.$hours.$minutes;
    }
}
