<?php

namespace App\Console\Commands;

use App\Assignment;
use App\Course;
use App\Lesson;
use App\Webinar;
use Illuminate\Console\Command;

class CreateArskursHost2026 extends Command
{
    protected $signature = 'course:create-arskurs-host-2026';
    protected $description = 'Opprett Årskurs Høst 2026 med datoer, oppgaver og webinarer';

    public function handle(): int
    {
        $source = Course::find(119);
        if (!$source) {
            $this->error('Kurs 119 ikke funnet!');
            return 1;
        }

        // === KURS ===
        $new = $source->replicate();
        $new->title = 'Årskurs Høst 2026';
        $new->meta_title = 'Årskurs Høst 2026 — Skriv boken din med Forfatterskolen';
        $new->meta_description = 'Bli med på årskurs hos Forfatterskolen høsten 2026! Egen redaktør følger deg fra idé til ferdig manus. Oppstart 31. august.';
        $new->start_date = '2026-08-31';
        $new->status = 1;
        $new->save();
        $courseId = $new->id;
        $this->info("✅ Kurs opprettet: ID {$courseId} — {$new->title}");

        // === PAKKER ===
        foreach ($source->packages as $pkg) {
            $newPkg = $pkg->replicate();
            $newPkg->course_id = $courseId;
            $newPkg->variation = str_replace(['Årskurs 2026', '2026'], ['Årskurs Høst 2026', 'Høst 2026'], $newPkg->variation);
            $newPkg->save();
            $this->info("  📦 Pakke: {$newPkg->variation} — {$newPkg->full_payment_price} kr");
        }

        // === OPPGAVER (med riktige datoer for Høst 2026) ===
        // Vår: oppstart 26.jan → Høst: oppstart 31.aug (+217 dager)
        // Men tilpasset manuelt for å unngå ferier
        $assignments = [
            [
                'title' => 'Innlevering prosjektbeskrivelse og brev',
                'available_date' => '2026-08-31',        // Kursstart
                'submission_date' => '2026-09-14 23:59',  // 2 uker
                'expected_finish' => '2026-09-28',        // 2 uker til redaktør
            ],
            [
                'title' => 'Første innlevering',
                'available_date' => '2026-09-15',
                'submission_date' => '2026-10-26 23:59',  // Unngår høstferie uke 40 (28.sep-4.okt) — frist ETTER
                'expected_finish' => '2026-11-09',
            ],
            [
                'title' => 'Andre innlevering',
                'available_date' => '2026-10-27',
                'submission_date' => '2026-12-14 23:59',  // Før juleferie
                'expected_finish' => '2027-01-12',        // Redaktør ferdig etter nyttår
            ],
            [
                'title' => 'Tredje innlevering',
                'available_date' => '2027-01-04',         // Etter nyttår
                'submission_date' => '2027-03-16 23:59',  // Før påske (28.mar-5.apr)
                'expected_finish' => '2027-04-07',
            ],
            [
                'title' => 'Fjerde og siste innlevering',
                'available_date' => '2027-03-17',
                'submission_date' => '2027-06-02 23:59',
                'expected_finish' => '2027-06-23',
            ],
            [
                'title' => 'Redigering Årskurs Høst 2026',
                'available_date' => '2026-09-01',
                'submission_date' => '2027-07-31 23:59',
                'expected_finish' => null,
            ],
        ];

        // Kopier fra kilde for å få riktige feltverdier
        $sourceAssignments = Assignment::where('course_id', 119)->get();
        foreach ($assignments as $i => $data) {
            $sourceA = $sourceAssignments[$i] ?? $sourceAssignments->first();
            $newA = $sourceA->replicate();
            $newA->course_id = $courseId;
            $newA->title = $data['title'];
            $newA->available_date = $data['available_date'];
            $newA->submission_date = $data['submission_date'];
            $newA->expected_finish = $data['expected_finish'];
            $newA->save();
            $this->info("  📝 Oppgave: {$newA->title} | frist: {$data['submission_date']}");
        }

        // === LEKSJONER ===
        foreach ($source->lessons as $lesson) {
            $newL = $lesson->replicate();
            $newL->course_id = $courseId;
            $newL->title = str_replace(['Januar 2026', 'Februar 2026', 'Mars 2026'], ['September 2026', 'Oktober 2026', 'November 2026'], $newL->title);
            $newL->save();
            $this->info("  📖 Leksjon: {$newL->title}");
        }

        // === WEBINARER (1 per uke: tirsdag kl 12:00) ===
        // Høstsemester: 1. sep 2026 → jun 2027
        // Unngår: høstferie uke 40 (28.sep-4.okt), juleferie (21.des-4.jan), vinterferie uke 8 (16-22.feb), påske (28.mar-6.apr)
        $skipWeeks = [
            '2026-09-28', // Høstferie uke 40
            '2026-12-21', '2026-12-28', // Juleferie
            '2027-02-15', // Vinterferie uke 8
            '2027-03-29', // Påskeuke
        ];

        $webinarDate = \Carbon\Carbon::parse('2026-09-01'); // Første tirsdag
        $endDate = \Carbon\Carbon::parse('2027-06-30');
        $webinarCount = 0;

        while ($webinarDate->lt($endDate)) {
            $weekStart = $webinarDate->copy()->startOfWeek()->format('Y-m-d');

            if (in_array($weekStart, $skipWeeks)) {
                $webinarDate->addWeek();
                continue;
            }

            // Tirsdag kl 12:00
            $tue = $webinarDate->copy()->startOfWeek()->addDay(); // tirsdag
            $tueDate = $tue->format('d.m.Y');
            Webinar::create([
                'course_id' => $courseId,
                'title' => "Årskurswebinar {$tueDate}",
                'start_date' => $tue->format('Y-m-d') . ' 12:00:00',
                'duration' => 120,
            ]);
            $webinarCount++;

            $webinarDate->addWeek();
        }

        $this->info("  🎥 {$webinarCount} webinarer opprettet (aug 2026 — jun 2027)");

        $this->info('');
        $this->info("🎉 Ferdig! Nytt kurs: /admin/course/{$courseId}");
        $this->info('');
        $this->info('Husk å:');
        $this->info('  1. Oppdatere kursbeskrivelse i admin');
        $this->info('  2. Sette opp Fiken produkt-ID på pakkene');
        $this->info('  3. Opprette webinarer i BigMarker');
        $this->info('  4. Sjekke at alle datoer stemmer');

        return 0;
    }
}
