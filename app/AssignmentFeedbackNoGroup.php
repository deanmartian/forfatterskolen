<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentFeedbackNoGroup extends Model
{
    use Loggable;

    protected $table = 'assignment_feedbacks_no_group';

    protected $fillable = ['assignment_manuscript_id', 'learner_id', 'feedback_user_id', 'filename', 'is_admin', 'is_active', 'availability', 'hours_worked', 'notes_to_head_editor'];

    protected $with = ['manuscript'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(\App\AssignmentManuscript::class, 'assignment_manuscript_id', 'id');
    }

    public function feedbackUser(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'feedback_user_id', 'id');
    }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'learner_id', 'id');
    }

    /**
     * Accessor field
     */
    public function getFileLinkAttribute(): string
    {
        $fileLink = '';
        $filename = $this->attributes['filename'];

        $extension = explode('.', basename($filename));
        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            $fileLink = '<a href="/js/ViewerJS/#../..'.$filename.'">'.basename($filename).'</a>';
        } elseif (end($extension) == 'docx' || end($extension) == 'doc') {
            $fileLink = '<a href="https://view.officeapps.live.com/op/embed.aspx?src='.url('').$filename.'">'
                .basename($filename).'</a>';
        }

        return $fileLink;
    }
}
