<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestToEditor extends Model
{
    protected $fillable = ['from_type', 'editor_id', 'manuscript_id', 'answer_until', 'answer',
        'project_item_id', 'project_item_type'];

    protected $appends = ['editor_name', 'AnswerP'];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'editor_id', 'id');
    }

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(\App\ShopManuscriptsTaken::class, 'manuscript_id', 'id');
    }

    public function projectItem()
    {
        switch ($this->project_item_type) {
            case 'copy-editing':
                return $this->belongsTo(\App\CopyEditingManuscript::class, 'project_item_id', 'id');
            case 'correction':
                return $this->belongsTo(\App\CorrectionManuscript::class, 'project_item_id', 'id');
            case 'self-publishing':
                return $this->belongsTo(\App\SelfPublishing::class, 'project_item_id', 'id');
            default:
                return $this->belongsTo(\App\CopyEditingManuscript::class, 'project_item_id', 'id');
        }
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
