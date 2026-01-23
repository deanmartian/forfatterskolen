<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiFile extends Model
{
    protected $table = 'api_files';

    protected $fillable = [
        'user_id',
        'original_filename',
        'mime_type',
        'size',
        'storage_path',
    ];
}
