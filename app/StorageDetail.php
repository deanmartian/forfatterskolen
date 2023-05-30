<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageDetail extends Model
{
    protected $fillable = [
        'storage_book_id',
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
        'price_vat',
        'registered_with_council',
    ];
}
