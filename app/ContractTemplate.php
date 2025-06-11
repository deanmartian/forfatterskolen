<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContractTemplate extends Model
{
    protected $fillable = [
        'title',
        'details',
        'signature_label',
        'show_in_project',
    ];
}
