<?php

namespace App\Http;

class AdminHelpers
{
    public static function newButtonMenu()
    {
        ?>
	<ul class="newButtonMenu">
		<li><a href="">Course</a></li>
		<li><a href="">Learner</a></li>
		<li><a href="">Assignment</a></li>
		<li><a href="">Manuscript</a></li>
		<li><a href="">Webinar</a></li>
	</ul>
	<?php
    }

    public static function courseSubpages()
    {
        $subpages = ['overview', 'lessons', 'manuscripts', 'videos', 'assignments', 'webinars', 'workshops', 'dripping', 'packages', 'learners'];

        return $subpages;
    }

    public static function validateCourseSubpage($section)
    {
        if (in_array($section, self::courseSubpages())) {
            return true;
        } else {
            return abort('404');
            exit();
        }
    }

    public static function courseAddLearners($courseLearners)
    {
        $users = \App\User::where('role', 2)->whereNotIn('id', $courseLearners)->get();

        return $users;
    }

    public static function currencyFormat($value)
    {
        return 'Kr '.number_format($value, 2, ',', '.');
    }

    public static function isDate($string)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $string);

        return $d && $d->format('Y-m-d') === $string;
    }

    public static function get_num_of_words($string)
    {
        $string = preg_replace('/\s+/', ' ', trim($string));
        $words = explode(' ', strip_tags($string));

        return count($words);
    }
}
