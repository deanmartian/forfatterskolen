<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webinar_presenters()
    {
        return $this->hasMany('App\FreeWebinarPresenter');
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
