<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroupMember extends Model
{
    protected $fillable = ['private_group_id', 'user_id', 'role'];

    public function private_group()
    {
        return $this->belongsTo(\App\PrivateGroup::class, 'private_group_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function preferences()
    {
        return $this->hasMany(\App\PrivateGroupMemberPreference::class, 'user_id');
    }
}
