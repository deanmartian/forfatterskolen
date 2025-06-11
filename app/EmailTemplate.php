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
    protected $fillable = [
        'page_name', 'subject', 'from_email', 'email_content', 'course_type', 'course_id', 'is_assignment_manu_feedback',
    ];
}
