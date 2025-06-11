<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Replay extends Model
{
    protected $fillable = ['title', 'video_link', 'file'];
}
