<?php

namespace App\Http\Controllers\Backend;

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

class AdminMessageController extends Controller
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

        return view('backend.messages.index', compact('conversations'));
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

        return view('backend.messages.show', compact('conversation'));
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

        // Notify other participants who are editors
        $editorParticipantIds = $conversation->participants()
            ->where('users.id', '!=', Auth::id())
            ->where('users.role', User::EditorRole)
            ->pluck('users.id');

        foreach ($editorParticipantIds as $editorId) {
            $this->notifyRecipient($conversation, $message, $editorId);
        }

        return redirect()->route('admin.messages.show', $conversation->id);
    }

    private function notifyRecipient(Conversation $conversation, ConversationMessage $message, int $recipientId): void
    {
        $recipient = User::find($recipientId);
        if (!$recipient || !$recipient->email || $recipient->role != User::EditorRole) {
            return;
        }

        $url = route('editor.messages.show', $conversation->id);

        try {
            Mail::to($recipient->email)->send(
                new NewConversationMessageMail($conversation, $message, $recipient, $url)
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send conversation notification: ' . $e->getMessage());
        }
    }
}
