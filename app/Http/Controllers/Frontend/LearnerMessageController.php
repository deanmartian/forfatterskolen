<?php

namespace App\Http\Controllers\Frontend;

use App\Conversation;
use App\ConversationMessage;
use App\ConversationParticipant;
use App\Http\Controllers\Controller;
use App\Mail\NewConversationMessageMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class LearnerMessageController extends Controller
{
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

        return view('frontend.learner.messages.index', compact('conversations'));
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

        return view('frontend.learner.messages.show', compact('conversation'));
    }

    // Admin user IDs that learners can contact
    private const CONTACTABLE_ADMIN_IDS = [5749, 1376, 1064]; // Annina, Sven Inge, Kristine

    public function create(): View
    {
        // Get editors assigned to this learner
        $preferredEditor = Auth::user()->preferredEditor;
        $editors = collect();

        if ($preferredEditor) {
            $editor = User::where('id', $preferredEditor->editor_id)
                ->where('role', User::EditorRole)
                ->where('is_active', 1)
                ->first();
            if ($editor) {
                $editors = collect([$editor]);
            }
        }

        // Admin contacts available to all learners
        $admins = User::whereIn('id', self::CONTACTABLE_ADMIN_IDS)
            ->where('is_active', 1)
            ->orderBy('first_name')
            ->get();

        return view('frontend.learner.messages.create', compact('editors', 'admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'recipient_id' => 'required|exists:users,id',
            'body' => 'required|string',
        ]);

        // Verify recipient is allowed (assigned editor or contactable admin)
        $recipientId = (int) $request->recipient_id;
        $allowedIds = collect(self::CONTACTABLE_ADMIN_IDS);
        $preferredEditor = Auth::user()->preferredEditor;
        if ($preferredEditor) {
            $allowedIds->push($preferredEditor->editor_id);
        }
        if (!$allowedIds->contains($recipientId)) {
            abort(403, 'Du kan ikke sende melding til denne mottakeren.');
        }

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

        // Send email notification to recipient
        $this->notifyRecipient($conversation, $message, $request->recipient_id);

        return redirect()->route('learner.messages.show', $conversation->id)
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

        return redirect()->route('learner.messages.show', $conversation->id);
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
