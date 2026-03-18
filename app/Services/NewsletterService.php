<?php

namespace App\Services;

use App\CoursesTaken;
use App\Jobs\AddMailToQueueJob;
use App\Models\Contact;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    /**
     * Bygg mottakerliste basert på nyhetsbrevets segment.
     */
    public function buildRecipientList(Newsletter $newsletter): Collection
    {
        $query = Contact::subscribed();

        switch ($newsletter->segment) {
            case 'active_course':
                $query->whereNotNull('user_id')
                    ->whereHas('user', function ($q) {
                        $q->whereHas('coursesTaken', fn ($q2) => $q2->where('is_active', 1));
                    });
                break;

            case 'no_active_course':
                $query->where(function ($q) {
                    $q->whereNull('user_id')
                        ->orWhereDoesntHave('user', function ($q2) {
                            $q2->whereHas('coursesTaken', fn ($q3) => $q3->where('is_active', 1));
                        });
                });
                break;

            case 'webinar_registrants':
                $query->withTag('nyhetsbrev');
                break;

            case 'course_17':
                $query->whereNotNull('user_id')
                    ->whereHas('user', function ($q) {
                        $q->whereHas('coursesTaken', fn ($q2) => $q2->where('package_id', 17));
                    });
                break;

            default:
                // 'all' eller tag:xxx
                if (str_starts_with($newsletter->segment, 'tag:')) {
                    $tag = substr($newsletter->segment, 4);
                    $query->withTag($tag);
                }
                // 'all' — ingen ekstra filter
                break;
        }

        return $query->get();
    }

    /**
     * Planlegg et nyhetsbrev for utsending.
     */
    public function schedule(Newsletter $newsletter, Carbon $scheduledAt): void
    {
        $newsletter->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    /**
     * Start utsending av et nyhetsbrev nå.
     */
    public function sendNow(Newsletter $newsletter): void
    {
        $recipients = $this->buildRecipientList($newsletter);

        // Opprett sends for alle mottakere
        foreach ($recipients as $contact) {
            NewsletterSend::create([
                'newsletter_id' => $newsletter->id,
                'contact_id' => $contact->id,
                'email' => $contact->email,
                'status' => 'pending',
                'created_at' => now(),
            ]);
        }

        $newsletter->update([
            'status' => 'sending',
            'total_recipients' => $recipients->count(),
        ]);

        Log::info("Nyhetsbrev #{$newsletter->id} '{$newsletter->subject}' startet — {$recipients->count()} mottakere");
    }

    /**
     * Send en batch av et nyhetsbrev. Returnerer antall sendt.
     */
    public function sendBatch(Newsletter $newsletter, int $batchSize = 500): int
    {
        $sends = $newsletter->pendingSends()
            ->limit($batchSize)
            ->get();

        if ($sends->isEmpty()) {
            // Alle sendt — oppdater status
            $newsletter->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
            Log::info("Nyhetsbrev #{$newsletter->id} fullført — {$newsletter->total_sent} sendt, {$newsletter->total_failed} feilet");

            return 0;
        }

        $sent = 0;

        foreach ($sends as $send) {
            try {
                $unsubscribeUrl = url('/avmeld/' . base64_encode($send->email));

                $bodyWithUnsubscribe = $newsletter->body_html . '
                    <p style="text-align:center;font-size:12px;color:#999;margin-top:30px;">
                        <a href="' . $unsubscribeUrl . '" style="color:#999;">Avmeld nyhetsbrev</a>
                    </p>';

                AddMailToQueueJob::dispatch(
                    $send->email,
                    $newsletter->subject,
                    $bodyWithUnsubscribe,
                    $newsletter->from_address,
                    $newsletter->from_name,
                    null,
                    'newsletter',
                    $newsletter->id,
                    'emails.branded.newsletter'
                );

                $send->markSent();
                $newsletter->incrementSent();
                $sent++;

            } catch (\Exception $e) {
                Log::error("Nyhetsbrev send feilet for {$send->email}: {$e->getMessage()}");
                $send->markFailed();
                $newsletter->incrementFailed();
            }
        }

        return $sent;
    }
}
