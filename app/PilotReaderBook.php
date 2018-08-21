<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function author()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    /**
     * Get chapters and display by display order field where 0 is on the last
     * @return HasMany
     */
    public function chapters()
    {
        return $this->hasMany('App\PilotReaderBookChapter')
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->orderBy('display_order', 'asc');
    }

    public function chaptersOnly()
    {
        return $this->hasMany('App\PilotReaderBookChapter')
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->where('type', 1)
            ->orderBy('display_order', 'asc');
    }

    public function chapterQuestionnaire()
    {
        return $this->hasMany('App\PilotReaderBookChapter')
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->where('type', 2)
            ->orderBy('display_order', 'asc');
    }

    public function chapterWordSum()
    {
        return $this->hasMany('App\PilotReaderBookChapter')->sum('word_count');
    }

    public function invitations()
    {
        return $this->hasMany('App\PilotReaderBookInvitation','book_id','id');
    }

    public function readers()
    {
        return $this->hasMany('App\PilotReaderBookReading','book_id','id');
    }

    public function settings()
    {
        return $this->hasOne('App\PilotReaderBookSettings','book_id','id');
    }
}
