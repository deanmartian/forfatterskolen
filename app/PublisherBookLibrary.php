<?php
namespace App;

use App\Http\AdminHelpers;
use Illuminate\Database\Eloquent\Model;

class PublisherBookLibrary extends Model
{
    protected $table = 'publisher_book_library';
    protected $fillable = ['publisher_book_id', 'book_image', 'book_link'];
    protected $appends = [
        'book_image_name'
    ];

    public function publisher()
    {
        return $this->belongsTo('App\PublisherBook');
    }

    public function getBookImageNameAttribute()
    {
        return AdminHelpers::extractFileName($this->attributes['book_image']);
    }
}
