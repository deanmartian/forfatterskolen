<?php

namespace App\Mail;

use App\Assignment;
use App\CoursesTaken;
use App\Http\FrontendHelpers;
use App\Lesson;
use App\User;
use App\Webinar;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklyDigestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    protected static $quotes = [
        ['text' => 'Skriv det du vil lese.', 'author' => 'Toni Morrison'],
        ['text' => 'Det finnes ingen regler. Det er slik det er mulig.', 'author' => 'Virginia Woolf'],
        ['text' => 'Du trenger ikke se hele trappen, bare ta det første steget.', 'author' => 'Martin Luther King Jr.'],
        ['text' => 'Start der du er. Bruk det du har. Gjør det du kan.', 'author' => 'Arthur Ashe'],
        ['text' => 'En forfatter er en som skriver.', 'author' => 'Anne Enright'],
        ['text' => 'Skriv hardt og klart om det som gjør vondt.', 'author' => 'Ernest Hemingway'],
        ['text' => 'Du kan alltid redigere en dårlig side. Du kan ikke redigere en blank side.', 'author' => 'Jodi Picoult'],
        ['text' => 'Å skrive er å tenke på papir.', 'author' => 'William Zinsser'],
        ['text' => 'Inspirasjonen finnes, men den må finne deg i arbeid.', 'author' => 'Pablo Picasso'],
        ['text' => 'Hver ekspert var en gang en nybegynner.', 'author' => 'Helen Hayes'],
        ['text' => 'Det viktigste er ikke å ha skrevet, men å skrive.', 'author' => 'Karl Ove Knausgård'],
        ['text' => 'Skriving er den eneste tingen der du ikke føler deg bedre etter en dusj.', 'author' => 'Robert Frost'],
    ];

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $data = $this->buildDigestData();

        if (empty($data)) {
            return null;
        }

        $subject = $this->generateSubjectLine($data);

        return $this->from('post@forfatterskolen.no', 'Kristine S. Henningsen')
            ->subject($subject)
            ->view('emails.branded.weekly-digest', $data);
    }

    public function buildDigestData(): ?array
    {
        $user = $this->user;
        $now = Carbon::now();
        $weekStart = $now->copy()->startOfWeek(); // Monday
        $weekEnd = $now->copy()->endOfWeek();     // Sunday

        // Get active courses for this user
        $coursesTaken = CoursesTaken::where('user_id', $user->id)
            ->where(fn($q) => $q->where('end_date', '>=', $now->format('Y-m-d'))->orWhereNull('end_date'))
            ->where('can_receive_email', 1)
            ->get();

        if ($coursesTaken->isEmpty()) {
            return null;
        }

        $courseIds = $coursesTaken->map(function ($ct) {
            return $ct->package ? $ct->package->course_id : null;
        })->filter()->unique()->values();

        if ($courseIds->isEmpty()) {
            return null;
        }

        // a) Mentormøte (course 17) this week
        $mentorMeeting = $this->getMentorMeeting($weekStart, $weekEnd);

        // b) Course webinars this week (excluding course 17)
        $webinars = $this->getCourseWebinars($courseIds, $weekStart, $weekEnd);

        // c) Upcoming modules this week (per user's CoursesTaken)
        $upcomingModules = $this->getUpcomingModules($coursesTaken, $weekStart, $weekEnd);

        // d) Assignment deadlines this week
        $assignmentDeadlines = $this->getAssignmentDeadlines($courseIds, $weekStart, $weekEnd);

        // e) Quote
        $quote = self::$quotes[$now->weekOfYear % count(self::$quotes)];

        // Check if there's any content
        $hasContent = $mentorMeeting || count($webinars) > 0 || count($upcomingModules) > 0 || count($assignmentDeadlines) > 0;
        if (!$hasContent) {
            return null;
        }

        $norDays = ['mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag', 'søndag'];
        $norMonths = ['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember'];

        $startDay = (int) $weekStart->format('j');
        $endDay = (int) $weekEnd->format('j');
        $startMonth = $norMonths[$weekStart->month - 1];
        $endMonth = $norMonths[$weekEnd->month - 1];

        if ($weekStart->month === $weekEnd->month) {
            $weekRange = $startDay . '.' . " – " . $endDay . '. ' . $startMonth;
        } else {
            $weekRange = $startDay . '. ' . $startMonth . ' – ' . $endDay . '. ' . $endMonth;
        }

        return [
            'firstName' => $user->first_name,
            'weekNumber' => $now->weekOfYear,
            'weekRange' => $weekRange,
            'mentorMeeting' => $mentorMeeting,
            'webinars' => $webinars,
            'upcomingModules' => $upcomingModules,
            'assignmentDeadlines' => $assignmentDeadlines,
            'quote' => $quote,
            'portalUrl' => config('app.url') . '/learner/dashboard',
        ];
    }

    protected function getMentorMeeting(Carbon $weekStart, Carbon $weekEnd): ?array
    {
        $webinar = Webinar::where('course_id', 17)
            ->where('start_date', '>=', $weekStart->format('Y-m-d H:i:s'))
            ->where('start_date', '<=', $weekEnd->format('Y-m-d H:i:s'))
            ->active()->notReplay()
            ->with('webinar_presenters')
            ->orderBy('start_date')
            ->first();

        if (!$webinar) {
            return null;
        }

        $presenter = null;
        if ($webinar->webinar_presenters->isNotEmpty()) {
            $p = $webinar->webinar_presenters->first();
            $presenter = [
                'name' => trim($p->first_name . ' ' . $p->last_name),
                'image' => $p->image ? config('app.url') . '/storage/' . $p->image : null,
            ];
        }

        $startDate = Carbon::parse($webinar->start_date);
        $norDays = ['mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag', 'søndag'];
        $dayName = $norDays[$startDate->dayOfWeekIso - 1];

        return [
            'title' => $webinar->title,
            'date' => ucfirst($dayName) . ' ' . $startDate->format('d.m') . ' kl. ' . $startDate->format('H:i'),
            'description' => $webinar->description,
            'presenter' => $presenter,
            'link' => $webinar->link,
        ];
    }

    protected function getCourseWebinars($courseIds, Carbon $weekStart, Carbon $weekEnd): array
    {
        $webinars = Webinar::whereIn('course_id', $courseIds->toArray())
            ->where('course_id', '!=', 17)
            ->where('start_date', '>=', $weekStart->format('Y-m-d H:i:s'))
            ->where('start_date', '<=', $weekEnd->format('Y-m-d H:i:s'))
            ->active()->notReplay()
            ->with('course')
            ->orderBy('start_date')
            ->get();

        $result = [];
        foreach ($webinars as $w) {
            $startDate = Carbon::parse($w->start_date);
            $norDays = ['mandag', 'tirsdag', 'onsdag', 'torsdag', 'fredag', 'lørdag', 'søndag'];
            $dayName = $norDays[$startDate->dayOfWeekIso - 1];

            $result[] = [
                'title' => $w->title,
                'date' => ucfirst($dayName) . ' ' . $startDate->format('d.m') . ' kl. ' . $startDate->format('H:i'),
                'courseName' => $w->course ? $w->course->title : '',
            ];
        }

        return $result;
    }

    protected function getUpcomingModules($coursesTaken, Carbon $weekStart, Carbon $weekEnd): array
    {
        $excludeTitles = ['Kursplan', 'Repriser'];
        $result = [];

        foreach ($coursesTaken as $ct) {
            $package = $ct->package;
            if (!$package) continue;

            $course = $package->course;
            if (!$course) continue;

            $startedAtRaw = $ct->attributes['started_at'] ?? null;
            if (empty($startedAtRaw)) continue;

            $lessons = $course->lessons()
                ->whereNotIn('title', $excludeTitles)
                ->orderBy('order', 'asc')
                ->get();

            foreach ($lessons as $lesson) {
                $availDateStr = FrontendHelpers::lessonAvailability($startedAtRaw, $lesson->delay, $lesson->period);
                if ($availDateStr === 'Course not started') continue;

                $availDate = Carbon::parse($availDateStr);

                // Check if this lesson becomes available this week
                if ($availDate->between($weekStart, $weekEnd)) {
                    $result[] = [
                        'order' => $lesson->order,
                        'title' => $lesson->title,
                        'courseName' => $course->title,
                        'availableDate' => $availDate->format('d.m'),
                    ];
                }
            }
        }

        return $result;
    }

    protected function getAssignmentDeadlines($courseIds, Carbon $weekStart, Carbon $weekEnd): array
    {
        $assignments = Assignment::whereIn('course_id', $courseIds->toArray())
            ->whereRaw("submission_date BETWEEN ? AND ?", [
                $weekStart->format('Y-m-d 00:00:00'),
                $weekEnd->format('Y-m-d 23:59:59'),
            ])
            ->forCourseOnly()
            ->with('course')
            ->get();

        $result = [];
        foreach ($assignments as $a) {
            $rawDate = $a->getRawOriginal('submission_date');
            $deadlineDate = Carbon::parse($rawDate);

            $result[] = [
                'title' => $a->title,
                'deadline' => $deadlineDate->format('d.m.Y H:i'),
                'courseName' => $a->course ? $a->course->title : '',
            ];
        }

        return $result;
    }

    protected function generateSubjectLine(array $data): string
    {
        $parts = [];

        if (!empty($data['mentorMeeting'])) {
            $presenterName = $data['mentorMeeting']['presenter']['name'] ?? null;
            if ($presenterName) {
                $parts[] = $presenterName . ' denne uken';
            } else {
                $parts[] = 'Mentormøte denne uken';
            }
        }

        $webinarCount = count($data['webinars'] ?? []);
        if ($webinarCount > 0) {
            $parts[] = $webinarCount . ' webinar' . ($webinarCount > 1 ? 'er' : '');
        }

        $moduleCount = count($data['upcomingModules'] ?? []);
        if ($moduleCount > 0) {
            $parts[] = $moduleCount . ' ny' . ($moduleCount > 1 ? 'e' : '') . ' modul' . ($moduleCount > 1 ? 'er' : '');
        }

        $deadlineCount = count($data['assignmentDeadlines'] ?? []);
        if ($deadlineCount > 0) {
            $parts[] = $deadlineCount . ' frist' . ($deadlineCount > 1 ? 'er' : '');
        }

        if (!empty($parts)) {
            return ucfirst(implode(' + ', $parts)) . ' — uke ' . $data['weekNumber'];
        }

        return 'God morgen! Her er uken din hos Forfatterskolen';
    }
}
