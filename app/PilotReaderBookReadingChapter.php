<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PilotReaderBookReadingChapter extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_reading_chapters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'chapter_id'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function chapter()
    {
        return $this->belongsTo('App\PilotReaderBookChapter');
    }
}
