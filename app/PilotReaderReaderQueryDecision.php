<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderReaderQueryDecision extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_reader_query_decisions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['query_id', 'decision'];
}
