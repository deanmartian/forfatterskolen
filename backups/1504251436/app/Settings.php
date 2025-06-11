<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = ['setting_name', 'setting_value'];

    public static function welcomeEmail()
    {
        return self::getByName('welcome_email');
    }

    public static function getByName($settingName)
    {
        return self::where('setting_name', $settingName)->pluck('setting_value')->first();
    }
}
