<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroupMemberPreference extends Model
{
    protected $fillable = ['private_group_id', 'user_id', 'email_notifications_option'];
}
