<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailOutRecipient extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'courses_email_out_recipients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email_out_id', 'user_id'];

    public function emailOut()
    {
        return $this->belongsTo(\App\EmailOut::class);
    }
}
