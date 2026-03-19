<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HelpwiseConversation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tags' => 'array',
        'raw_payload' => 'array',
        'helpwise_created_at' => 'datetime',
        'helpwise_closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(HelpwiseMessage::class, 'conversation_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(HelpwiseMessage::class, 'conversation_id')->latest('message_at');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function findOrCreateFromWebhook(array $data): self
    {
        $helpwiseId = $data['id'] ?? $data['conversation_id'] ?? $data['conversationId'] ?? null;

        if (!$helpwiseId) {
            $helpwiseId = 'unknown_' . now()->timestamp . '_' . rand(1000, 9999);
        }

        $email = self::extractEmail($data);
        $user = $email ? User::where('email', $email)->first() : null;

        return self::updateOrCreate(
            ['helpwise_id' => (string) $helpwiseId],
            [
                'inbox' => $data['inbox'] ?? $data['mailbox'] ?? $data['inbox_name'] ?? null,
                'inbox_id' => $data['inbox_id'] ?? $data['mailbox_id'] ?? null,
                'subject' => $data['subject'] ?? null,
                'customer_email' => $email,
                'customer_name' => self::extractName($data),
                'user_id' => $user?->id,
                'status' => self::mapStatus($data['status'] ?? $data['state'] ?? 'open'),
                'assigned_to' => $data['assigned_to'] ?? $data['assignee'] ?? $data['assignee_name'] ?? null,
                'tags' => $data['tags'] ?? $data['labels'] ?? null,
                'raw_payload' => $data,
                'helpwise_created_at' => isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at']) : now(),
            ]
        );
    }

    private static function extractEmail(array $data): ?string
    {
        return $data['customer_email']
            ?? $data['email']
            ?? $data['from_email']
            ?? $data['contact_email']
            ?? $data['customer']['email'] ?? null
            ?? $data['contact']['email'] ?? null;
    }

    private static function extractName(array $data): ?string
    {
        return $data['customer_name']
            ?? $data['name']
            ?? $data['from_name']
            ?? $data['contact_name']
            ?? $data['customer']['name'] ?? null
            ?? $data['contact']['name'] ?? null;
    }

    private static function mapStatus(string $status): string
    {
        return match (strtolower($status)) {
            'open', 'active', 'new' => 'open',
            'closed', 'resolved', 'done' => 'closed',
            'pending', 'waiting' => 'pending',
            'snoozed' => 'snoozed',
            default => 'unknown',
        };
    }
}
