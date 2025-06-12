<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotReaderBookBookmark extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_bookmark';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['bookmarker_id', 'book_id', 'chapter_id', 'paragraph_text', 'paragraph_order'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(\App\PilotReaderBook::class);
    }
}
