<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AssignmentManuscriptEditorCanTake extends Model
{
    protected $fillable = ['assignment_manuscript_id', 'editor_id', 'how_many_you_can_take'];

    public function assignment()
    {
        return $this->belongsTo('App\Assignment', 'assignment_manuscript_id', 'id');
    }

    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id', 'id');
    }

    public function getAssignedCountAttribute()
    {
        $count = DB::select('SELECT COUNT(id) as cnt FROM assignment_manuscripts WHERE assignment_id = '.$this->attributes['assignment_manuscript_id'].' AND editor_id = '.$this->attributes['editor_id'].' GROUP BY editor_id');
        if ($count) {
            return $count[0]->cnt;
        }

        return 0;
    }

    public function getFinishedCountAttribute()
    {
        $count = DB::select('SELECT COUNT(id) as cnt FROM assignment_manuscripts WHERE assignment_id = '.$this->attributes['assignment_manuscript_id'].' AND editor_id = '.$this->attributes['editor_id'].' AND `has_feedback`= 1 GROUP BY editor_id');
        if ($count) {
            return $count[0]->cnt;
        }

        return 0;
    }

    public function getPendingCountAttribute()
    {
        $count = DB::select('SELECT COUNT(id) as cnt FROM assignment_manuscripts WHERE assignment_id = '.$this->attributes['assignment_manuscript_id'].' AND editor_id = '.$this->attributes['editor_id'].' AND `has_feedback`= 0 GROUP BY editor_id');
        if ($count) {
            return $count[0]->cnt;
        }

        return 0;
    }
}
