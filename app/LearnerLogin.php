<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearnerLogin extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'learner_logins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'ip', 'country', 'country_code', 'provider', 'platform'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function loginActivity(): HasMany
    {
        return $this->hasMany(\App\LearnerLoginActivity::class);
    }
}
