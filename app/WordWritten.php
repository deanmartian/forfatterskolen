<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordWritten extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'words_written';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'date', 'words'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}
