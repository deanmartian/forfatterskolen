<?php

namespace App\Http;

use Carbon\Carbon;

class FrontendHelpers
{
    public static function InCart($key, $value)
    {
        $in_cart = array_search($value, array_column(self::cart(), $key)); // Check if already in cart

        if ($in_cart === false) {
            return false;
        }

        return true;
    }

    public static function cartIndex($arr_key, $arr_value)
    {
        $index = null;

        foreach (self::cart() as $key => $value) {
            if (is_array($value) && $value[$arr_key] == $arr_value) {
                $index = $key;
            }
        }

        return $index;
    }

    public static function cart()
    {
        $cart = session()->has('cart') ? session('cart') : [];

        return $cart;
    }

    public static function currencyFormat($value)
    {
        return 'Kr '.number_format($value, 2, ',', '.');
    }

    public static function lessonAvailability($startedAt, $delay, $period)
    {
        if (empty($startedAt)) {
            return 'Course not started';
        }
        $availableOn = Carbon::parse($startedAt);

        if (self::isDate($delay)) {
            $availableOn = date_create($delay);
        } else {
            $availableOn->addDays($delay);
        }

        return date_format($availableOn, 'M d, Y');
    }

    public static function isDate($string)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $string);

        return $d && $d->format('Y-m-d') === $string;
    }

    public static function isLessonAvailable($startedAt, $delay, $period)
    {
        if (empty($startedAt)) {
            return 'Course not started';
        }
        $availableOn = strtotime(self::lessonAvailability($startedAt, $delay, $period));
        $now = time();

        return $availableOn <= $now;
    }

    public static function hasLessonAccess($course_taken, $lesson)
    {
        $access_lessons = $course_taken->access_lessons;

        return  in_array($lesson->id, $access_lessons);
    }

    public static function isCourseAvailable($course)
    {
        if ($course->start_date || $course->end_date) {
            $now = time();
            if ($course->start_date) {
                if ($now < strtotime($course->start_date)) {
                    return false;
                }
            }
            if ($course->end_date) {
                if ($now > strtotime($course->end_date)) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function isWebinarAvailable($webinar)
    {
        $now = time();
        if ($now < strtotime($webinar->start_date)) {
            return false;
        }

        return true;
    }

    public static function isCourseTakenAvailable($courseTaken)
    {
        if ($courseTaken->start_date || $courseTaken->end_date) {
            $now = time();
            if ($courseTaken->start_date) {
                if ($now < strtotime($courseTaken->start_date)) {
                    return false;
                }
            }
            if ($courseTaken->end_date) {
                if ($now > strtotime($courseTaken->end_date)) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function FikenConnect($url)
    {
        $username = 'cleidoscope@gmail.com';
        $password = 'moonfang';
        $headers = [];
        $headers[] = 'Accept: application/hal+json, application/vnd.error+json';
        $headers[] = 'Content-Type: application/hal+json';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
        $data = json_decode($data);

        return $data;
    }

    public static function get_num_of_words($string)
    {
        $string = preg_replace('/\s+/', ' ', trim($string));
        $words = explode(' ', strip_tags($string));

        return count($words);
    }
}
