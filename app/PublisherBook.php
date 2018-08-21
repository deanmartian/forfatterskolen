<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PublisherBook extends Model
{
    protected $table = 'publisher_books';
    protected $fillable = ['title', 'description', 'quote_description', 'author_image', 'book_image', 'book_image_link',
        'display_order'];

}
