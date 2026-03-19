<?php

namespace App\Models\Inbox;

use App\User;
use Illuminate\Database\Eloquent\Model;

class InboxComment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'mentioned_user_ids' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(InboxConversation::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
