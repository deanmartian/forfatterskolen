<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroupInvitationLink extends Model
{
    protected $fillable = ['private_group_id', 'link_token', 'enabled'];

    public function group()
    {
        return $this->belongsTo(\App\PrivateGroup::class, 'private_group_id');
    }
}
