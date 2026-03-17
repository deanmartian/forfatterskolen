<?php

namespace App\Http\Controllers\Frontend;

use App\AnthologySubmission;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AnthologyController extends Controller
{
    public function show()
    {
        $deadline = Carbon::parse('2026-08-20 23:59:59');
        $submissionCount = AnthologySubmission::count();
        $isOpen = now()->isBefore($deadline);

        return view('frontend.anthology', compact('deadline', 'submissionCount', 'isOpen'));
    }

    public function submit(Request $request)
    {
        $deadline = Carbon::parse('2026-08-20 23:59:59');
        abort_if(now()->isAfter($deadline), 403, 'Fristen er utløpt.');

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'connection' => 'required|in:elev,tidligere_elev,ny',
            'course_name' => 'nullable|string|max:200',
            'title' => 'required|string|max:200',
            'genre' => 'required|in:novelle,krim,barnefortelling,dikt,feelgood,sakprosa',
            'description' => 'nullable|string|max:1000',
            'manuscript' => 'required|file|mimes:docx,pdf|max:10240',
            'consent' => 'accepted',
            'consent_marketing' => 'nullable',
        ]);

        // Sjekk maks 3 bidrag per e-post
        $existingCount = AnthologySubmission::where('email', $request->email)->count();
        if ($existingCount >= 3) {
            return back()->withInput()->withErrors(['manuscript' => 'Du har allerede sendt inn 3 bidrag.']);
        }

        // Lagre fil
        $path = $request->file('manuscript')->store('anthology/2026', 'local');

        $submission = AnthologySubmission::create([
            'user_id' => auth()->id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'connection' => $request->connection,
            'course_name' => $request->course_name,
            'title' => $request->title,
            'genre' => $request->genre,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $request->file('manuscript')->getClientOriginalName(),
            'consent_terms' => true,
            'consent_marketing' => (bool) $request->consent_marketing,
        ]);

        // Send bekreftelse
        try {
            $emailBody = "Hei {$request->first_name},<br><br>"
                . "Tusen takk for bidraget ditt til Juleantologien 2026!<br><br>"
                . "Vi har mottatt teksten din: <strong>\"{$submission->title}\"</strong> ({$submission->genre_label}).<br><br>"
                . "<strong>Hva skjer nå?</strong><br>"
                . "1. Redaksjonen leser alle bidrag i september<br>"
                . "2. Alle får tilbakemelding — uansett utfall<br>"
                . "3. Utvalgte tekster redigeres i oktober<br>"
                . "4. Boken lanseres i november<br><br>"
                . "Har du spørsmål? Svar gjerne på denne e-posten.<br><br>"
                . "Varm hilsen,<br>"
                . "Kristine og redaksjonen<br>"
                . "Forfatterskolen";

            Mail::to($request->email)->send(new SubjectBodyEmail([
                'email_message' => $emailBody,
                'email_subject' => 'Takk for bidraget til Juleantologien 2026!',
                'from_name' => 'Forfatterskolen',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ]));
        } catch (\Exception $e) {
            Log::error('Anthology confirmation email failed: ' . $e->getMessage());
        }

        // Synk til ActiveCampaign
        $this->syncToActiveCampaign($request, $submission);

        // Varsle admin via e-post
        try {
            $adminBody = "Nytt antologi-bidrag mottatt!<br><br>"
                . "<strong>Navn:</strong> {$request->first_name} {$request->last_name}<br>"
                . "<strong>E-post:</strong> {$request->email}<br>"
                . "<strong>Tilknytning:</strong> {$submission->connection_label}<br>"
                . "<strong>Sjanger:</strong> {$submission->genre_label}<br>"
                . "<strong>Tittel:</strong> {$submission->title}<br>"
                . "<strong>Fil:</strong> {$submission->file_name}<br>"
                . ($request->consent_marketing ? "<strong>Marketing-samtykke:</strong> Ja<br>" : "")
                . "<br>Se alle innsendinger: <a href='" . url('/anthology') . "'>Admin → Antologi</a>";

            Mail::to('post@forfatterskolen.no')->send(new SubjectBodyEmail([
                'email_message' => $adminBody,
                'email_subject' => "Nytt antologi-bidrag: \"{$submission->title}\" ({$submission->genre_label})",
                'from_name' => 'Forfatterskolen System',
                'from_email' => 'noreply@forfatterskolen.no',
                'attach_file' => null,
            ]));
        } catch (\Exception $e) {
            Log::error('Anthology admin notification failed: ' . $e->getMessage());
        }

        return redirect('/juleantologi/takk')->with('submission', $submission);
    }

    public function takk()
    {
        $submission = session('submission');

        return view('frontend.anthology-takk', compact('submission'));
    }

    private function syncToActiveCampaign($request, $submission)
    {
        try {
            // Legg til i AC liste 40 (skrivetips/nyhetsbrev) hvis marketing-samtykke
            if ($request->consent_marketing) {
                AdminHelpers::addToActiveCampaignList(40, [
                    'email' => $request->email,
                    'name' => $request->first_name,
                    'last_name' => $request->last_name,
                ]);
            }

            // Legg ALLE antologi-deltagere til liste 40 uansett (for oppfølging)
            // men bare med basisinfo
            AdminHelpers::addToActiveCampaignList(40, [
                'email' => $request->email,
                'name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

        } catch (\Exception $e) {
            Log::error('AC sync failed for anthology: ' . $e->getMessage());
        }
    }
}
