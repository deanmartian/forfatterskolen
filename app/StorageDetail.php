<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageDetail extends Model
{
    protected $fillable = [
        'project_book_id', // id from project_registrations table
        'subtitle',
        'original_title',
        'author',
        'editor',
        'publisher',
        'book_group',
        'item_number',
        'isbn',
        'isbn_ebook',
        'edition_on_sale',
        'edition_total',
        'release_date',
        'release_date_for_media',
        'price_vat',
        'registered_with_council',
    ];
}
