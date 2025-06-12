<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeManuscriptFeedbackHistory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'free_manuscript_feedbacks_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['free_manuscript_id', 'date_sent'];

    public function freeManuscript(): BelongsTo
    {
        return $this->belongsTo('App\FreeManuscripts');
    }

    /**
     * Format the date sent
     *
     * @return false|null|string
     */
    public function getDateSentAttribute($value)
    {
        return $value ? date_format(date_create($value), 'd.m.Y') : null;
    }
}
