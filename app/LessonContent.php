<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonContent extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    // protected $table = 'lesson';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['lesson_id', 'title', 'lesson_content', 'tags', 'date', 'description'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo('App\lesson');
    }
}
