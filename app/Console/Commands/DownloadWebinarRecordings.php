<?php

namespace App\Console\Commands;

use App\Lesson;
use App\Video;
use App\Webinar;
use App\Services\BigMarkerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadWebinarRecordings extends Command
{
    protected $signature = 'webinar:download-recordings {--webinar-id= : Spesifikt webinar-ID} {--dry-run : Vis hva som ville blitt lastet ned}';

    protected $description = 'Last ned BigMarker-opptak og legg som leksjoner i tilhørende kurs';

    public function handle(): int
    {
        $bigmarker = app(BigMarkerService::class);

        // Hent webinarer som har passert og har BigMarker-link, men ikke er satt som replay ennå
        $query = Webinar::where('status', 1)
            ->where('start_date', '<', now())
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
            // Sjekk om det allerede finnes en leksjon for dette webinaret
            $existingLesson = Lesson::where('course_id', $webinar->course_id)
                ->where('title', 'like', '%' . substr($webinar->title, 0, 50) . '%')
                ->where('title', 'like', '%Opptak%')
                ->first();

            if ($existingLesson) {
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
                // Last ned filen
                $filename = "webinar-recordings/{$conferenceId}.mp4";
                $tempPath = storage_path("app/{$filename}");

                // Opprett mappe
                Storage::makeDirectory('webinar-recordings');

                // Last ned med streaming
                $response = Http::timeout(600)->withOptions(['sink' => $tempPath])->get($recordingUrl);

                if (!file_exists($tempPath) || filesize($tempPath) < 1000) {
                    $this->error("  ❌ Nedlasting feilet for: {$webinar->title}");
                    $failed++;
                    continue;
                }

                $fileSizeMb = round(filesize($tempPath) / 1024 / 1024, 1);
                $this->line("    Lastet ned: {$fileSizeMb} MB");

                // Flytt til public storage
                $publicPath = "webinar-recordings/{$conferenceId}.mp4";
                $storagePath = public_path("storage/{$publicPath}");

                // Opprett mappe i public
                if (!is_dir(dirname($storagePath))) {
                    mkdir(dirname($storagePath), 0755, true);
                }

                rename($tempPath, $storagePath);

                // Opprett leksjon i kurset
                $lesson = Lesson::create([
                    'course_id' => $webinar->course_id,
                    'title' => "Opptak: {$webinar->title}",
                    'type' => 'module',
                    'description' => "Opptak fra webinaret \"{$webinar->title}\" holdt {$webinar->start_date}.",
                    'allow_lesson_download' => 1,
                    'whole_lesson_file' => "/storage/{$publicPath}",
                ]);

                // Legg til video embed
                Video::create([
                    'lesson_id' => $lesson->id,
                    'embed_code' => '<video controls style="width:100%;max-width:800px;"><source src="' . asset("storage/{$publicPath}") . '" type="video/mp4"></video>',
                ]);

                // Merk webinar som replay
                $webinar->update([
                    'set_as_replay' => 1,
                ]);

                $this->info("  ✅ Leksjon opprettet: {$lesson->title} (Kurs: {$webinar->course->title})");
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
