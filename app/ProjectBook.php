<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectBook extends Model
{

    protected $fillable = ['project_id', 'user_id', 'book_name', 'isbn_hardcover_book', 'isbn_ebook'];

}
