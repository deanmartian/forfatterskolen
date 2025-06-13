<?php

namespace App;

use App\Http\AdminHelpers;
use FrontendHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublisherBookLibrary extends Model
{
    protected $table = 'publisher_book_library';

    protected $fillable = ['publisher_book_id', 'book_image', 'book_link'];

    protected $appends = [
        'book_image_name',
        'book_image_jpg',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(\App\PublisherBook::class);
    }

    public function getBookImageNameAttribute()
    {
        return AdminHelpers::extractFileName($this->attributes['book_image']);
    }

    public function getBookImageJpgAttribute()
    {
        return FrontendHelpers::checkJpegImg($this->attributes['book_image']);
    }
}
