<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class ProjectBookPicture extends Model
{
    use Loggable;
    
    protected $fillable = ['project_id', 'image', 'description'];
}
