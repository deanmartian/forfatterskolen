<?php

namespace App\Models\Inbox;

use App\User;
use Illuminate\Database\Eloquent\Model;

class InboxAssignment extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(InboxConversation::class, 'conversation_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
