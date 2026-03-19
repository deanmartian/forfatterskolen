<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\HelpwiseService;
use App\HelpwiseWebhookLog;
use Illuminate\Http\Request;

class HelpwiseController extends Controller
{
    public function __construct(
        private readonly HelpwiseService $helpwiseService,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['email', 'inbox', 'status', 'assigned_to', 'search', 'user_id']);
        $conversations = $this->helpwiseService->searchConversations($filters);
        $inboxes = $this->helpwiseService->getInboxes();
        $stats = $this->helpwiseService->getStats();

        return view('backend.helpwise.index', compact('conversations', 'filters', 'inboxes', 'stats'));
    }

    public function show(int $id)
    {
        $conversation = $this->helpwiseService->getConversationWithMessages($id);

        return view('backend.helpwise.show', compact('conversation'));
    }

    public function studentProfile(int $userId)
    {
        $profile = $this->helpwiseService->getStudentProfile($userId);

        return view('backend.helpwise.student-profile', compact('profile'));
    }

    public function linkUsers()
    {
        $linked = $this->helpwiseService->linkConversationsToUsers();

        return redirect()->route('admin.helpwise.index')
            ->with('alert_type', 'success')
            ->with('message', "{$linked} samtaler koblet til elever.");
    }

    public function webhookLogs(Request $request)
    {
        $logs = HelpwiseWebhookLog::orderByDesc('created_at')
            ->paginate(50);

        return view('backend.helpwise.webhook-logs', compact('logs'));
    }
}
