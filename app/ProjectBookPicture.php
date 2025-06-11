<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectBookPicture extends Model
{
    protected $fillable = ['project_id', 'image', 'description'];
}
