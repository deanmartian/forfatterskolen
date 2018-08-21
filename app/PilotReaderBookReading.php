<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PilotReaderBookReading extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_reading';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'book_id', 'role','started_at', 'last_seen', 'status', 'status_date'];

    public function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    public function book()
    {
        return $this->belongsTo('App\PilotReaderBook');
    }

    public function getStartedAtAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, H:i a') : NULL;
    }

    public function getLastSeenAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, H:i a') : NULL;
    }

    public function reason(){
        return $this->hasOne('App\PilotReaderQuittedReason', 'book_reader_id', 'id');
    }
}
