<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LearnerLoginActivity extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'learner_login_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['learner_login_id', 'activity'];
}
