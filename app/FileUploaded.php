<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FileUploaded extends Model
{
    protected $table = 'files_uploaded';

    protected $fillable = ['file_location', 'hash'];
}
