<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderChapterFeedbackMessage extends Model {
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

    public function feedback()
    {
        return $this->belongsTo('App\PilotReaderChapterFeedback','feedback_id','id');
    }
}