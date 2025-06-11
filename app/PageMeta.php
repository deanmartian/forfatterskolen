<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageMeta extends Model
{
    protected $table = 'page_metas';

    protected $fillable = ['url', 'meta_title', 'meta_description', 'meta_image'];
}
