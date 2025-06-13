<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseShared extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'courses_shared';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'package_id', 'hash'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }
}
