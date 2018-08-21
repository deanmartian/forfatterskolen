<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'settings';
    public $timestamps = false;
    protected $fillable = ['setting_name', 'setting_value',];

    public static function welcomeEmail()
    {
        return self::getByName('welcome_email');
    }

    public static function terms()
    {
        return self::getByName('terms');
    }

    public static function optInTerms()
    {
        return self::getByName('opt_in_terms');
    }

    public static function optInDescription()
    {
        return self::getByName('opt_in_description');
    }

    public static function getByName($settingName)
    {
        return self::where('setting_name', $settingName)->pluck('setting_value')->first();
    }
}
