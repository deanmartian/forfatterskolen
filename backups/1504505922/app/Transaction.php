<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\FrontendHelpers;

class Transaction extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['invoice_id', 'mode', 'mode_transaction', 'amount'];

    public function invoice()
    {
        return $this->belongsTo('App\Invoice');
    }
    
    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

}
