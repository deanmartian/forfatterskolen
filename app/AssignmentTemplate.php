<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class AssignmentTemplate extends Model
{
    use Loggable;

    protected $fillable = ['title', 'description', 'submission_date', 'available_date', 'max_words'];

    protected $appends = ['submission_is_date'];

    public function getSubmissionIsDateAttribute()
    {
        return ! is_numeric($this->attributes['submission_date']);
    }
}
