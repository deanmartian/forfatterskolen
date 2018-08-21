<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'email_template';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['page_name', 'from_email', 'email_content'];
}