<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class CoachingTimerManuscript extends Model
{
    /**
     * For plan_type field
     * 1 is 1 hour
     * 2 is 30 min
     */
    const STATUS_FINISHED = 1;

    const STATUS_BOOKED = 2;

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
    protected $fillable = ['user_id', 'file', 'payment_price', 'plan_type', 'help_with', 'suggested_date', 'approved_date',
        'suggested_date_admin', 'editor_id', 'replay_link', 'comment', 'document', 'status', 'is_approved', 'hours_worked'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }
}
