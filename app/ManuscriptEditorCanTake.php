<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManuscriptEditorCanTake extends Model
{
    protected $fillable = ['editor_id', 'date_from', 'date_to', 'how_many_script', 'how_many_hours', 'note'];
}
