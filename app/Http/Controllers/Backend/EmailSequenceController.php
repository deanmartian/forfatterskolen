<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EmailSequence;
use App\Models\EmailSequenceStep;
use Illuminate\Http\Request;

class EmailSequenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $sequences = EmailSequence::withCount('steps')->get();

        return view('backend.crm.index', [
            'tab' => 'sequences',
            'sequences' => $sequences,
            'totalContacts' => \App\Models\Contact::count(),
            'activeContacts' => \App\Models\Contact::subscribed()->count(),
            'pendingEmails' => \App\Models\EmailAutomationQueue::pending()->count(),
        ]);
    }

    public function show($id)
    {
        $sequence = EmailSequence::with('steps')->findOrFail($id);

        return view('backend.crm.sequence-show', compact('sequence'));
    }

    public function toggleActive($id)
    {
        $sequence = EmailSequence::findOrFail($id);
        $sequence->update(['is_active' => ! $sequence->is_active]);

        return back()->with('success', $sequence->is_active ? 'Sekvens aktivert.' : 'Sekvens deaktivert.');
    }

    public function editStep($id, $stepId)
    {
        $sequence = EmailSequence::findOrFail($id);
        $step = EmailSequenceStep::findOrFail($stepId);

        return view('backend.crm.step-edit', compact('sequence', 'step'));
    }

    public function updateStep($id, $stepId, Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'delay_hours' => 'nullable|integer|min:0',
            'scheduled_date' => 'nullable|date',
            'from_type' => 'required|in:transactional,newsletter',
        ]);

        $step = EmailSequenceStep::findOrFail($stepId);
        $data = $request->only([
            'subject', 'body_html', 'delay_hours', 'send_time',
            'scheduled_date', 'from_type', 'only_without_active_course',
        ]);
        if (!empty($data['scheduled_date'])) {
            $data['delay_hours'] = 0;
        } else {
            $data['scheduled_date'] = null;
        }
        $step->update($data);

        return redirect()->route('admin.crm.sequences.show', $id)->with('success', 'Steg oppdatert.');
    }

    public function createStep($id, Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'delay_hours' => 'nullable|integer|min:0',
            'scheduled_date' => 'nullable|date',
            'from_type' => 'required|in:transactional,newsletter',
        ]);

        $sequence = EmailSequence::findOrFail($id);
        $maxStep = $sequence->steps()->max('step_number') ?? 0;

        $data = $request->only(['subject', 'body_html', 'delay_hours', 'send_time', 'scheduled_date', 'from_type', 'only_without_active_course']);
        if (!empty($data['scheduled_date'])) {
            $data['delay_hours'] = 0;
        } else {
            $data['scheduled_date'] = null;
        }

        EmailSequenceStep::create(array_merge(
            $data,
            [
                'sequence_id' => $sequence->id,
                'step_number' => $maxStep + 1,
            ]
        ));

        return redirect()->route('admin.crm.sequences.show', $id)->with('success', 'Nytt steg opprettet.');
    }

    public function deleteStep($id, $stepId)
    {
        EmailSequenceStep::findOrFail($stepId)->delete();

        return redirect()->route('admin.crm.sequences.show', $id)->with('success', 'Steg slettet.');
    }
}
