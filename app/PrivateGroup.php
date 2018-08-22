<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Get the manager of the group
     * @return Relation
     */
    public function manager()
    {
        return $this->hasOne('App\PrivateGroupMember')
            ->where(['role' => 'manager', 'user_id' => Auth::user()->id]);
    }
}
