<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DelayedEmail extends Model
{
    protected $fillable = ['subject', 'message', 'from_name', 'from_email', 'recipient', 'attachment', 'send_date',
        'parent', 'parent_id'];
}
