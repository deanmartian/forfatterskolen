<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use Loggable;

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
    protected $fillable = ['course_id', 'title', 'whole_lesson_file', 'description', 'description_simplemde', 'delay', 'period',
        'allow_lesson_download'];

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

    public function documents()
    {
        return $this->hasMany('App\LessonDocuments')->orderBy('created_at', 'desc');
    }

    public function lessonContent()
    {
        return $this->hasMany('App\LessonContent')->orderBy('created_at', 'desc');
    }

    /**
     * Delete related table/model
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($lesson) { // before delete() method call this
            // delete the lesson documents first before deleting the actual record
            foreach ($lesson->documents as $document) {
                $file = public_path($document->document);
                if (\File::isFile($file)) {
                    \File::delete($file);
                }
            }

            $lesson->documents()->delete(); // delete lesson document
        });
    }
}
