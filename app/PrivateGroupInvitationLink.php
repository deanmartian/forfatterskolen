<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateGroupInvitationLink extends Model
{
    protected $fillable = ['private_group_id', 'link_token', 'enabled'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(\App\PrivateGroup::class, 'private_group_id');
    }
}
