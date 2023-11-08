<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseApplication extends Model
{
    protected $fillable = [
        'package_id',
        'user_id', 
        'age',
        'optional_words', 
        'reason_for_applying', 
        'need_in_course', 
        'expectations',
        'how_ready',
        'file_path',
        'approved_date'
    ];

    protected $casts = [
        'approved_date' => 'timestamp'
    ];

    protected $appends = [
        'file_link'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['file_path'];

        $extension = explode('.', basename($filename));
        if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }
}
