<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PilotReaderBook extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_books';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'title', 'display_name', 'about_book', 'critique_guidance'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    /**
     * Get chapters and display by display order field where 0 is on the last
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookChapter::class)
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->orderBy('display_order', 'asc');
    }

    public function chaptersOnly(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookChapter::class)
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->where('type', 1)
            ->orderBy('display_order', 'asc');
    }

    public function chapterQuestionnaire(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookChapter::class)
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->where('type', 2)
            ->orderBy('display_order', 'asc');
    }

    public function chapterWordSum(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookChapter::class)->sum('word_count');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookInvitation::class, 'book_id', 'id');
    }

    public function readers(): HasMany
    {
        return $this->hasMany(\App\PilotReaderBookReading::class, 'book_id', 'id');
    }

    public function settings(): HasOne
    {
        return $this->hasOne(\App\PilotReaderBookSettings::class, 'book_id', 'id');
    }
}
