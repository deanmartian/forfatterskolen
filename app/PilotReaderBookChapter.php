<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PilotReaderBookChapter extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_chapters';

    /**
     * The attributes that are mass assignable.
     * field type 1 = chapter, 2 = questionnaire
     *
     * @var array
     */
    protected $fillable = ['pilot_reader_book_id', 'title', 'pre_read_guidance', 'post_read_guidance', 'notify_readers',
        'word_count', 'display_order', 'is_hidden', 'type'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(\App\PilotReaderBook::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(\App\PilotReaderChapterNote::class);
    }

    public function ownFeedback(): HasMany
    {
        return $this->hasMany(\App\PilotReaderChapterFeedback::class, 'chapter_id', 'id')
            ->where('user_id', \Auth::user()->id);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(\App\PilotReaderChapterFeedback::class, 'chapter_id', 'id')
            ->where('user_id', '!=', \Auth::user()->id);
    }

    // get the chapter that the logged in user have read
    public function readingChapter(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookReadingChapter::class, 'chapter_id', 'id')
            ->where('user_id', \Auth::user()->id);
    }

    public function readers(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookReadingChapter::class, 'chapter_id', 'id');
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookChapterVersion::class, 'chapter_id', 'id');
    }
}
