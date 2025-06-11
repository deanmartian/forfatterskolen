<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Editor extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'editors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'editor_image'];
}
