<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';

    protected $fillable = ['name', 'email', 'details', 'teamviewer', 'image', 'role', 'sequence'];

    public static function boot()
    {
        parent::boot();

        // to prevent inserting of null as string if the value is empty
        static::creating(function ($model) {
            foreach ($model->attributes as $key => $value) {
                if ($key == 'sequence') {
                    $model->{$key} = (empty($value) || $value === 'null' || is_null($value)) ? 0 : $value;
                }
            }
        });

        // to prevent updating of null as string if the value is empty
        static::updating(function ($model) {
            foreach ($model->attributes as $key => $value) {
                if ($key == 'sequence') {
                    $model->{$key} = (empty($value) || $value === 'null' || is_null($value)) ? 0 : $value;
                }
            }
        });

    }
}
