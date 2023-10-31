<?php
namespace App;

use App\Http\AdminHelpers;
use Illuminate\Database\Eloquent\Model;

class AssignmentManuscript extends Model
{
    
    protected $table = 'assignment_manuscripts';
    protected $fillable = ['assignment_id', 'user_id', 'filename', 'words', 'grade', 'type', 'manu_type', 'editor_id',
        'join_group', 'letter_to_editor', 'expected_finish', 'editor_expected_finish'];
    protected $appends = ['file_link', 'file_link_with_download', 'assignment_type', 'where_in_script',
        'file_extension',
        'file_link_url'];

    const APPROVED_STATUS = 1; // approved feedback status
    const FINISHED_STATUS = 2; // finished status


    public function assignment()
    {
        return $this->belongsTo('App\Assignment');
    }


    public function user()
    {
        return $this->belongsTo('App\User');
    }


    public function feedbacks() //cannot use this. 
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

    /**
     * Accessor field
     * @return string
     */
    public function getFileLinkWithDownloadAttribute()
    {
        $fileLink = '';
        $files = explode(',', $this->attributes['filename']);

        foreach ($files as $file) {
            $extension = explode('.', basename($file));

            if (end($extension) == 'pdf' || end($extension) == 'odt') {
                $fileLink .= '<a href="/js/ViewerJS/#../..'.trim($file).'">'.basename($file).'</a>';

                if ($file) {
                    $fileLink .= ' <a href="'.$file.'" download><i class="fa fa-download" aria-hidden="true"></i></a>';
                }

                $fileLink .= ', ';
            } else {
                $fileLink .= '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').trim($file).'">'.basename($file).'</a>';

                if ($file) {
                    $fileLink .= ' <a href="'.$file.'" download><i class="fa fa-download" aria-hidden="true"></i></a>';
                }

                $fileLink .= ', ';
            }
        }

        return trim($fileLink, ', ');
    }

    public function getFileExtensionAttribute()
    {
        $file = explode('.', basename($this->attributes['filename']));
        return end($file);
    }

    public function getFileLinkUrlAttribute()
    {
        $fileLink = '';
        $file = $this->attributes['filename'];
        $extension = explode('.', basename($file));

        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            $fileLink = "/js/ViewerJS/#../..".trim($file);
        } else {
            $fileLink = "https://view.officeapps.live.com/op/embed.aspx?src=".url('').trim($file);
        }

        return $fileLink;
    }

    public function getExpectedFinishAttribute($value) {
        return $value ? date_format(date_create($value), 'd.m.Y') : NULL;
    }

    public function getEditorExpectedFinishAttribute($value) {
        return $value ? date_format(date_create($value), 'd.m.Y') : NULL;
    }

    public function getAssignmentTypeAttribute()
    {
        return AdminHelpers::assignmentType($this->attributes['type']);
    }

    public function getWhereInScriptAttribute()
    {
        return AdminHelpers::manuscriptType($this->attributes['manu_type']);
    }
}
