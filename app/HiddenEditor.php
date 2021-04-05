<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HiddenEditor extends Model
{
    protected $fillable = ['editor_id', 'hide_date_from', 'hide_date_to', 'notes'];
}
