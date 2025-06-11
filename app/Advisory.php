<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advisory extends Model
{
    protected $fillable = ['page_name', 'page_included', 'advisory', 'from_date', 'to_date'];

    public static function getContactAdvisory()
    {
        return self::find(1);
    }

    public static function getShopManuscriptAdvisory()
    {
        return self::find(2);
    }
}
