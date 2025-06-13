<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateGroupMemberInvitation extends Model
{
    protected $fillable = ['email', 'private_group_id', 'token', 'status', 'send_count'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(\App\PrivateGroup::class, 'private_group_id');
    }
}
