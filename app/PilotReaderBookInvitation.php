<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilotReaderBookInvitation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_invitation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['book_id', 'email', 'status', '_token'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(\App\PilotReaderBook::class);
    }
}
