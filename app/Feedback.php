<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['manuscript_id', 'filename', 'grade', 'notes'];

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(\App\Manuscript::class);
    }

    public function getFilenameAttribute($value)
    {
        if (! $value) {
            return [];
        }

        return json_decode($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}
