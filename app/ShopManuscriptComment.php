<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopManuscriptComment extends Model
{
    protected $table = 'shop_manuscript_comments';

    protected $fillable = ['shop_manuscript_taken_id', 'user_id', 'comment'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function shop_manuscript_taken()
    {
        return $this->belongsTo(\App\ShopManuscriptsTaken::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}
