<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderReaderProfile extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_reader_profiles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'genre_preferences', 'dislike_contents', 'expertise', 'favourite_author', 'availability'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
