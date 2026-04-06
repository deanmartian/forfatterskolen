<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\InboxService;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function __construct(
        private readonly InboxService $inboxService,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'assigned_to', 'inbox', 'category', 'search', 'starred', 'sent', 'follow_up']);
        $conversations = $this->inboxService->getConversations($filters);
        $stats = $this->inboxService->getStats();
        $teamMembers = $this->inboxService->getTeamMembers();
        $inboxes = $this->inboxService->getInboxes();

        return view('backend.inbox.index', compact('conversations', 'filters', 'stats', 'teamMembers', 'inboxes'));
    }

    public function show(int $id)
    {
        $conversation = $this->inboxService->getConversation($id);
        $timeline = $conversation->timeline();
        $teamMembers = $this->inboxService->getTeamMembers();
        $cannedResponses = $this->inboxService->getCannedResponses();
        $studentContext = $this->getStudentContext($conversation);

        return view('backend.inbox.show', compact('conversation', 'timeline', 'teamMembers', 'cannedResponses', 'studentContext'));
    }

    public function reply(Request $request, int $id)
    {
        $request->validate(['body' => 'required|string']);
        $isDraft = $request->boolean('save_as_draft', false);
        $sendAndClose = $request->boolean('send_and_close', false);

        $this->inboxService->sendReply($id, $request->input('body'), auth()->id(), $isDraft);

        if ($sendAndClose && !$isDraft) {
            $this->inboxService->updateStatus($id, 'closed');
            return redirect()->route('admin.inbox.index')
                ->with('alert_type', 'success')
                ->with('message', 'Svar sendt og samtale lukket!');
        }

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', $isDraft ? 'info' : 'success')
            ->with('message', $isDraft ? 'Utkast lagret' : 'Svar sendt!');
    }

    public function comment(Request $request, int $id)
    {
        $request->validate(['body' => 'required|string']);

        $mentionedIds = $request->input('mentioned_user_ids', []);
        $this->inboxService->addComment($id, auth()->id(), $request->input('body'), $mentionedIds);

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', 'Kommentar lagt til');
    }

    public function assign(Request $request, int $id)
    {
        $request->validate(['assigned_to' => 'required|integer']);

        $this->inboxService->assignConversation($id, $request->input('assigned_to'), auth()->id(), $request->input('note'));

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', 'Samtale tildelt');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:open,pending,closed,snoozed']);

        $this->inboxService->updateStatus($id, $request->input('status'));

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', 'Status oppdatert');
    }

    public function toggleStar(int $id)
    {
        $this->inboxService->toggleStar($id);
        return redirect()->back();
    }

    public function markSpam(int $id)
    {
        $this->inboxService->markAsSpam($id);
        return redirect()->route('admin.inbox.index')
            ->with('alert_type', 'success')
            ->with('message', 'Markert som spam');
    }

    public function compose(Request $request)
    {
        $request->validate(['to' => 'required|email', 'subject' => 'required', 'body' => 'required']);
        $isDraft = $request->boolean('save_draft', false);

        // Create conversation
        $conversation = \App\Models\Inbox\InboxConversation::create([
            'subject' => $request->input('subject'),
            'customer_email' => $request->input('to'),
            'customer_name' => $request->input('to'),
            'status' => $isDraft ? 'pending' : 'closed',
            'source' => 'compose',
            'inbox' => 'post@forfatterskolen.no',
        ]);

        // Try to link user
        $user = \App\User::where('email', $request->input('to'))->first();
        if ($user) {
            $conversation->update(['user_id' => $user->id, 'customer_name' => $user->full_name]);
        }

        // Create message
        $message = \App\Models\Inbox\InboxMessage::create([
            'conversation_id' => $conversation->id,
            'type' => 'reply',
            'direction' => 'outbound',
            'from_email' => 'post@forfatterskolen.no',
            'from_name' => auth()->user()->full_name . ' — Forfatterskolen',
            'to_email' => $request->input('to'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'body_plain' => $request->input('body'),
            'body_html' => nl2br(e($request->input('body'))),
            'sent_by_user_id' => auth()->id(),
            'is_draft' => $isDraft,
            'sent_at' => $isDraft ? null : now(),
        ]);

        if (!$isDraft) {
            $htmlBody = nl2br(e($request->input('body')));
            dispatch(new \App\Jobs\AddMailToQueueJob(
                $request->input('to'),
                $request->input('subject'),
                $htmlBody,
                'post@forfatterskolen.no',
                auth()->user()->full_name . ' — Forfatterskolen',
                null, 'inbox-compose', $conversation->id
            ));
        }

        return redirect()->route('admin.inbox.show', $conversation->id)
            ->with('alert_type', 'success')
            ->with('message', $isDraft ? 'Utkast lagret' : 'E-post sendt!');
    }

    public function setFollowUp(Request $request, int $id)
    {
        $conversation = \App\Models\Inbox\InboxConversation::findOrFail($id);
        $conversation->follow_up_at = $request->input('follow_up_at') ?: null;
        if ($conversation->follow_up_at && $conversation->status === 'closed') {
            $conversation->status = 'pending';
        }
        $conversation->save();

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', $conversation->follow_up_at ? 'Oppfølging satt!' : 'Oppfølging fjernet');
    }

    public function generateAiDraft(int $id)
    {
        $draft = $this->inboxService->generateAiDraft($id);

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', $draft ? 'success' : 'error')
            ->with('message', $draft ? 'AI-utkast generert!' : 'Kunne ikke generere utkast');
    }

    public function importFromHelpwise()
    {
        $imported = $this->inboxService->importFromHelpwise();

        return redirect()->route('admin.inbox.index')
            ->with('alert_type', 'success')
            ->with('message', "{$imported} samtaler importert fra Helpwise");
    }

    public function cannedResponses()
    {
        $responses = $this->inboxService->getCannedResponses();
        return view('backend.inbox.canned-responses', compact('responses'));
    }

    public function storeCannedResponse(Request $request)
    {
        $request->validate(['title' => 'required|string', 'body' => 'required|string']);

        \App\Models\Inbox\InboxCannedResponse::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->route('admin.inbox.canned-responses')
            ->with('alert_type', 'success')
            ->with('message', 'Hurtigsvar opprettet');
    }

    private function getStudentContext($conversation): array
    {
        if (!$conversation->user_id) return [];

        $user = $conversation->customer;
        if (!$user) return [];

        $context = [
            'Navn' => $user->first_name . ' ' . $user->last_name,
            'E-post' => $user->email,
            'Rolle' => match ($user->role) { 1 => 'Admin', 2 => 'Elev', 3 => 'Redaktør', default => 'Ukjent' },
        ];

        try {
            $courses = $user->coursesTaken()->where('is_active', 1)->with('package.course')->get();
            if ($courses->isNotEmpty()) {
                $context['Aktive kurs'] = $courses->map(fn($ct) => $ct->package?->course?->title ?? 'Ukjent')->implode(', ');
            }
        } catch (\Exception $e) {}

        return $context;
    }
}
