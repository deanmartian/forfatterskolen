<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderChapterFeedback extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_chapter_feedback';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'chapter_id', 'chapter_version_id'];

    public function chapter()
    {
        return $this->belongsTo('App\PilotReaderBookChapter', 'chapter_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany('App\PilotReaderChapterFeedbackMessage', 'feedback_id', 'id');
    }

    public function readerMessages()
    {
        return $this->hasMany('App\PilotReaderChapterFeedbackMessage', 'feedback_id', 'id')
            ->where('is_reply', 0)
            ->where('published', 1);
    }
}
