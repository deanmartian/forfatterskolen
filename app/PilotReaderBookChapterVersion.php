<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderBookChapterVersion extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_chapter_versions';

    /**
     * The attributes that are mass assignable.
     * field type 1 = chapter, 2 = questionnaire
     *
     * @var array
     */
    protected $fillable = ['chapter_id', 'content', 'change_description'];
}
