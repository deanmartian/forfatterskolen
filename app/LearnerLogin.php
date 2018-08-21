<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function loginActivity()
    {
        return $this->hasMany('App\LearnerLoginActivity');
    }

}