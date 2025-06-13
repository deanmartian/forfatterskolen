<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserEmail extends Model
{
    use Loggable;

    /**
     * issue_date is for the faktura issue date
     *
     * @var array
     */
    protected $fillable = ['user_id', 'email'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(\App\User::class, 'user_emails', 'id');
    }
}
