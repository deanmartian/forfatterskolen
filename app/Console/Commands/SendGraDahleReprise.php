<?php

namespace App\Console\Commands;

use App\Jobs\AddMailToQueueJob;
use App\Models\Contact;
use App\Models\EmailAutomationQueue;
use App\Models\EmailSequence;
use App\Models\EmailSequenceStep;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendGraDahleReprise extends Command
{
    protected $signature = 'webinar:send-gro-dahle-reprise {--dry-run : Vis hva som ville blitt sendt} {--now : Send umiddelbart i stedet for kl 23:00}';

    protected $description = 'Send reprise-e-post til alle Gro Dahle webinar-påmeldte med riktig lenke';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $sendNow = $this->option('now');

        $scheduledAt = $sendNow ? now() : Carbon::today()->setTime(23, 0);

        if ($scheduledAt->isPast() && !$sendNow) {
            $scheduledAt = now()->addMinutes(5);
            $this->warn("23:00 er passert, sender om 5 minutter i stedet.");
        }

        // Finn eller opprett sekvensen
        $sequence = EmailSequence::firstOrCreate(
            ['name' => 'Gro Dahle reprise - riktig lenke'],
            [
                'trigger_event' => 'manual',
                'description' => 'Beklager teknisk feil + riktig repriselenke til alle påmeldte',
                'from_type' => 'transactional',
                'is_active' => 1,
            ]
        );

        // Steg 1: Ikke-elever (med kurssalg)
        $stepNonStudent = EmailSequenceStep::firstOrCreate(
            ['sequence_id' => $sequence->id, 'step_number' => 1],
            [
                'subject' => '🎬 Reprisen er klar — beklager ventet!',
                'body_html' => $this->getNonStudentBody(),
                'delay_hours' => 0,
                'from_type' => 'transactional',
                'only_without_active_course' => 1,
            ]
        );

        // Steg 2: Elever (kun reprise)
        $stepStudent = EmailSequenceStep::firstOrCreate(
            ['sequence_id' => $sequence->id, 'step_number' => 2],
            [
                'subject' => '🎬 Reprisen er klar — beklager ventet!',
                'body_html' => $this->getStudentBody(),
                'delay_hours' => 0,
                'from_type' => 'transactional',
                'only_without_active_course' => 0,
            ]
        );

        $this->info("Sekvens: {$sequence->name} (ID: {$sequence->id})");
        $this->info("Du kan redigere den i admin under Sekvenser.");
        $this->newLine();

        // Hent alle påmeldte for webinar 94
        $registrations = DB::table('webinar_registrations')
            ->where('free_webinar_id', 94)
            ->get();

        $this->info("Fant {$registrations->count()} registreringer for Gro Dahle-webinaret.");

        // Finn elev-e-poster
        $studentEmails = DB::table('courses_taken')
            ->join('users', 'courses_taken.user_id', '=', 'users.id')
            ->where('courses_taken.is_active', 1)
            ->pluck('users.email')
            ->map(fn($e) => strtolower(trim($e)))
            ->unique()
            ->toArray();

        $this->info("Fant " . count($studentEmails) . " aktive elever.");

        $queued = 0;
        $skipped = 0;

        foreach ($registrations as $reg) {
            $email = strtolower(trim($reg->email));
            $isStudent = in_array($email, $studentEmails);
            $step = $isStudent ? $stepStudent : $stepNonStudent;

            // Finn kontakt
            $contact = Contact::where('email', $email)->first();

            if (!$contact) {
                $contact = Contact::create([
                    'email' => $email,
                    'first_name' => $reg->first_name ?? '',
                    'last_name' => $reg->last_name ?? '',
                    'source' => 'webinar',
                ]);
            }

            // Sjekk om allerede i kø
            $exists = EmailAutomationQueue::where('contact_id', $contact->id)
                ->where('step_id', $step->id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $type = $isStudent ? 'ELEV' : 'IKKE-ELEV';
                $this->line("  [{$type}] {$email} ({$reg->first_name})");
                $queued++;
                continue;
            }

            EmailAutomationQueue::create([
                'contact_id' => $contact->id,
                'email' => $email,
                'sequence_id' => $sequence->id,
                'step_id' => $step->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
                'metadata' => [
                    'webinar_id' => 94,
                    'webinar_title' => 'Gratiswebinar med Gro Dahle: Slik skaper du karakterer som lever',
                    'fornavn' => $reg->first_name ?? '',
                    'replay_url' => 'https://www.forfatterskolen.no/gratis-webinar/94/reprise',
                ],
            ]);

            $queued++;
        }

        $this->newLine();
        $this->info("Lagt i kø: {$queued}, Hoppet over: {$skipped}");
        $this->info("Planlagt sending: {$scheduledAt->format('d.m.Y H:i')}");

        return 0;
    }

    private function getNonStudentBody(): string
    {
        return <<<'HTML'
<div style="max-width:600px;margin:0 auto;font-family:Georgia,serif;">
    <h2 style="color:#1a1a1a;font-size:24px;">Hei [fornavn]!</h2>

    <p style="font-size:16px;color:#444;line-height:1.7;">
        Beklager at det var litt tekniske utfordringer med lenken til opptaket tidligere i kveld. Nå er alt i orden!
    </p>

    <div style="background:linear-gradient(135deg,#f8f4f0,#fff);border-radius:12px;padding:24px;margin:24px 0;border-left:4px solid #862736;">
        <p style="font-size:15px;color:#444;margin:0 0 16px;">Her kan du se hele opptaket av <strong>Gratiswebinar med Gro Dahle: Slik skaper du karakterer som lever</strong>:</p>
        <a href="https://www.forfatterskolen.no/gratis-webinar/94/reprise" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;">🎬 Se reprisen gratis →</a>
    </div>

    <div style="background:#fdf8f0;border-radius:12px;padding:28px;margin:28px 0;">
        <h3 style="color:#862736;font-size:20px;margin:0 0 12px;">Nå har vi et konkret spørsmål til deg:</h3>
        <p style="font-size:15px;color:#444;line-height:1.7;">
            Har du en romanidé du ikke helt får tak på — eller et manus du vil løfte til et høyere nivå?
        </p>
        <p style="font-size:15px;color:#444;line-height:1.7;">
            <strong>20. april</strong> sparker vi i gang et nytt <span style="color:#862736;font-weight:600;">10 ukers intensivt romankurs</span>, der du får lære og komme tett på noen av landets mest erfarne forfattere:
        </p>
        <p style="font-size:17px;color:#1a1a1a;font-weight:600;text-align:center;margin:16px 0;">
            Trude Marstein · Gro Dahle · Bjarte Breiteig · Rolf Enger
        </p>
        <p style="font-size:15px;color:#444;line-height:1.7;font-style:italic;">
            Dette er ikke et kurs du bare «tar».<br>
            Det er et kurs du <strong>skriver deg gjennom.</strong>
        </p>

        <div style="margin-top:16px;padding-top:16px;border-top:1px solid #e8ddd0;">
            <p style="font-size:14px;color:#444;margin:0 0 4px;">✍️ Fra idé til ferdig førsteutkast — steg for steg</p>
            <p style="font-size:14px;color:#444;margin:0 0 4px;">📹 Ukentlige webinarer med forfatterne</p>
            <p style="font-size:14px;color:#444;margin:0 0 4px;">📝 Profesjonell tilbakemelding på teksten din</p>
            <p style="font-size:14px;color:#444;margin:0;">🤝 Et skrivemiljø som varer</p>
        </div>

        <div style="text-align:center;margin-top:24px;">
            <a href="https://www.forfatterskolen.no/course/121" style="display:inline-block;background:#862736;color:#fff;padding:14px 32px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;">Les mer og bestill →</a>
            <p style="font-size:13px;color:#888;margin-top:8px;">🏷️ Earlybird-pris til 1. april — spar kr 5 500</p>
        </div>
    </div>

    <p style="font-size:15px;color:#444;line-height:1.7;">
        God skrivekveld!<br><br>
        Vennlig hilsen<br>
        <strong>Forfatterskolen</strong>
    </p>
</div>
HTML;
    }

    private function getStudentBody(): string
    {
        return <<<'HTML'
<div style="max-width:600px;margin:0 auto;font-family:Georgia,serif;">
    <h2 style="color:#1a1a1a;font-size:24px;">Hei [fornavn]!</h2>

    <p style="font-size:16px;color:#444;line-height:1.7;">
        Beklager at det var litt tekniske utfordringer med lenken til opptaket tidligere i kveld. Nå er alt i orden!
    </p>

    <div style="background:linear-gradient(135deg,#f8f4f0,#fff);border-radius:12px;padding:24px;margin:24px 0;border-left:4px solid #862736;">
        <p style="font-size:15px;color:#444;margin:0 0 16px;">Her kan du se hele opptaket av <strong>Gratiswebinar med Gro Dahle: Slik skaper du karakterer som lever</strong>:</p>
        <a href="https://www.forfatterskolen.no/gratis-webinar/94/reprise" style="display:inline-block;background:#862736;color:#fff;padding:14px 28px;text-decoration:none;border-radius:8px;font-size:16px;font-weight:600;">🎬 Se reprisen gratis →</a>
    </div>

    <p style="font-size:15px;color:#444;line-height:1.7;">
        God skrivekveld!<br><br>
        Vennlig hilsen<br>
        <strong>Forfatterskolen</strong>
    </p>
</div>
HTML;
    }
}
