<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentManuscript extends Model
{
    
    protected $table = 'assignment_manuscripts';
    protected $fillable = ['assignment_id', 'user_id', 'filename', 'words', 'grade', 'type', 'manu_type', 'editor_id',
        'join_group', 'expected_finish'];



    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }


    public function feedbacks()
    {
        return $this->hasMany('App\AssignmentFeedback');
    }

    public function noGroupFeedbacks()
    {
        return $this->hasMany('App\AssignmentFeedbackNoGroup');
    }

    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id', 'id');
    }

    /**
     * Accessor field
     * @return string
     */
    public function getFileLinkAttribute()
    {
        $fileLink = '';
        $filename = $this->attributes['filename'];

        $extension = explode('.', basename($filename));
        if( end($extension) == 'pdf' || end($extension) == 'odt' ) {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif( end($extension) == 'docx' || end($extension) == 'doc' ) {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }

    public function getExpectedFinishAttribute($value) {
        return $value ? date_format(date_create($value), 'd.m.Y') : NULL;
    }

}
