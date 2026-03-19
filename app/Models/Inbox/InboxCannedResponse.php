<?php

namespace App\Models\Inbox;

use App\User;
use Illuminate\Database\Eloquent\Model;

class InboxCannedResponse extends Model
{
    protected $guarded = ['id'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
