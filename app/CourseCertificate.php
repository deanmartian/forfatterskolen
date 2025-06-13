<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCertificate extends Model
{
    use Loggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'course_certificates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'package_id', 'template'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Course::class);
    }
}
