<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PilotReaderBookSettings extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['book_id', 'is_reading_reminder_on', 'days_of_reminder', 'will_receive_a_feedback_email',
        'is_feedback_shared', 'is_inline_commenting_allowed', 'book_units', 'is_table_of_contents_numbered', 'is_deactivated'];

    public function book(): BelongsToMany
    {
        return $this->belongsToMany(\App\PilotReaderBook::class, 'pilot_reader_book_settings', 'id');
    }
}
