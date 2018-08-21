<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroupMember extends Model
{
    protected $fillable = [ 'private_group_id', 'user_id', 'role'];

    public function private_group()
    {
        return $this->belongsTo('App\PrivateGroup', 'private_group_id');
    }
}
