<?php

namespace App;

use App\User;
use App\CoachingTimeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EditorTimeSlot extends Model
{
    protected $fillable = ['editor_id', 'date', 'start_time', 'duration'];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(CoachingTimeRequest::class, 'editor_time_slot_id');
    }
}
