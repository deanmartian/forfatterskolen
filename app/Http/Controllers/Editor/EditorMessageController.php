<?php

namespace App\Http\Controllers\Editor;

use App\Conversation;
use App\ConversationMessage;
use App\ConversationParticipant;
use App\Http\Controllers\Controller;
use App\Mail\NewConversationMessageMail;
use App\User;
use App\UserPreferredEditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EditorMessageController extends Controller
{
    private const CONTACTABLE_ADMIN_IDS = [5749, 1376, 1064]; // Annina, Sven Inge, Kristine

    public function index(): View
    {
        $conversations = Conversation::whereHas('participants', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->with(['latestMessage', 'participants', 'creator'])
            ->orderByDesc(
                ConversationMessage::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->orderByDesc('created_at')
                    ->limit(1)
            )
            ->get();

        return view('editor.messages.index', compact('conversations'));
    }

    public function show($id): View
    {
        $conversation = Conversation::with(['messages.sender', 'participants'])
            ->findOrFail($id);

        // Verify user is a participant
        $participant = ConversationParticipant::where('conversation_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Mark as read
        $participant->update(['last_read_at' => now()]);

        return view('editor.messages.show', compact('conversation'));
    }

    public function create(): View
    {
        // Get learners assigned to this editor
        $learnerIds = UserPreferredEditor::where('editor_id', Auth::id())
            ->pluck('user_id');

        $learners = User::whereIn('id', $learnerIds)
            ->where('role', User::LearnerRole)
            ->where('is_active', 1)
            ->orderBy('first_name')
            ->get();

        // Also allow messaging other active editors
        $editors = User::where('role', User::EditorRole)
            ->where('id', '!=', Auth::id())
            ->where('is_active', 1)
            ->orderBy('first_name')
            ->get();

        // Admin contacts
        $admins = User::whereIn('id', self::CONTACTABLE_ADMIN_IDS)
            ->where('is_active', 1)
            ->where('id', '!=', Auth::id())
            ->orderBy('first_name')
            ->get();

        return view('editor.messages.create', compact('learners', 'editors', 'admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'recipient_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        $conversation = Conversation::create([
            'subject' => $request->subject,
            'created_by' => Auth::id(),
        ]);

        // Add participants
        $conversation->participants()->attach([
            Auth::id() => ['last_read_at' => now(), 'created_at' => now()],
            $request->recipient_id => ['last_read_at' => null, 'created_at' => now()],
        ]);

        // Add the first message
        $message = ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        // Send email notification to recipient if they are an editor
        $this->notifyRecipient($conversation, $message, $request->recipient_id);

        return redirect()->route('editor.messages.show', $conversation->id)
            ->with('success', 'Samtale opprettet.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($id);

        // Verify user is a participant
        ConversationParticipant::where('conversation_id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $message = ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        // Update read timestamp for sender
        ConversationParticipant::where('conversation_id', $id)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);

        // Notify other participants (editors and admins)
        $otherParticipantIds = $conversation->participants()
            ->where('users.id', '!=', Auth::id())
            ->pluck('users.id');

        foreach ($otherParticipantIds as $participantId) {
            $this->notifyRecipient($conversation, $message, $participantId);
        }

        return redirect()->route('editor.messages.show', $conversation->id);
    }

    public function broadcastCreate(): View
    {
        return view('editor.messages.broadcast');
    }

    public function broadcastStore(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $conversation = Conversation::create([
            'subject' => $request->subject,
            'created_by' => Auth::id(),
            'is_broadcast' => true,
        ]);

        // Add all active editors as participants
        $editors = User::where('role', User::EditorRole)->where('is_active', 1)->get();
        $participantData = [];
        foreach ($editors as $editor) {
            $participantData[$editor->id] = [
                'last_read_at' => $editor->id == Auth::id() ? now() : null,
                'created_at' => now(),
            ];
        }
        // Also add the admin sender if not already an editor
        if (!isset($participantData[Auth::id()])) {
            $participantData[Auth::id()] = ['last_read_at' => now(), 'created_at' => now()];
        }
        $conversation->participants()->attach($participantData);

        $message = ConversationMessage::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        // Send email notification to all editors except sender
        foreach ($editors as $editor) {
            if ($editor->id != Auth::id()) {
                $this->notifyRecipient($conversation, $message, $editor->id);
            }
        }

        return redirect()->route('editor.messages.index')
            ->with('success', 'Broadcast sendt til alle redaktorer.');
    }

    private function notifyRecipient(Conversation $conversation, ConversationMessage $message, int $recipientId): void
    {
        $recipient = User::find($recipientId);
        if (!$recipient || !$recipient->email) {
            return;
        }

        // Send email to editors and contactable admins
        if ($recipient->role == User::EditorRole) {
            $url = route('editor.messages.show', $conversation->id);
        } elseif (in_array($recipient->id, self::CONTACTABLE_ADMIN_IDS)) {
            $url = route('admin.messages.show', $conversation->id);
        } else {
            return;
        }

        try {
            Mail::to($recipient->email)->send(
                new NewConversationMessageMail($conversation, $message, $recipient, $url)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send conversation notification: ' . $e->getMessage());
        }
    }
}
