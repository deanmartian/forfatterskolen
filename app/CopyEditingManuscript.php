<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CopyEditingManuscript extends Model {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'copy_editing_manuscripts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'file', 'payment_price'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}