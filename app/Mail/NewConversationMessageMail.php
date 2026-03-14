<?php

namespace App\Mail;

use App\Conversation;
use App\ConversationMessage;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewConversationMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $conversation;
    public $message;
    public $senderName;
    public $recipientName;
    public $messagePreview;
    public $conversationUrl;

    public function __construct(Conversation $conversation, ConversationMessage $message, User $recipient, string $conversationUrl)
    {
        $this->conversation = $conversation;
        $this->message = $message;
        $this->senderName = $message->sender->full_name;
        $this->recipientName = $recipient->full_name;
        $this->messagePreview = \Illuminate\Support\Str::limit(strip_tags($message->body), 200);
        $this->conversationUrl = $conversationUrl;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address', 'support@forfatterskolen.no');
        $fromName = config('mail.from.name', 'Forfatterskolen');

        return $this->from($fromAddress, $fromName)
            ->subject('Ny melding: ' . $this->conversation->subject)
            ->view('emails.new-conversation-message');
    }
}
