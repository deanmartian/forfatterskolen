<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\FrontendHelpers;

class Lesson extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lessons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['course_id', 'title', 'description', 'description_simplemde', 'delay', 'period'];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    
    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }


    public function videos()
    {
        return $this->hasMany('App\Video')->orderBy('created_at', 'desc');
    }

}
