<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(\App\PilotReaderBookChapter::class, 'chapter_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(\App\PilotReaderChapterFeedbackMessage::class, 'feedback_id', 'id');
    }

    public function readerMessages(): HasMany
    {
        return $this->hasMany(\App\PilotReaderChapterFeedbackMessage::class, 'feedback_id', 'id')
            ->where('is_reply', 0)
            ->where('published', 1);
    }
}
