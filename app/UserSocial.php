<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_social';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'facebook', 'instagram'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
