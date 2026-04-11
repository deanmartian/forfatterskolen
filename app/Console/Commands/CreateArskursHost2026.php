<?php

namespace App\Console\Commands;

use App\Assignment;
use App\Course;
use App\Package;
use Illuminate\Console\Command;

class CreateArskursHost2026 extends Command
{
    protected $signature = 'course:create-arskurs-host-2026';
    protected $description = 'Opprett Årskurs Høst 2026 basert på Årskurs Vår 2026 (kurs 119)';

    public function handle(): int
    {
        $source = Course::find(119);
        if (!$source) {
            $this->error('Kurs 119 ikke funnet!');
            return 1;
        }

        // Kopier kurset
        $new = $source->replicate();
        $new->title = 'Årskurs Høst 2026';
        $new->meta_title = 'Årskurs Høst 2026 — Skriv boken din med Forfatterskolen';
        $new->meta_description = 'Bli med på årskurs hos Forfatterskolen høsten 2026! Egen redaktør som følger deg fra idé til ferdig manus. Oppstart september 2026.';
        $new->start_date = '2026-08-31';
        $new->status = 1;
        $new->save();

        $this->info("Kurs opprettet: ID {$new->id} — {$new->title}");

        // Kopier pakker
        foreach ($source->packages as $pkg) {
            $newPkg = $pkg->replicate();
            $newPkg->course_id = $new->id;
            // Oppdater navn
            $newPkg->variation = str_replace('2026', 'Høst 2026', $newPkg->variation);
            $newPkg->save();
            $this->info("  Pakke: {$newPkg->variation} — {$newPkg->full_payment_price} kr");
        }

        // Kopier oppgaver
        $assignments = Assignment::where('course_id', 119)->get();
        foreach ($assignments as $a) {
            $newA = $a->replicate();
            $newA->course_id = $new->id;
            $newA->save();
            $this->info("  Oppgave: {$newA->title}");
        }

        // Kopier leksjoner
        foreach ($source->lessons as $lesson) {
            $newL = $lesson->replicate();
            $newL->course_id = $new->id;
            $newL->save();
            $this->info("  Leksjon: {$newL->title}");
        }

        $this->info('');
        $this->info("Ferdig! Nytt kurs: /admin/course/{$new->id}");
        $this->info('Husk å:');
        $this->info('  1. Oppdatere beskrivelse/innhold i admin');
        $this->info('  2. Sette opp Fiken produkt-ID på pakkene');
        $this->info('  3. Legge til webinarer/mentormøter');

        return 0;
    }
}
