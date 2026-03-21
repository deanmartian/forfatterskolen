<?php

namespace App\Services;

use App\Jobs\AddMailToQueueJob;
use App\Models\Contact;
use App\Models\EmailAutomationQueue;
use App\Models\EmailSequence;
use App\Models\EmailSequenceStep;
use App\Repositories\Services\SaleService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmailAutomationService
{
    public function __construct(
        protected ContactService $contactService,
        protected SaleService $saleService,
    ) {}

    /**
     * Start en e-postsekvens for en kontakt.
     *
     * @param  array  $metadata  Ekstra data (webinar_id, webinar_start_date, etc.)
     */
    public function startSequence(Contact $contact, string $triggerEvent, array $metadata = []): void
    {
        $sequence = EmailSequence::where('trigger_event', $triggerEvent)
            ->where('is_active', true)
            ->first();

        if (! $sequence) {
            Log::info("Ingen aktiv sekvens funnet for trigger: {$triggerEvent}");
            return;
        }

        // Sjekk om kontakten allerede er i denne sekvensen
        $alreadyInSequence = EmailAutomationQueue::where('contact_id', $contact->id)
            ->where('sequence_id', $sequence->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyInSequence) {
            Log::info("Kontakt {$contact->id} er allerede i sekvens {$sequence->id}");
            return;
        }

        $steps = $sequence->steps;

        foreach ($steps as $step) {
            $scheduledAt = $this->calculateScheduledAt($step, $metadata);

            EmailAutomationQueue::create([
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'sequence_id' => $sequence->id,
                'step_id' => $step->id,
                'scheduled_at' => $scheduledAt,
                'status' => 'pending',
                'metadata' => $metadata,
            ]);
        }

        Log::info("Sekvens '{$sequence->name}' startet for kontakt {$contact->id} ({$contact->email}) — {$steps->count()} steg planlagt");
    }

    /**
     * Kanseller alle ventende e-poster i en sekvens for en kontakt.
     */
    public function cancelSequence(Contact $contact, int $sequenceId, string $reason = 'manual'): void
    {
        EmailAutomationQueue::where('contact_id', $contact->id)
            ->where('sequence_id', $sequenceId)
            ->where('status', 'pending')
            ->update([
                'status' => 'cancelled',
                'cancelled_reason' => $reason,
            ]);
    }

    /**
     * Prosesser køen — send e-poster som er klare.
     * Returnerer antall prosesserte elementer.
     */
    public function processQueue(int $limit = 500): int
    {
        $items = EmailAutomationQueue::dueNow()
            ->with(['contact', 'step', 'sequence'])
            ->limit($limit)
            ->get();

        $processed = 0;

        foreach ($items as $item) {
            try {
                $skipReason = $this->shouldSkipStep($item);

                if ($skipReason) {
                    $item->cancel($skipReason);
                    $processed++;
                    continue;
                }

                $this->sendAutomationEmail($item);
                $item->markSent();
                $processed++;

            } catch (\Exception $e) {
                Log::error("Automatisering feilet for kø-element {$item->id}: {$e->getMessage()}");
                $item->markFailed();
                $processed++;
            }
        }

        if ($processed > 0) {
            Log::info("E-postautomatisering: {$processed} elementer prosessert");
        }

        return $processed;
    }

    /**
     * Sjekk om et steg skal hoppes over.
     * Returnerer null hvis det skal sendes, ellers en grunn.
     */
    public function shouldSkipStep(EmailAutomationQueue $item): ?string
    {
        $contact = $item->contact;

        if (! $contact) {
            return 'contact_not_found';
        }

        // Sjekk om kontakten er avmeldt eller bouncet
        if ($contact->status === 'unsubscribed') {
            return 'unsubscribed';
        }

        if ($contact->status === 'bounced') {
            return 'bounced';
        }

        $step = $item->step;

        if (! $step) {
            return 'step_not_found';
        }

        // Sjekk om steget kun er for folk uten aktivt kurs
        if ($step->only_without_active_course && $contact->hasActiveCourse()) {
            return 'has_active_course';
        }

        // Sjekk kurs 17 (mentormøter) — aldri salgsmail
        if ($step->only_without_active_course && $contact->hasCourse17()) {
            return 'course_17_permanent_exclusion';
        }

        // Sjekk manuelle ekskluderinger
        if ($step->only_without_active_course && $contact->exclusions()->exists()) {
            return 'manual_exclusion';
        }

        return null;
    }

    /**
     * Send en automatisert e-post via AddMailToQueueJob
     */
    protected function sendAutomationEmail(EmailAutomationQueue $item): void
    {
        $step = $item->step;
        $contact = $item->contact;
        $metadata = $item->metadata ?? [];

        // Erstatt variabler i emne og body
        $subject = $this->replaceVariables($step->subject, $contact, $metadata);
        $body = $this->replaceVariables($step->body_html, $contact, $metadata);

        $fromEmail = $step->getFromAddress();
        $fromName = $step->getFromName();

        // Generer avmeldingslenke
        $unsubscribeUrl = url('/avmeld/' . base64_encode($contact->email));

        // Legg til avmeldingslenke i body
        $body .= '<p style="text-align:center;font-size:12px;color:#999;margin-top:30px;">
            <a href="' . $unsubscribeUrl . '" style="color:#999;">Avmeld nyhetsbrev</a>
        </p>';

        // Bruk eksisterende AddMailToQueueJob for å sende
        AddMailToQueueJob::dispatch(
            $contact->email,
            $subject,
            $body,
            $fromEmail,
            $fromName,
            null, // attachment
            'automation', // parent
            $item->id, // parent_id
            'emails.branded.automation-email' // email view
        );
    }

    /**
     * Beregn planlagt tidspunkt for et steg.
     */
    protected function calculateScheduledAt(EmailSequenceStep $step, array $metadata = []): Carbon
    {
        $now = now();

        // Spesialhåndtering for webinar-relaterte steg
        if (isset($metadata['webinar_start_date'])) {
            $webinarStart = Carbon::parse($metadata['webinar_start_date']);

            // Steg 2: Dagen før kl 18:00
            if ($step->step_number === 2) {
                return $webinarStart->copy()->subDay()->setTime(18, 0);
            }

            // Steg 3: 1 time før start
            if ($step->step_number === 3) {
                return $webinarStart->copy()->subHour();
            }

            // Steg 4: 2 timer etter start (reprise)
            if ($step->step_number === 4) {
                return $webinarStart->copy()->addHours(2);
            }
        }

        // Standard: bruk delay_hours
        if ($step->delay_hours === 0) {
            return $now;
        }

        $scheduledAt = $now->copy()->addHours($step->delay_hours);

        // Hvis send_time er satt, juster til det tidspunktet
        if ($step->send_time) {
            $time = Carbon::parse($step->send_time);
            $scheduledAt->setTime($time->hour, $time->minute);

            // Hvis tidspunktet allerede har passert i dag, send neste dag
            if ($scheduledAt->isPast()) {
                $scheduledAt->addDay();
            }
        }

        return $scheduledAt;
    }

    /**
     * Erstatt variabler i tekst med kontaktdata
     */
    protected function replaceVariables(string $text, Contact $contact, array $metadata = []): string
    {
        $replacements = [
            '[fornavn]' => $contact->first_name ?? '',
            '[etternavn]' => $contact->last_name ?? '',
            '[epost]' => $contact->email,
            '[webinar_tittel]' => $metadata['webinar_title'] ?? '',
            '[webinar_dato]' => $metadata['webinar_date'] ?? '',
            '[webinar_tid]' => $metadata['webinar_time'] ?? '',
            '[join_lenke]' => $metadata['join_url'] ?? '',
            '[reprise_url]' => $metadata['replay_url'] ?? (isset($metadata['webinar_id']) ? url('/gratis-webinar/' . $metadata['webinar_id'] . '/reprise') : ''),
            '[kurs_tilbud_url]' => $metadata['course_offer_url'] ?? url('/'),
            '[avmeld_url]' => url('/avmeld/' . base64_encode($contact->email)),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
