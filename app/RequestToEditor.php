<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestToEditor extends Model
{
    protected $fillable = ['from_type', 'editor_id', 'manuscript_id', 'answer_until', 'answer'];

    protected $appends = ['editor_name', 'AnswerP'];

    public function editor()
    {
        return $this->belongsTo('App\User', 'editor_id', 'id');
    }

    public function manuscript()
    {
        return $this->belongsTo('App\ShopManuscriptsTaken', 'manuscript_id', 'id');
    }

    public function getEditorNameAttribute()
    {
        return $this->editor->full_name;
    }

    public function getAnswerPAttribute()
    {
        if ($this->attributes['answer']) {
            return $this->attributes['answer'];
        } else {
            return trans('site.no-answer');
        }
    }
}
