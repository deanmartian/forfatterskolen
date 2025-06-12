<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormerCourse extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'former_courses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'package_id', 'is_active', 'started_at', 'start_date', 'end_date', 'access_lessons',
        'years', 'is_free', 'created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }
}
