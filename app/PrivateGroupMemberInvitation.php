<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroupMemberInvitation extends Model
{
    protected $fillable = ['email', 'private_group_id', 'token', 'status', 'send_count'];

    public function group()
    {
        return $this->belongsTo('App\PrivateGroup', 'private_group_id');
    }
}
