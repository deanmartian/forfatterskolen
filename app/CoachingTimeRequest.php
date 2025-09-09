<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingTimeRequest extends Model
{
    protected $fillable = ['coaching_timer_manuscript_id', 'editor_time_slot_id', 'status'];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(CoachingTimerManuscript::class, 'coaching_timer_manuscript_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(EditorTimeSlot::class, 'editor_time_slot_id');
    }
}
