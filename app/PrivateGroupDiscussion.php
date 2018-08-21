<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroupDiscussion extends Model
{
    protected $fillable = [ 'private_group_id', 'user_id', 'subject', 'message', 'is_announcement'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function replies()
    {
        return $this->hasMany('App\PrivateGroupDiscussionReply', 'disc_id');
    }

    public function group()
    {
        return $this->belongsTo('App\PrivateGroup', 'private_group_id');
    }
}
