<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manuscript extends Model
{
    use Loggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'manuscripts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['coursetaken_id', 'filename', 'word_count', 'grade', 'feedback_user_id', 'expected_finish'];

    public function courseTaken(): BelongsTo
    {
        return $this->belongsTo(\App\CoursesTaken::class, 'coursetaken_id');
    }

    public function getUserAttribute()
    {
        $courseTaken = $this->courseTaken;

        return $courseTaken->user;
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(\App\Feedback::class)->orderBy('created_at', 'desc');
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    public function getWordCountAttribute($value)
    {
        return number_format($value);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'feedback_user_id');
    }

    public function getStatusAttribute()
    {
        $file = $this->attributes['filename'];
        $feedbacks = $this->feedbacks->count();
        if ($file && $feedbacks > 0) {
            return 'Finished';
        } elseif ($file && $feedbacks == 0) {
            return 'Started';
        } elseif (! $file) {
            return 'Not started';
        }
    }
}
