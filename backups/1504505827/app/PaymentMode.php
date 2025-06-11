<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMode extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payment_modes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mode'];
}
