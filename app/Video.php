<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['lesson_id', 'embed_code'];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function getPreviewAttribute()
    {
        return str_replace('videoFoam=true"', 'videoFoam=true autoPlay=false"', $this->attributes['embed_code']);
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}
