<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectRegistration extends Model
{

    protected $fillable = ['project_id', 'type', 'value'];

    public function scopeIsbns( $query )
    {
        $query->where('type', 'isbn');
    }

    public function scopeCentralDistributions( $query )
    {
        $query->where('type', 'central-distribution');
    }

    public function scopeMentorBookBase( $query )
    {
        $query->where('type', 'mentor-book-base');
    }

    public function scopeUploadFilesToMentorBookBase( $query )
    {
        $query->where('type', 'upload-files-to-mentor-book-base');
    }
}
