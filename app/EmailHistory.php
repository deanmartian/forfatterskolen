<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailHistory extends Model
{
    protected $fillable = [ 'subject', 'from_email', 'token', 'message', 'parent', 'parent_id', 'track_code', 'open_date' ];
    protected $table = 'email_history';

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}
