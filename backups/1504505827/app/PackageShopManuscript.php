<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageShopManuscript extends Model
{
    protected $table = 'package_shop_manuscripts';
    protected $fillable = ['package_id', 'shop_manuscript_id'];



    public function package()
    {
        return $this->belongsTo('App\Package');
    }


    public function shop_manuscript()
    {
        return $this->belongsTo('App\ShopManuscript');
    }
}
