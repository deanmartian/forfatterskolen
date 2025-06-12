<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageWorkshop extends Model
{
    protected $table = 'package_workshops';

    protected $fillable = ['package_id', 'workshop_id'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Package::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(\App\Workshop::class);
    }
}
