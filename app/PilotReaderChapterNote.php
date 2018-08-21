<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderChapterNote extends Model {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_chapter_notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pilot_reader_book_chapter_id', 'mark', 'published', 'message'];

    public function chapter()
    {
        return $this->belongsTo('App\PilotReaderBookChapter');
    }
}