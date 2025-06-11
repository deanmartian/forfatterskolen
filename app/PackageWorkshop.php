<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageWorkshop extends Model
{
    protected $table = 'package_workshops';

    protected $fillable = ['package_id', 'workshop_id'];

    public function package()
    {
        return $this->belongsTo('App\Package');
    }

    public function workshop()
    {
        return $this->belongsTo('App\Workshop');
    }
}
