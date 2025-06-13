<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FreeWebinar extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'free_webinars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'start_date', 'image', 'gtwebinar_id'];

    /**
     * Get the webinar presenters
     */
    public function webinar_presenters(): HasMany
    {
        return $this->hasMany(\App\FreeWebinarPresenter::class);
    }

    /**
     * On delete, remove also the files
     */
    public static function boot()
    {
        parent::boot();

        // if the row is deleted, delete also the document for that row
        FreeWebinar::deleted(function ($record) {
            $file = public_path($record->image);
            if (\File::isFile($file)) {
                \File::delete($file);
            }
        });
    }
}
