<?php

namespace App\Console\Commands;

use App\Lesson;
use App\LessonContent;
use App\Video;
use App\Webinar;
use App\Services\BigMarkerService;
use App\Services\WistiaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadWebinarRecordings extends Command
{
    protected $signature = 'webinar:download-recordings {--webinar-id= : Spesifikt webinar-ID} {--days=30 : Antall dager tilbake å sjekke} {--dry-run : Vis hva som ville blitt lastet ned}';

    protected $description = 'Last ned BigMarker-opptak og legg som leksjoner i tilhørende kurs';

    public function handle(): int
    {
        $bigmarker = app(BigMarkerService::class);

        // Hent webinarer som har passert og har BigMarker-link, men ikke er satt som replay ennå
        $days = (int) $this->option('days');
        $query = Webinar::where('status', 1)
            ->where('start_date', '<', now())
            ->where('start_date', '>', now()->subDays($days))
            ->whereNotNull('link')
            ->where('link', '!=', '');

        if ($this->option('webinar-id')) {
            $query->where('id', $this->option('webinar-id'));
        }

        $webinars = $query->get();

        $this->info("Fant {$webinars->count()} webinarer å sjekke.");

        $downloaded = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($webinars as $webinar) {
            // Sjekk om det allerede finnes opptak for dette webinaret
            $webinarDate = \Carbon\Carbon::parse($webinar->start_date);

            if ($webinar->course_id == 17) {
                // Mentormøter: sjekk LessonContent
                $existing = LessonContent::whereHas('lesson', fn($q) => $q->where('course_id', 17))
                    ->where('title', 'like', '%' . substr($webinar->title, 0, 40) . '%')
                    ->first();
            } else {
                // Vanlige kurs: sjekk Lesson
                $existing = Lesson::where('course_id', $webinar->course_id)
                    ->where('title', 'like', '%' . substr($webinar->title, 0, 40) . '%')
                    ->where('title', 'like', '%Opptak%')
                    ->first();
            }

            if ($existing) {
                $this->line("  ⏭ Allerede lastet ned: {$webinar->title}");
                $skipped++;
                continue;
            }

            // Hent BigMarker conference ID fra link
            $conferenceId = $this->extractConferenceId($webinar->link);

            if (!$conferenceId) {
                $this->warn("  ⚠ Kunne ikke finne conference ID for: {$webinar->title}");
                $failed++;
                continue;
            }

            // Hent recording URL fra BigMarker
            $recordingUrl = $bigmarker->getRecordingUrl($conferenceId);

            if (!$recordingUrl) {
                $this->line("  — Ingen opptak tilgjengelig: {$webinar->title}");
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $this->info("  [DRY-RUN] Ville lastet ned: {$webinar->title}");
                $this->line("    Recording: {$recordingUrl}");
                $downloaded++;
                continue;
            }

            $this->info("  ⬇ Laster ned: {$webinar->title}");

            try {
                // Last opp til Wistia — organisert per kurs
                $wistia = app(WistiaService::class);
                $videoName = "Opptak: {$webinar->title} ({$webinar->start_date})";
                $courseName = $webinar->course->title ?? 'Ukjent kurs';

                // Finn eller opprett Wistia-prosjekt for kurset
                $projectId = $this->getOrCreateWistiaProject($wistia, $courseName);

                $this->line("    Laster opp til Wistia (prosjekt: {$courseName})...");
                $wistiaResult = $wistia->uploadFromUrl($recordingUrl, $videoName, $projectId);

                $wistiaHashedId = $wistiaResult['hashed_id'] ?? null;

                if (!$wistiaHashedId) {
                    $this->error("  ❌ Wistia-opplasting feilet for: {$webinar->title}");
                    $failed++;
                    continue;
                }

                $this->line("    Wistia ID: {$wistiaHashedId}");

                // Hent embed-kode
                $embedCode = $wistia->getEmbedCode($wistiaHashedId);

                $isMentorCourse = ($webinar->course_id == 17);
                $webinarDate = \Carbon\Carbon::parse($webinar->start_date);

                if ($isMentorCourse) {
                    // MENTORMØTER: Legg til som LessonContent i månedens leksjon
                    $monthName = $webinarDate->translatedFormat('F Y');
                    $lessonTitle = "Reprise {$monthName}";

                    // Finn eller opprett månedens leksjon
                    $lesson = Lesson::where('course_id', $webinar->course_id)
                        ->where('title', $lessonTitle)
                        ->first();

                    if (!$lesson) {
                        $lesson = Lesson::create([
                            'course_id' => $webinar->course_id,
                            'title' => $lessonTitle,
                            'type' => 'module',
                            'allow_lesson_download' => 0,
                        ]);
                        $this->line("    📁 Ny månedsleksjon: {$lessonTitle}");
                    }

                    // Legg til som LessonContent
                    LessonContent::create([
                        'lesson_id' => $lesson->id,
                        'title' => "{$webinar->title} {$webinarDate->format('d.m.Y')}",
                        'lesson_content' => $embedCode,
                        'date' => $webinarDate->format('Y-m-d'),
                    ]);

                    $this->info("  ✅ Mentormøte lagt til i {$lessonTitle}: {$webinar->title} (Wistia: {$wistiaHashedId})");
                } else {
                    // VANLIGE KURS: Opprett egen leksjon
                    $lesson = Lesson::create([
                        'course_id' => $webinar->course_id,
                        'title' => "Opptak: {$webinar->title}",
                        'type' => 'module',
                        'description' => "Opptak fra webinaret \"{$webinar->title}\" holdt {$webinarDate->format('d.m.Y')}.",
                        'allow_lesson_download' => 0,
                    ]);

                    // Legg til Wistia video embed
                    Video::create([
                        'lesson_id' => $lesson->id,
                        'embed_code' => $embedCode,
                    ]);

                    $this->info("  ✅ Leksjon opprettet: {$lesson->title} (Wistia: {$wistiaHashedId})");
                }

                // Merk webinar som replay
                $webinar->update([
                    'set_as_replay' => 1,
                ]);
                $downloaded++;

            } catch (\Exception $e) {
                $this->error("  ❌ Feil for {$webinar->title}: {$e->getMessage()}");
                Log::error("Webinar recording download feilet", [
                    'webinar_id' => $webinar->id,
                    'error' => $e->getMessage(),
                ]);
                $failed++;
            }
        }

        $this->newLine();
        $this->table(
            ['Lastet ned', 'Hoppet over', 'Feilet'],
            [[$downloaded, $skipped, $failed]]
        );

        return 0;
    }

    /**
     * Finn eller opprett Wistia-prosjekt for et kurs
     */
    private function getOrCreateWistiaProject(WistiaService $wistia, string $courseName): string
    {
        // Cache prosjekt-IDer per kjøring
        static $projectCache = [];

        if (isset($projectCache[$courseName])) {
            return $projectCache[$courseName];
        }

        // Søk etter eksisterende prosjekt
        try {
            $projects = $wistia->listProjects(1, 100);
            foreach ($projects as $project) {
                if (($project['name'] ?? '') === $courseName) {
                    $projectCache[$courseName] = $project['hashedId'] ?? $project['hashed_id'] ?? '';
                    return $projectCache[$courseName];
                }
            }

            // Opprett nytt prosjekt
            $newProject = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.wistia.api_token'),
            ])->post('https://api.wistia.com/v1/projects.json', [
                'name' => $courseName,
            ])->json();

            $projectId = $newProject['hashedId'] ?? $newProject['hashed_id'] ?? '';
            $projectCache[$courseName] = $projectId;
            $this->info("    📁 Nytt Wistia-prosjekt opprettet: {$courseName}");

            return $projectId;
        } catch (\Exception $e) {
            Log::warning("Kunne ikke opprette Wistia-prosjekt: {$e->getMessage()}");
            return '';
        }
    }

    /**
     * Ekstraher BigMarker conference ID fra webinar-link
     */
    private function extractConferenceId(string $link): ?string
    {
        // Link format: https://www.bigmarker.com/easywrite/webinar-name
        // Conference ID er i URL-en, men vi trenger API for å finne den
        // Prøv å hente fra URL-path
        $path = parse_url($link, PHP_URL_PATH);

        if (!$path) {
            return null;
        }

        // Sjekk om det er en direkte conference ID (hex)
        $segments = array_filter(explode('/', $path));
        $lastSegment = end($segments);

        // BigMarker conference IDs er 12-tegns hex
        if (preg_match('/^[a-f0-9]{12}$/', $lastSegment)) {
            return $lastSegment;
        }

        // Prøv å hente conference via BigMarker API med URL-slug
        try {
            $bigmarker = app(BigMarkerService::class);
            $conferences = Http::withHeaders([
                'API-KEY' => config('services.big_marker.api_key'),
            ])->get('https://www.bigmarker.com/api/v1/conferences/', [
                'per_page' => 100,
            ])->json('conferences', []);

            foreach ($conferences as $conf) {
                if (str_contains($link, $conf['conference_address'] ?? '')) {
                    return $conf['id'];
                }
                // Match på URL-slug
                $confPath = parse_url($conf['conference_address'] ?? '', PHP_URL_PATH);
                if ($confPath && str_contains($path, trim($confPath, '/'))) {
                    return $conf['id'];
                }
            }
        } catch (\Exception $e) {
            Log::warning("Kunne ikke matche conference ID fra link: {$link}");
        }

        return null;
    }
}
