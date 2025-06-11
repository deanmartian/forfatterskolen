<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopManuscript extends Model
{
    protected $table = 'shop_manuscripts';

    protected $fillable = ['title', 'description', 'max_words', 'price', 'split_payment_price', 'fiken_product'];
}
