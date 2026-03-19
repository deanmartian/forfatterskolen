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
                'status' => self::mapStatus($data['status'] ?? $data['state'] ?? (($data['is_resolved'] ?? false) ? 'closed' : 'open')),
                'assigned_to' => isset($data['assigned_to']['assigned_to']['id']) ? (string) $data['assigned_to']['assigned_to']['id'] : ($data['assigned_to'] ?? $data['assignee'] ?? null),
                'tags' => $data['tags'] ?? $data['labels'] ?? null,
                'raw_payload' => $data,
                'helpwise_created_at' => isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at']) : now(),
            ]
        );
    }

    private static function extractEmail(array $data): ?string
    {
        // Direct fields
        if (!empty($data['customer_email'])) return $data['customer_email'];
        if (!empty($data['email'])) return $data['email'];
        if (!empty($data['from_email'])) return $data['from_email'];
        if (!empty($data['contact_email'])) return $data['contact_email'];

        // Nested objects
        if (!empty($data['customer']['email'])) return $data['customer']['email'];
        if (!empty($data['contact']['email'])) return $data['contact']['email'];

        // Helpwise format: emails object with nested from array
        if (!empty($data['emails']) && is_array($data['emails'])) {
            $firstEmail = reset($data['emails']);
            if (!empty($firstEmail['from']) && is_array($firstEmail['from'])) {
                $from = reset($firstEmail['from']);
                if (!empty($from['email'])) return $from['email'];
            }
        }

        return null;
    }

    private static function extractName(array $data): ?string
    {
        if (!empty($data['customer_name'])) return $data['customer_name'];
        if (!empty($data['name'])) return $data['name'];
        if (!empty($data['from_name'])) return $data['from_name'];
        if (!empty($data['contact_name'])) return $data['contact_name'];

        if (!empty($data['customer']['name'])) return $data['customer']['name'];
        if (!empty($data['contact']['name'])) return $data['contact']['name'];

        // Helpwise format
        if (!empty($data['emails']) && is_array($data['emails'])) {
            $firstEmail = reset($data['emails']);
            if (!empty($firstEmail['from']) && is_array($firstEmail['from'])) {
                $from = reset($firstEmail['from']);
                if (!empty($from['name'])) return $from['name'];
            }
        }

        return null;
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
