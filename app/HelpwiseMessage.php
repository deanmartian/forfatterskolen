<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpwiseMessage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'attachments' => 'array',
        'raw_payload' => 'array',
        'message_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(HelpwiseConversation::class, 'conversation_id');
    }

    public static function createFromWebhook(int $conversationId, array $data): self
    {
        return self::create([
            'helpwise_message_id' => $data['message_id'] ?? $data['id'] ?? null,
            'conversation_id' => $conversationId,
            'direction' => self::detectDirection($data),
            'from_email' => $data['from_email'] ?? $data['from'] ?? $data['sender_email'] ?? null,
            'from_name' => $data['from_name'] ?? $data['sender_name'] ?? $data['sender'] ?? null,
            'to_email' => $data['to_email'] ?? $data['to'] ?? $data['recipient'] ?? null,
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'] ?? $data['html_body'] ?? $data['message'] ?? $data['content'] ?? null,
            'body_plain' => $data['body_plain'] ?? $data['text_body'] ?? $data['plain_text'] ?? strip_tags($data['body'] ?? $data['message'] ?? ''),
            'attachments' => $data['attachments'] ?? null,
            'channel' => $data['channel'] ?? $data['type'] ?? $data['source'] ?? null,
            'raw_payload' => $data,
            'message_at' => isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at']) : now(),
        ]);
    }

    private static function detectDirection(array $data): string
    {
        $type = strtolower($data['type'] ?? $data['direction'] ?? $data['message_type'] ?? '');

        if (in_array($type, ['outbound', 'outgoing', 'reply', 'agent_reply', 'sent'])) {
            return 'outbound';
        }

        if (isset($data['is_reply_from_agent']) && $data['is_reply_from_agent']) {
            return 'outbound';
        }

        return 'inbound';
    }
}
