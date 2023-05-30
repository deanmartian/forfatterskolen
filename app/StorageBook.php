<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StorageBook extends Model
{
    protected $fillable = [
        'project_id',
        'name'
    ];

    public function detail()
    {
        return $this->hasOne('\App\StorageDetail');
    }

    public function various()
    {
        return $this->hasOne('\App\StorageVarious');
    }
}
