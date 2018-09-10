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
    protected $fillable = ['user_id', 'file', 'payment_price', 'plan_type', 'suggested_date', 'approved_date',
        'suggested_date_admin', 'editor_id', 'replay_link', 'is_approved'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id', 'id');
    }
}