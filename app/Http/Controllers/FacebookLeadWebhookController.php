<?php

namespace App\Http\Controllers;

use App\Services\FacebookAdsService;
use App\Services\BigMarkerService;
use App\User;
use App\FreeWebinar;
use App\Http\AdminHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacebookLeadWebhookController extends Controller
{
    /**
     * Verifisering av webhook (GET) — Facebook sender denne ved oppsett
     */
    public function verify(Request $request)
    {
        $challenge = FacebookAdsService::verifyWebhook($request);

        if ($challenge !== null) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Motta leads fra Facebook Lead Ads (POST)
     */
    public function handle(Request $request)
    {
        Log::info('Facebook Lead Webhook mottatt', ['payload' => $request->all()]);

        $leads = FacebookAdsService::parseLeadWebhook($request->all());

        foreach ($leads as $lead) {
            $this->processLead($lead);
        }

        return response('OK', 200);
    }

    /**
     * Prosesser én lead — opprett bruker, registrer til webinar, legg til i AC-liste
     */
    private function processLead(array $lead): void
    {
        $email = $lead['email'] ?? null;
        if (!$email) return;

        $firstName = $lead['first_name'] ?? '';
        $lastName = $lead['last_name'] ?? '';

        Log::info("Facebook Lead: {$email} ({$firstName} {$lastName})");

        // 1. Opprett eller finn bruker
        $user = User::where('email', $email)->first();

        if (!$user) {
            $defaultPassword = 'Z5C5E5M2jv';
            $user = User::create([
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => bcrypt($defaultPassword),
                'default_password' => $defaultPassword,
                'need_pass_update' => 1,
            ]);

            Log::info("Ny bruker opprettet fra Facebook Lead: {$email}");

            // Send velkomst-e-post
            $this->sendWelcomeEmail($user, $defaultPassword);
        }

        // 2. Legg til i ActiveCampaign-liste (webinar-leads = liste 40)
        try {
            AdminHelpers::addToActiveCampaignList(40, [
                'email' => $email,
                'name' => $firstName,
                'last_name' => $lastName,
            ]);
        } catch (\Exception $e) {
            Log::warning("ActiveCampaign feilet for {$email}: {$e->getMessage()}");
        }

        // 3. Registrer til neste kommende webinar i BigMarker (hvis det finnes)
        $this->registerToUpcomingWebinar($user);
    }

    /**
     * Send velkomst-e-post til ny lead
     */
    private function sendWelcomeEmail(User $user, string $password): void
    {
        try {
            $encodeEmail = encrypt($user->email);
            $emailTemplate = AdminHelpers::emailTemplate('Fb Leads Registration');

            if (!$emailTemplate) {
                Log::warning("E-postmal 'Fb Leads Registration' ikke funnet");
                return;
            }

            $actionUrl = route('auth.login.email', $encodeEmail);

            $message = str_replace(
                [':login', ':end_login', ':firstname', ':lastname', ':password'],
                [
                    "<a href='{$actionUrl}' class='redirect-button' target='_blank'>",
                    '</a>',
                    $user->first_name,
                    $user->last_name,
                    $password,
                ],
                $emailTemplate->email_content
            );

            $to = $user->email;
            $emailData = [
                'email_subject' => $emailTemplate->subject,
                'email_message' => view('emails.fb-leads-registration', compact('message'))->render(),
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
            ];

            AdminHelpers::sendEmail($to, $emailData);
        } catch (\Exception $e) {
            Log::error("Velkomst-e-post feilet for {$user->email}: {$e->getMessage()}");
        }
    }

    /**
     * Registrer bruker til neste kommende gratis webinar i BigMarker
     */
    private function registerToUpcomingWebinar(User $user): void
    {
        try {
            $webinar = FreeWebinar::where('start_date', '>=', now())
                ->orderBy('start_date')
                ->first();

            if (!$webinar || !$webinar->gtwebinar_id) return;

            $bigmarker = app(BigMarkerService::class);
            $bigmarker->registerAttendee($webinar->gtwebinar_id, [
                'email' => $user->email,
                'first_name' => $user->first_name ?? '',
                'last_name' => $user->last_name ?? '',
            ]);

            Log::info("Registrert {$user->email} til BigMarker webinar: {$webinar->title}");
        } catch (\Exception $e) {
            Log::warning("BigMarker registrering feilet for {$user->email}: {$e->getMessage()}");
        }
    }
}
