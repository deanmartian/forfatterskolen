<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonDocuments extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lessons_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['lesson_id', 'name', 'document'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Lesson::class);
    }

    /**
     * On delete, remove also the files
     */
    public static function boot()
    {
        parent::boot();

        // if the row is deleted, delete also the document for that row
        LessonDocuments::deleted(function ($photo) {
            $file = public_path($photo->document);
            if (\File::isFile($file)) {
                \File::delete($file);
            }
        });
    }
}
