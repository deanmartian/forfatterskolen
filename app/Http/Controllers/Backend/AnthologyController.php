<?php

namespace App\Http\Controllers\Backend;

use App\AnthologySubmission;
use App\Http\Controllers\Controller;
use App\Mail\SubjectBodyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AnthologyController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkPageAccess:2');
    }

    public function index(Request $request)
    {
        $query = AnthologySubmission::query()->orderBy('created_at', 'desc');

        // Filtrering
        if ($request->filled('connection')) {
            $query->where('connection', $request->connection);
        }
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(25);

        // Statistikk
        $stats = [
            'total' => AnthologySubmission::count(),
            'elev' => AnthologySubmission::where('connection', 'elev')->count(),
            'tidligere' => AnthologySubmission::where('connection', 'tidligere_elev')->count(),
            'ny' => AnthologySubmission::where('connection', 'ny')->count(),
            'received' => AnthologySubmission::where('status', 'received')->count(),
            'under_review' => AnthologySubmission::where('status', 'under_review')->count(),
            'selected' => AnthologySubmission::where('status', 'selected')->count(),
            'not_selected' => AnthologySubmission::where('status', 'not_selected')->count(),
            'feedback_sent' => AnthologySubmission::where('status', 'feedback_sent')->count(),
        ];

        // Sjanger-statistikk
        $genreStats = AnthologySubmission::selectRaw('genre, count(*) as count')
            ->groupBy('genre')
            ->pluck('count', 'genre');

        return view('backend.anthology.index', compact('submissions', 'stats', 'genreStats'));
    }

    public function updateStatus(Request $request, $id)
    {
        $submission = AnthologySubmission::findOrFail($id);

        $request->validate([
            'status' => 'required|in:received,under_review,selected,not_selected,feedback_sent',
        ]);

        $submission->update([
            'status' => $request->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Status oppdatert til \"{$submission->status_label}\" for \"{$submission->title}\".");
    }

    public function sendFeedback(Request $request, $id)
    {
        $submission = AnthologySubmission::findOrFail($id);

        $request->validate([
            'editor_feedback' => 'required|string|min:10',
        ]);

        $submission->update([
            'editor_feedback' => $request->editor_feedback,
            'status' => 'feedback_sent',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Send e-post med tilbakemelding
        try {
            $emailBody = "Hei {$submission->first_name},<br><br>"
                . "Takk for at du sendte inn \"{$submission->title}\" til Juleantologien 2026.<br><br>"
                . "Her er tilbakemeldingen fra redaktøren:<br><br>"
                . "<div style='border-left: 3px solid #d4a574; padding-left: 15px; margin: 15px 0; color: #333;'>"
                . nl2br(e($request->editor_feedback))
                . "</div><br>"
                . "Vi setter stor pris på bidraget ditt og håper tilbakemeldingen er nyttig.<br><br>"
                . "Varm hilsen,<br>"
                . "Kristine og redaksjonen<br>"
                . "Forfatterskolen";

            Mail::to($submission->email)->send(new SubjectBodyEmail([
                'email_message' => $emailBody,
                'email_subject' => "Tilbakemelding på \"{$submission->title}\" — Juleantologien 2026",
                'from_name' => 'Forfatterskolen',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ]));
        } catch (\Exception $e) {
            Log::error('Anthology feedback email failed: ' . $e->getMessage());
            return back()->with('error', 'Tilbakemelding lagret, men e-post feilet: ' . $e->getMessage());
        }

        return back()->with('success', "Tilbakemelding sendt til {$submission->first_name} {$submission->last_name}.");
    }

    public function download($id)
    {
        $submission = AnthologySubmission::findOrFail($id);

        return Storage::disk('local')->download($submission->file_path, $submission->file_name);
    }

    public function export()
    {
        $submissions = AnthologySubmission::orderBy('created_at', 'desc')->get();

        $csv = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
        $csv .= "ID;Fornavn;Etternavn;E-post;Tilknytning;Kurs;Sjanger;Tittel;Beskrivelse;Filnavn;Ord;Marketing;Status;Tilbakemelding;Dato\n";

        foreach ($submissions as $s) {
            $csv .= implode(';', [
                $s->id,
                '"' . str_replace('"', '""', $s->first_name) . '"',
                '"' . str_replace('"', '""', $s->last_name) . '"',
                $s->email,
                $s->connection_label,
                '"' . str_replace('"', '""', $s->course_name ?? '') . '"',
                $s->genre_label,
                '"' . str_replace('"', '""', $s->title) . '"',
                '"' . str_replace('"', '""', $s->description ?? '') . '"',
                $s->file_name,
                $s->word_count ?? '',
                $s->consent_marketing ? 'Ja' : 'Nei',
                $s->status_label,
                '"' . str_replace('"', '""', $s->editor_feedback ?? '') . '"',
                $s->created_at->format('d.m.Y H:i'),
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="juleantologi-2026-' . date('Y-m-d') . '.csv"');
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:anthology_submissions,id',
            'status' => 'required|in:received,under_review,selected,not_selected',
        ]);

        AnthologySubmission::whereIn('id', $request->ids)->update([
            'status' => $request->status,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', count($request->ids) . ' bidrag oppdatert til "' . $request->status . '".');
    }
}
