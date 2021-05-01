<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentFeedbackNoGroup extends Model
{

    protected $table = 'assignment_feedbacks_no_group';
    protected $fillable = ['assignment_manuscript_id', 'learner_id','feedback_user_id', 'filename', 'is_admin', 'is_active', 'availability', 'hours_worked', 'notes_to_head_editor'];
    protected $with = ['manuscript'];



    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function manuscript()
    {
        return $this->belongsTo('App\AssignmentManuscript','assignment_manuscript_id','id');
    }

    public function feedbackUser()
    {
        return $this->belongsTo('App\User','feedback_user_id','id');
    }

    public function learner()
    {
        return $this->belongsTo('App\User','learner_id','id');
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
}
