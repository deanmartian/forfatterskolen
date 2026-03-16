<?php

namespace App\Http\Controllers\Editor;

use AdminHelpers;
use App\CoachingSession;
use App\Http\Controllers\Controller;
use App\Jobs\TranscribeCoachingSessionJob;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoachingSessionController extends Controller
{
    public function index(): View
    {
        $sessions = CoachingSession::forEditor(Auth::id())
            ->with(['student', 'manuscript'])
            ->orderByDesc('created_at')
            ->get();

        return view('editor.coaching-sessions.index', compact('sessions'));
    }

    public function show($id): View
    {
        $session = CoachingSession::with(['student', 'manuscript', 'editor'])
            ->findOrFail($id);

        if ($session->editor_id !== Auth::id()) {
            abort(403);
        }

        return view('editor.coaching-sessions.show', compact('session'));
    }

    public function start($id): RedirectResponse
    {
        $session = CoachingSession::findOrFail($id);

        if ($session->editor_id !== Auth::id()) {
            abort(403);
        }

        $session->update([
            'status' => 'active',
            'started_at' => Carbon::now(),
        ]);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Samtalen er startet.'),
            'alert_type' => 'success',
        ]);
    }

    public function end($id): RedirectResponse
    {
        $session = CoachingSession::findOrFail($id);

        if ($session->editor_id !== Auth::id()) {
            abort(403);
        }

        $session->update([
            'status' => 'completed',
            'ended_at' => Carbon::now(),
        ]);

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Samtalen er avsluttet.'),
            'alert_type' => 'success',
        ]);
    }

    public function uploadRecording(Request $request, $id)
    {
        $session = CoachingSession::findOrFail($id);

        if ($session->editor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'recording' => 'required|file|max:512000', // Maks 500MB
        ]);

        $file = $request->file('recording');
        $filename = 'coaching_' . $session->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('coaching-recordings', $filename, 'local');

        $session->update([
            'recording_path' => $path,
        ]);

        // Start transkripsjon i bakgrunnen
        TranscribeCoachingSessionJob::dispatch($session->fresh());

        return response()->json([
            'success' => true,
            'message' => 'Opptak lastet opp. Transkripsjon startet.',
        ]);
    }

    public function studentHistory($studentId): View
    {
        $sessions = CoachingSession::forEditor(Auth::id())
            ->forStudent($studentId)
            ->with(['manuscript'])
            ->orderByDesc('created_at')
            ->get();

        $student = \App\User::findOrFail($studentId);

        return view('editor.coaching-sessions.student-history', compact('sessions', 'student'));
    }
}
