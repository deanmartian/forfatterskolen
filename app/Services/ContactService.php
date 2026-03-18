<?php

namespace App\Services;

use App\CoursesTaken;
use App\Models\Contact;
use App\Models\ContactTag;
use App\Models\EmailAutomationExclusion;
use App\Models\EmailAutomationQueue;
use App\User;
use Carbon\Carbon;

class ContactService
{
    /**
     * Finn eller opprett kontakt basert på e-post.
     * Kobler automatisk til User hvis en finnes med samme e-post.
     */
    public function findOrCreateByEmail(string $email, array $data = []): Contact
    {
        $email = strtolower(trim($email));

        $contact = Contact::where('email', $email)->first();

        if ($contact) {
            // Oppdater eksisterende kontakt med ny data (ikke overskriv med null)
            $updateData = array_filter($data, fn ($v) => $v !== null && $v !== '');
            if (! empty($updateData)) {
                $contact->update($updateData);
            }

            return $contact->fresh();
        }

        // Opprett ny kontakt
        $user = User::where('email', $email)->first();

        return Contact::create(array_merge([
            'email' => $email,
            'first_name' => $data['first_name'] ?? $user?->first_name,
            'last_name' => $data['last_name'] ?? $user?->last_name,
            'phone' => $data['phone'] ?? null,
            'user_id' => $user?->id,
            'source' => $data['source'] ?? 'manual',
            'status' => $data['status'] ?? 'active',
        ], $data));
    }

    /**
     * Legg til tag på kontakt (ignorerer duplikater)
     */
    public function tagContact(Contact $contact, string $tag): void
    {
        ContactTag::firstOrCreate([
            'contact_id' => $contact->id,
            'tag' => $tag,
        ], [
            'created_at' => now(),
        ]);
    }

    /**
     * Fjern tag fra kontakt
     */
    public function removeTag(Contact $contact, string $tag): void
    {
        $contact->tags()->where('tag', $tag)->delete();
    }

    /**
     * Meld av kontakt — setter status og kansellerer alle ventende e-poster
     */
    public function unsubscribe(Contact $contact): void
    {
        $contact->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        // Kanseller alle ventende automatiserte e-poster
        EmailAutomationQueue::where('contact_id', $contact->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'cancelled',
                'cancelled_reason' => 'unsubscribed',
            ]);
    }

    /**
     * Marker kontakt som bouncet
     */
    public function markBounced(Contact $contact): void
    {
        $contact->update([
            'status' => 'bounced',
            'bounced_at' => now(),
        ]);

        EmailAutomationQueue::where('contact_id', $contact->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'cancelled',
                'cancelled_reason' => 'bounced',
            ]);
    }

    /**
     * Synkroniser en User til contacts-tabellen
     */
    public function syncUserToContact(User $user): Contact
    {
        return $this->findOrCreateByEmail($user->email, [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'user_id' => $user->id,
            'source' => 'existing_user',
        ]);
    }

    /**
     * Sjekk om kontakten er ekskludert fra salgsmail
     */
    public function isExcludedFromSalesEmails(Contact $contact): bool
    {
        return $contact->isExcludedFromSales();
    }

    /**
     * Oppdater ekskluderinger basert på nåværende kurstilgang
     */
    public function refreshExclusions(Contact $contact): void
    {
        if (! $contact->user_id) {
            return;
        }

        // Fjern gamle kurs-baserte ekskluderinger (ikke manuelle)
        EmailAutomationExclusion::where('contact_id', $contact->id)
            ->whereIn('reason', ['active_course', 'course_17'])
            ->delete();

        // Sjekk kurs 17 (mentormøter) — permanent ekskludering
        $hasCourse17 = CoursesTaken::where('user_id', $contact->user_id)
            ->where('package_id', 17)
            ->exists();

        if ($hasCourse17) {
            EmailAutomationExclusion::create([
                'contact_id' => $contact->id,
                'user_id' => $contact->user_id,
                'reason' => 'course_17',
                'course_id' => 17,
                'created_at' => now(),
            ]);
        }

        // Sjekk aktive kurs (ikke kurs 17)
        $activeCourses = CoursesTaken::where('user_id', $contact->user_id)
            ->where('is_active', 1)
            ->where('package_id', '!=', 17)
            ->get();

        foreach ($activeCourses as $ct) {
            EmailAutomationExclusion::create([
                'contact_id' => $contact->id,
                'user_id' => $contact->user_id,
                'reason' => 'active_course',
                'course_id' => $ct->package_id,
                'created_at' => now(),
            ]);
        }
    }
}
