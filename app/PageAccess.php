<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageAccess extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'page_access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'page_id'];
}
