<?php

namespace App\Models\Inbox;

use App\User;
use Illuminate\Database\Eloquent\Model;

class InboxConversation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tags' => 'array',
        'is_spam' => 'boolean',
        'is_starred' => 'boolean',
        'snoozed_until' => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'follow_up_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(InboxMessage::class, 'conversation_id')->orderBy('created_at');
    }

    public function comments()
    {
        return $this->hasMany(InboxComment::class, 'conversation_id')->orderBy('created_at');
    }

    public function assignments()
    {
        return $this->hasMany(InboxAssignment::class, 'conversation_id')->orderByDesc('created_at');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function privateToUser()
    {
        return $this->belongsTo(User::class, 'private_to_user_id');
    }

    /**
     * Filter ut private inbokser som ikke tilhører den innloggede brukeren.
     *
     * En samtale er synlig hvis:
     *   1) Den er offentlig (private_to_user_id = null), ELLER
     *   2) Du eier den (private_to_user_id = ditt user_id), ELLER
     *   3) Den er tildelt deg (assigned_to = ditt user_id) — gjør at
     *      eieren av en privat inbox kan delegere svar til en annen admin
     *      uten å miste eierskapet.
     */
    public function scopeVisibleToUser($query, ?int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('private_to_user_id');
            if ($userId) {
                $q->orWhere('private_to_user_id', $userId);
                $q->orWhere('assigned_to', $userId);
            }
        });
    }

    public function latestMessage()
    {
        return $this->hasOne(InboxMessage::class, 'conversation_id')
            ->where('type', '!=', 'comment')
            ->latest('created_at');
    }

    public function latestInbound()
    {
        return $this->hasOne(InboxMessage::class, 'conversation_id')
            ->where('direction', 'inbound')
            ->latest('created_at');
    }

    public function aiDraft()
    {
        return $this->hasOne(InboxMessage::class, 'conversation_id')
            ->where('is_ai_draft', true)
            ->where('is_draft', true)
            ->latest('created_at');
    }

    public function timeline()
    {
        $messages = $this->messages()->get()->map(fn($m) => [
            'type' => $m->is_ai_draft ? 'ai_draft' : ($m->direction === 'inbound' ? 'customer' : 'reply'),
            'item' => $m,
            'at' => $m->created_at,
        ]);

        $comments = $this->comments()->with('user')->get()->map(fn($c) => [
            'type' => 'comment',
            'item' => $c,
            'at' => $c->created_at,
        ]);

        $assignments = $this->assignments()->with(['assignedBy', 'assignedTo'])->get()->map(fn($a) => [
            'type' => 'assignment',
            'item' => $a,
            'at' => $a->created_at,
        ]);

        return $messages->concat($comments)->concat($assignments)->sortBy('at')->values();
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeNotSpam($query)
    {
        return $query->where('is_spam', false);
    }

    public static function findOrCreateFromEmail(array $data): self
    {
        $email = $data['from_email'] ?? $data['customer_email'];
        $subject = $data['subject'] ?? '';

        // Try to find existing open conversation with same email and subject
        $existing = self::where('customer_email', $email)
            ->where('status', '!=', 'closed')
            ->where('subject', $subject)
            ->first();

        if ($existing) return $existing;

        // Find linked user
        $user = User::where('email', $email)->first();

        return self::create([
            'subject' => $subject,
            'customer_email' => $email,
            'customer_name' => $data['from_name'] ?? $data['customer_name'] ?? null,
            'user_id' => $user?->id,
            'status' => 'open',
            'inbox' => $data['inbox'] ?? $data['to_email'] ?? 'post@forfatterskolen.no',
            'source' => $data['source'] ?? 'email',
            'helpwise_id' => $data['helpwise_id'] ?? null,
        ]);
    }
}
