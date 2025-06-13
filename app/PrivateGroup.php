<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class PrivateGroup extends Model
{
    protected $fillable = ['name', 'policy', 'welcome_msg', 'contact_email'];

    public function books_shared(): HasMany
    {
        return $this->hasMany(\App\PrivateGroupSharedBook::class);
    }

    public function discussions(): HasMany
    {
        return $this->hasMany(\App\PrivateGroupDiscussion::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(\App\PrivateGroupMemberInvitation::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(\App\PrivateGroupMember::class);
    }

    /**
     * Get the manager of the group
     */
    public function manager(): HasOne
    {
        return $this->hasOne(\App\PrivateGroupMember::class)
            ->where(['role' => 'manager', 'user_id' => Auth::user()->id]);
    }
}
