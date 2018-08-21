<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderReaderQuery extends Model {
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
    protected $fillable = [ 'from', 'to', 'book_id', 'letter', 'status' ];

    public function books()
    {
        return $this->belongsToMany('App\PilotReaderBook', 'pilot_reader_reader_queries', 'id', 'book_id');
    }

    public function decision()
    {
        return $this->hasOne('App\PilotReaderReaderQueryDecision', 'query_id');
    }

}