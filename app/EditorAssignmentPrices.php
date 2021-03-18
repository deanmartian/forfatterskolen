<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditorAssignmentPrices extends Model
{
    protected $fillable = ['assignment', 'unit', 'price'];
}
