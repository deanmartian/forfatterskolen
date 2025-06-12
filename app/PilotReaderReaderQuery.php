<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PilotReaderReaderQuery extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_reader_queries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['from', 'to', 'book_id', 'letter', 'status'];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(\App\PilotReaderBook::class, 'pilot_reader_reader_queries', 'id', 'book_id');
    }

    public function decision(): HasOne
    {
        return $this->hasOne(\App\PilotReaderReaderQueryDecision::class, 'query_id');
    }
}
