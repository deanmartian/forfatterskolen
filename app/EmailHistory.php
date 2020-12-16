<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailHistory extends Model
{
    use SoftDeletes;

    protected $fillable = [ 'subject', 'from_email', 'token', 'message', 'parent', 'parent_id', 'recipient',
        'track_code', 'date_open' ];
    protected $table = 'email_history';
    protected $appends = ['recipient', 'recipient_id'];

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    public function getDateOpenAttribute($value)
    {
        return $value ? date_format(date_create($value), 'M d, Y h:i a') : NULL;
    }

    public function getRecipientAttribute()
    {
        return $this->recipientQuery()['full_name'];
    }

    public function getRecipientIdAttribute()
    {
        return $this->recipientQuery()['learner_id'];
    }

    public function recipientQuery()
    {
        $parent = $this->attributes['parent'];
        $parent_id = $this->attributes['parent_id'];

        $learner_id = '';
        $fullname = $this->attributes['parent_id'];
        
        if (strpos($parent, 'shop-manuscripts-taken') !== false ) {
            $shopManuscript = ShopManuscriptsTaken::with('user')->where('id', $parent_id)->first();
            if($shopManuscript) {
                $learner_id = $shopManuscript->user_id;
                $fullname = $shopManuscript->user->full_name;
            }
        }

        if (strpos($parent, 'courses-taken') !== false ) {
            $courseTaken = CoursesTaken::with('user')->where('id', $parent_id)->first();
            if($courseTaken) {
                $learner_id = $courseTaken->user_id;
                $fullname = $courseTaken->user->full_name;   
            }
        }

        if (strpos($parent, 'assignment-manuscripts') !== false ) {
            $assignmentManuscript = AssignmentManuscript::with('user')->where('id', $parent_id)->first();
            if($assignmentManuscript) {
                $learner_id = $assignmentManuscript->user_id;
                $fullname = $assignmentManuscript->user->full_name;
            }
        }

        return [
            'learner_id' => $learner_id,
            'full_name' => $fullname
        ];

    }
}
