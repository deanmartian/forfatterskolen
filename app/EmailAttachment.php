<?php
namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmailAttachment extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    //protected $table = 'email_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['filename', 'hash'];
}