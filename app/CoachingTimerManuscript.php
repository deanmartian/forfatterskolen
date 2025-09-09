<?php

namespace App;

use App\User;
use App\EditorTimeSlot;
use App\CoachingTimeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CoachingTimerManuscript extends Model
{
    /**
     * For plan_type field
     * 1 is 1 hour
     * 2 is 30 min
     */
    const STATUS_FINISHED = 1;

    const STATUS_BOOKED = 2;

    protected $table = 'coaching_timer_manuscripts';

    protected $fillable = ['user_id', 'file', 'payment_price', 'plan_type', 'help_with', 'suggested_date', 'approved_date',
        'suggested_date_admin', 'editor_id', 'editor_time_slot_id', 'replay_link', 'comment', 'document', 'status', 'is_approved', 'hours_worked'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id', 'id');
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(EditorTimeSlot::class, 'editor_time_slot_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(CoachingTimeRequest::class, 'coaching_timer_manuscript_id');
    }
}
