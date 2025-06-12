<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotReaderChapterFeedbackMessage extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_chapter_feedback_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['feedback_id', 'message', 'mark', 'published', 'is_reply', 'reply_from'];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(\App\PilotReaderChapterFeedback::class, 'feedback_id', 'id');
    }
}
