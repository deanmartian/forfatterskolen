<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordWrittenGoal extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'words_written_goals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'from_date', 'to_date', 'total_words'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function getFromDateAttribute($value)
    {
        return date_format(date_create($value), 'd.m.Y');
    }

    public function getToDateAttribute($value)
    {
        return date_format(date_create($value), 'd.m.Y');
    }
}
