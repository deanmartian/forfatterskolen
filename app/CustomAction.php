<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomAction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'link', 'last_run'];
}
