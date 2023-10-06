<?php
namespace App;

use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;

class PublisherBook extends Model
{
    protected $table = 'publisher_books';
    protected $fillable = ['title', 'description', 'quote_description', 'author_image', 'book_image', 'book_image_link',
        'display_order'];

    protected $appends = ['author_image_jpg'];

    public function libraries()
    {
        return $this->hasMany('App\PublisherBookLibrary');
    }

    public function getAuthorImageJpgAttribute()
    {
        return FrontendHelpers::checkJpegImg($this->attributes['author_image']);
    }

}
