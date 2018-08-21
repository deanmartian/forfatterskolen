<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrivateGroup extends Model
{
    protected $fillable = [ 'name', 'policy', 'welcome_msg', 'contact_email' ];

    public function books_shared()
    {
        return $this->hasMany('App\PrivateGroupSharedBook');
    }

    public function discussions()
    {
        return $this->hasMany('App\PrivateGroupDiscussion');
    }

    public function invitations()
    {
        return $this->hasMany('App\PrivateGroupMemberInvitation');
    }

    public function members()
    {
        return $this->hasMany('App\PrivateGroupMember');
    }
}
