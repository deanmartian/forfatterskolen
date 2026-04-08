<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AiKnownIssue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiKnowledgeController extends Controller
{
    public function index(): View
    {
        $issues = AiKnownIssue::orderByRaw("FIELD(status, 'active', 'resolved')")
            ->orderByRaw("FIELD(severity, 'high', 'medium', 'low', 'info')")
            ->orderByDesc('created_at')
            ->get();

        return view('backend.ai-knowledge.index', compact('issues'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'workaround' => 'nullable|string',
            'severity' => 'required|in:info,low,medium,high',
            'category' => 'nullable|string|max:100',
            'discovered_at' => 'nullable|date',
        ]);

        $data['status'] = 'active';
        $data['discovered_at'] = $data['discovered_at'] ?? now()->toDateString();
        $data['created_by'] = auth()->id();

        AiKnownIssue::create($data);

        return redirect()->route('admin.ai-knowledge.index')
            ->with('success', 'Lagt til i AI-kunnskapsbasen.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $issue = AiKnownIssue::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'workaround' => 'nullable|string',
            'severity' => 'required|in:info,low,medium,high',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,resolved',
            'discovered_at' => 'nullable|date',
        ]);

        if ($data['status'] === 'resolved' && !$issue->resolved_at) {
            $data['resolved_at'] = now()->toDateString();
        } elseif ($data['status'] === 'active') {
            $data['resolved_at'] = null;
        }

        $issue->update($data);

        return redirect()->route('admin.ai-knowledge.index')
            ->with('success', 'Oppdatert.');
    }

    public function destroy(int $id): RedirectResponse
    {
        AiKnownIssue::findOrFail($id)->delete();

        return redirect()->route('admin.ai-knowledge.index')
            ->with('success', 'Slettet.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $issue = AiKnownIssue::findOrFail($id);

        if ($issue->status === 'active') {
            $issue->update([
                'status' => 'resolved',
                'resolved_at' => now()->toDateString(),
            ]);
        } else {
            $issue->update([
                'status' => 'active',
                'resolved_at' => null,
            ]);
        }

        return back()->with('success', 'Status oppdatert.');
    }
}
