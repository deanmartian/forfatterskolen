<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OptIn extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'opt_in';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'list_id', 'main_description', 'form_description', 'description', 'pdf_file'];

    public static function getBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

    public function getPdfFileNameAttribute()
    {
        return last(explode('/', $this->attributes['pdf_file']));
    }
}
