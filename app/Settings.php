<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use VanOns\Laraberg\Models\Gutenbergable;

class Settings extends Model
{
    use Gutenbergable;

    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = ['setting_name', 'setting_value'];

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

    public static function optInRektorDescription()
    {
        return self::getByName('opt_in_rektor_description');
    }

    public static function getAllTerms()
    {
        $termsList = ['terms', 'course-terms', 'manuscript-terms', 'workshop-terms', 'coaching-terms',
            'privacy-policy-terms'];

        return self::whereIn('setting_name', $termsList)->get();
    }

    public static function gtWebinarEmailNotification()
    {
        return self::getByName('gt_confirmation_email');
    }

    public static function webinarEmailTemplate()
    {
        return self::getByName('webinar_email_template');
    }

    public static function gtReminderEmailTemplate()
    {
        return self::getByName('gt_reminder_email_template');
    }

    public static function courseNotStartedReminderSubject()
    {
        return self::getByName('course_not_started_reminder_subject');
    }

    public static function courseNotStartedReminder()
    {
        return self::getByName('course_not_started_reminder');
    }

    public static function headEditor()
    {
        return self::getByName('head-editor');
    }

    public static function editorsNote()
    {
        return self::getByName('editors-note');
    }

    public static function getByName($settingName)
    {
        return self::where('setting_name', $settingName)->pluck('setting_value')->first();
    }

    public static function getDetailsByName($settingName)
    {
        return self::where('setting_name', $settingName)->first();
    }
}
