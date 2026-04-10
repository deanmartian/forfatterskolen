<?php

namespace App\Console\Commands;

use App\Course;
use App\CoursesTaken;
use App\CronLog;
use App\Jobs\AddMailToQueueJob;
use App\Services\BigMarkerService;
use App\Webinar;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendReplayToNoShows extends Command
{
    protected $signature = 'webinar:send-replay-to-noshows {--dry-run : Vis hva som ville blitt sendt uten å sende}';

    protected $description = 'Send reprise-e-post til kursdeltagere som ikke deltok på webinaret';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        CronLog::create(['activity' => 'SendReplayToNoShows CRON running.']);

        // Hent webinarer fra siste 3 dager som er satt som replay
        $webinars = Webinar::where('set_as_replay', 1)
            ->where('status', 1)
            ->where('start_date', '>=', now()->subDays(3))
            ->where('start_date', '<', now())
            ->whereNotNull('link')
            ->where('link', '!=', '')
            ->get();

        $this->info("Fant {$webinars->count()} webinarer med reprise siste 3 dager.");

        $totalSent = 0;
        $totalSkipped = 0;

        foreach ($webinars as $webinar) {
            $this->info("Behandler: {$webinar->title}");

            // Hent BigMarker conference ID
            $conferenceId = $this->extractConferenceId($webinar->link);

            if (!$conferenceId) {
                $this->warn("  Kunne ikke finne conference ID for: {$webinar->title}");
                continue;
            }

            // Hent deltakere fra BigMarker
            try {
                $bigmarker = app(BigMarkerService::class);
                $attendees = $bigmarker->getAttendees($conferenceId);
                $attendeeEmails = collect($attendees)
                    ->pluck('email')
                    ->map(fn($e) => strtolower(trim($e)))
                    ->filter()
                    ->unique()
                    ->toArray();

                $this->line("  Deltakere fra BigMarker: " . count($attendeeEmails));
            } catch (\Exception $e) {
                $this->error("  BigMarker API-feil: {$e->getMessage()}");
                Log::error("SendReplayToNoShows BigMarker-feil for webinar {$webinar->id}", [
                    'error' => $e->getMessage(),
                ]);
                continue;
            }

            // Hent registrerte kursdeltagere
            $courseId = $webinar->course_id;
            $course = Course::find($courseId);

            if (!$course) {
                $this->warn("  Kurs ikke funnet for webinar: {$webinar->title}");
                continue;
            }

            $coursesTaken = CoursesTaken::whereIn('package_id', $course->packages()->pluck('id'))
                ->where(function ($query) {
                    $query->where('end_date', '>=', Carbon::now())
                        ->orWhereNull('end_date');
                })
                ->where('can_receive_email', 1)
                ->with('user')
                ->get();

            $this->line("  Registrerte kursdeltagere: {$coursesTaken->count()}");

            // Finn no-shows
            $sent = 0;
            $skipped = 0;

            foreach ($coursesTaken as $courseTaken) {
                $user = $courseTaken->user;

                if (!$user || $user->is_disabled) {
                    $skipped++;
                    continue;
                }

                // Kun vanlige elever (role=2), ikke admins/redaktører
                if ($user->role != 2) {
                    $skipped++;
                    continue;
                }

                // Sjekk brukerens preferanse
                if (!$user->receive_replay_emails) {
                    $skipped++;
                    continue;
                }

                $userEmail = strtolower(trim($user->email));

                // Sjekk om brukeren deltok
                if (in_array($userEmail, $attendeeEmails)) {
                    $skipped++;
                    continue;
                }

                // Sjekk om allerede sendt
                $alreadySent = DB::table('webinar_replay_notifications')
                    ->where('webinar_id', $webinar->id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($alreadySent) {
                    $skipped++;
                    continue;
                }

                // Bygg e-post
                $subject = "Reprisen er klar: {$webinar->title}";
                $isMentor = str_contains(strtolower($webinar->title), 'mentor');
                $webinarUrl = $isMentor ? route('learner.webinar') : route('learner.course-webinar');
                $message = $this->buildEmailBody($user->first_name, $webinar->title, $webinarUrl, $isMentor);

                if ($dryRun) {
                    $this->line("  [DRY-RUN] Ville sendt til: {$user->email} ({$user->first_name} {$user->last_name})");
                } else {
                    dispatch(new AddMailToQueueJob(
                        $user->email,
                        $subject,
                        $message,
                        'post@forfatterskolen.no',
                        'Forfatterskolen',
                        null,
                        'webinar-replay',
                        $webinar->id,
                        'emails.mail_to_queue_branded',
                    ));

                    DB::table('webinar_replay_notifications')->insert([
                        'webinar_id' => $webinar->id,
                        'user_id' => $user->id,
                        'sent_at' => now(),
                    ]);

                    CronLog::create(['activity' => "SendReplayToNoShows sendt til {$user->email} for webinar {$webinar->id}"]);
                }

                $sent++;
            }

            $this->info("  Sendt: {$sent}, Hoppet over: {$skipped}");
            $totalSent += $sent;
            $totalSkipped += $skipped;
        }

        $this->info("Ferdig. Totalt sendt: {$totalSent}, totalt hoppet over: {$totalSkipped}");
        CronLog::create(['activity' => "SendReplayToNoShows CRON ferdig. Sendt: {$totalSent}, hoppet over: {$totalSkipped}"]);

        return self::SUCCESS;
    }

    private function buildEmailBody(string $firstName, string $webinarTitle, string $url, bool $isMentor = false): string
    {
        $sectionLabel = $isMentor ? 'Mentorm&oslash;ter' : 'Kurswebinarer';
        $sectionUrl = $isMentor ? route('learner.webinar') : route('learner.course-webinar');

        return <<<HTML
<p>Hei {$firstName},</p>

<p>Vi s&aring; at du ikke fikk med deg <strong>{$webinarTitle}</strong>. Ikke bekymre deg &mdash; reprisen er n&aring; klar!</p>

<p style="text-align: center; margin: 2rem 0;">
    <a href="{$url}" style="display: inline-block; padding: 14px 32px; background-color: #862736; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px;">Se reprisen &rarr;</a>
</p>

<p>Du finner reprisen p&aring; din kursside under &laquo;<a href="{$sectionUrl}" style="color: #862736; text-decoration: underline;">{$sectionLabel}</a>&raquo;.</p>

<p>Lykke til med skrivingen!</p>
HTML;
    }

    private function extractConferenceId(string $link): ?string
    {
        $path = parse_url($link, PHP_URL_PATH);

        if (!$path) {
            return null;
        }

        $segments = array_filter(explode('/', $path));
        $lastSegment = end($segments);

        // BigMarker conference IDs er 12-tegns hex
        if (preg_match('/^[a-f0-9]{12}$/', $lastSegment)) {
            return $lastSegment;
        }

        // Prøv å finne via BigMarker API
        try {
            $conferences = Http::withHeaders([
                'API-KEY' => config('services.big_marker.api_key'),
            ])->get('https://www.bigmarker.com/api/v1/conferences/', [
                'per_page' => 100,
            ])->json('conferences', []);

            foreach ($conferences as $conf) {
                if (str_contains($link, $conf['conference_address'] ?? '')) {
                    return $conf['id'];
                }
                $confPath = parse_url($conf['conference_address'] ?? '', PHP_URL_PATH);
                if ($confPath && str_contains($path, trim($confPath, '/'))) {
                    return $conf['id'];
                }
            }
        } catch (\Exception $e) {
            Log::warning("Kunne ikke hente conference ID for {$link}: {$e->getMessage()}");
        }

        // Fallback: bruk hele URL-path som ID
        return trim($path, '/');
    }
}
