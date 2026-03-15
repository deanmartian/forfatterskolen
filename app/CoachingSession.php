<?php

namespace App;

use App\User;
use App\CoachingTimerManuscript;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingSession extends Model
{
    protected $table = 'coaching_sessions';

    protected $fillable = [
        'coaching_timer_manuscript_id',
        'editor_id',
        'student_id',
        'whereby_room_url',
        'whereby_host_url',
        'whereby_meeting_id',
        'status',
        'recording_path',
        'transcription',
        'summary',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(CoachingTimerManuscript::class, 'coaching_timer_manuscript_id');
    }

    public function scopeForEditor($query, $editorId)
    {
        return $query->where('editor_id', $editorId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }
}
