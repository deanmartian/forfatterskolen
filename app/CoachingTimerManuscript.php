<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CoachingTimerManuscript extends Model {

    /**
     * For plan_type field
     * 1 is 1 hour
     * 2 is 30 min
     */

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coaching_timer_manuscripts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'file', 'payment_price', 'plan_type'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}