<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class UserEmail extends Model
{
    use Loggable;

    /**
     * issue_date is for the faktura issue date
     *
     * @var array
     */
    protected $fillable = ['user_id', 'email'];

    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'user_emails', 'id');
    }
}
