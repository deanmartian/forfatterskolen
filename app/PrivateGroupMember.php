<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrivateGroupMember extends Model
{
    protected $fillable = ['private_group_id', 'user_id', 'role'];

    public function private_group(): BelongsTo
    {
        return $this->belongsTo(\App\PrivateGroup::class, 'private_group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(\App\PrivateGroupMemberPreference::class, 'user_id');
    }
}
