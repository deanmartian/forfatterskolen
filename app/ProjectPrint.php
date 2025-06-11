<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectPrint extends Model
{
    protected $fillable = [
        'project_id',
        'isbn',
        'number',
        'pages',
        'format',
        'width',
        'height',
        'originals',
        'binding',
        'yarn_stapling',
        'media',
        'print_method',
        'color',
        'number_of_color_pages',
    ];
}
