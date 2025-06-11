<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroupDiscussionReply extends Model
{
    protected $fillable = ['disc_id', 'user_id', 'message'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function discussion()
    {
        return $this->belongsTo('App\PrivateGroupDiscussion', 'disc_id');
    }
}
