<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    protected $fillable = ['user_id', 'book_id', 'role', 'started_at', 'last_seen', 'status', 'status_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'user_id', 'id');
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(\App\PilotReaderBook::class);
    }

    public function getStartedAtAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, H:i a') : null;
    }

    public function getLastSeenAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, H:i a') : null;
    }

    public function reason(): HasOne
    {
        return $this->hasOne(\App\PilotReaderQuittedReason::class, 'book_reader_id', 'id');
    }
}
