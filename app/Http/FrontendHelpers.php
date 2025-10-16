<?php

namespace App\Http;

use App\Advisory;
use App\Course;
use App\Genre;
use App\Helpers\FileToText;
use App\Lesson;
use App\PaymentMode;
use App\PilotReaderBook;
use App\PilotReaderBookChapter;
use App\PilotReaderBookReading;
use App\PrivateGroupMember;
use App\Project;
use App\ProjectBookSale;
use App\SelfPublishingPortalRequest;
use App\Settings;
use App\Staff;
use App\User;
use App\WebinarRegistrant;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Request as GlobalRequest;
use ZipArchive;

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

    public static function formatCurrency($value)
    {
        return number_format($value, 2, ',', '');
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
            $availableOn->addDays((int) $delay);
        }

        return date_format($availableOn, 'M d, Y');
    }

    public static function isDate($string)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $string);

        return $d && $d->format('Y-m-d') === $string;
    }

    public static function formatDate($date)
    {
        return Carbon::parse($date)->format('d.m.Y');
    }

    public static function getTimeFromDT($date)
    {
        return Carbon::parse($date)->format('H:i');
    }

    public static function formatDateTimeNor($date)
    {
        return \Carbon\Carbon::parse($date)->format('d.m.Y').' klokken '.\Carbon\Carbon::parse($date)->format('H:i');
    }

    public static function formatDateTimeNor2($date)
    {
        return \Carbon\Carbon::parse($date)->format('d M Y').' Klokken '.\Carbon\Carbon::parse($date)->format('H:i');
    }

    public static function formatByMd($date)
    {
        return Carbon::parse($date)->format('M d');
    }

    public static function formatToYMDtoPrettyDate($date)
    {
        return Carbon::parse($date)->format('M d, Y h:i A');
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
        $access_lessons = $course_taken ? $course_taken->access_lessons : []; // $course_taken->access_lessons

        return in_array($lesson->id, $access_lessons);
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

    /**
     * Check if course is active
     */
    public static function isCourseActive($course): bool
    {

        if (! $course->status) {
            return false;
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

    public static function isWebinarAvailablePlusHour($webinar)
    {
        $now = time();
        if ($now < (strtotime($webinar->start_date) + 60 * 60)) {
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

    public static function roundUpToNearestMultiple($n, $increment = 1000)
    {
        return (int) ($increment * ceil($n / $increment));
    }

    public static function convertMonthLanguage($month_number = null)
    {
        $monthNames = [
            ['id' => 1, 'option' => 'januar'],
            ['id' => 2, 'option' => 'februar'],
            ['id' => 3, 'option' => 'mars'],
            ['id' => 4, 'option' => 'april'],
            ['id' => 5, 'option' => 'mai'],
            ['id' => 6, 'option' => 'juni'],
            ['id' => 7, 'option' => 'juli'],
            ['id' => 8, 'option' => 'august'],
            ['id' => 9, 'option' => 'september'],
            ['id' => 10, 'option' => 'oktober'],
            ['id' => 11, 'option' => 'november'],
            ['id' => 12, 'option' => 'desember'],
        ];

        if ($month_number) {
            foreach ($monthNames as $monthName) {
                if ($monthName['id'] == $month_number) {
                    return $monthName['option'];
                }
            }
        }

        return null;
    }

    public static function convertDayLanguage($day_number = null)
    {
        $dayNumbers = [
            ['id' => 1, 'option' => 'mandag'],
            ['id' => 2, 'option' => 'tirsdag'],
            ['id' => 3, 'option' => 'onsdag'],
            ['id' => 4, 'option' => 'torsdag'],
            ['id' => 5, 'option' => 'fredag'],
            ['id' => 6, 'option' => 'lørdag'],
            ['id' => 7, 'option' => 'søndag'],
        ];

        if ($day_number) {
            foreach ($dayNumbers as $dayNumber) {
                if ($dayNumber['id'] == $day_number) {
                    return $dayNumber['option'];
                }
            }
        }

        return null;
    }

    /**
     * List of front pages and the route name
     */
    public static function frontPageList(): array
    {
        return [
            ['page_name' => 'Front Page', 'page_route' => 'front.home'],
            ['page_name' => 'Course Page', 'page_route' => 'front.course.index'],
            ['page_name' => 'Course Single Page', 'page_route' => 'front.course.show'],
            ['page_name' => 'Course Checkout Page', 'page_route' => 'front.course.checkout'],
            ['page_name' => 'Shop Manuscript Page', 'page_route' => 'front.shop-manuscript.index'],
            ['page_name' => 'Shop Manuscript Checkout Page', 'page_route' => 'front.shop-manuscript.checkout'],
            ['page_name' => 'Publishing Page', 'page_route' => 'front.publishing'],
            ['page_name' => 'Blog Page', 'page_route' => 'front.blog'],
            ['page_name' => 'Blog Single Page', 'page_route' => 'front.read-blog'],
            ['page_name' => 'Workshop Page', 'page_route' => 'front.workshop.index'],
            ['page_name' => 'Workshop Single Page', 'page_route' => 'front.workshop.show'],
            ['page_name' => 'Workshop Checkout Page', 'page_route' => 'front.workshop.checkout'],
            ['page_name' => 'Faq Page', 'page_route' => 'front.faq'],
            ['page_name' => 'Contact Us Page', 'page_route' => 'front.contact-us'],
        ];
    }

    public static function getShopManuscriptAdvisory()
    {
        return Advisory::getShopManuscriptAdvisory();
    }

    public static function coursePortalNav()
    {
        $navs = [
            [
                'route_name' => 'learner.dashboard',
                'fa-icon' => 'fa fa-home',
                'label' => 'Kontrollpanel',
                'is_active' => Route::currentRouteName() === 'learner.dashboard',
            ],
            [
                'route_name' => 'learner.course',
                'fa-icon' => 'fa fa-graduation-cap',
                'label' => trans('site.learner.nav.course'),
                'is_active' => ! GlobalRequest::is('account/course-webinar') && GlobalRequest::is('account/course*'),
            ],
            [
                'route_name' => 'learner.shop-manuscript',
                'fa-icon' => 'fa fa-file-alt',
                'label' => trans('site.learner.nav.manuscript'),
                'is_active' => GlobalRequest::is('account/shop-manuscript*'),
            ],
            [
                'route_name' => 'learner.coaching-time',
                'fa-icon' => 'fa fa-briefcase',
                'label' => trans('site.learner.nav.workshop'),
                'is_active' => GlobalRequest::is('account/coaching-time*'),
            ],
            [
                'route_name' => 'learner.webinar',
                'fa-icon' => 'fab fa-youtube',
                'label' => trans('site.learner.nav.webinars'),
                'is_active' => GlobalRequest::is('account/webinar*'),
            ],
            [
                'route_name' => 'learner.course-webinar',
                'fa-icon' => 'fab fa-youtube',
                'label' => trans('site.learner.nav.course-webinars'),
                'is_active' => GlobalRequest::is('account/course-webinar*'),
            ],
            [
                'route_name' => 'learner.assignment',
                'fa-icon' => 'fa fa-clipboard-list',
                'label' => trans('site.learner.nav.assignment'),
                'is_active' => GlobalRequest::is('account/assignment*'),
            ],
            [
                'route_name' => 'learner.calendar',
                'fa-icon' => 'fa fa-calendar-alt',
                'label' => trans('site.learner.nav.calendar'),
                'is_active' => GlobalRequest::is('account/calendar*'),
            ],
            /* [
                'route_name' => 'learner.document-converter',
                'fa-icon' => 'fa fa-file-word',
                'label' => trans('site.learner.documents-and-forms-text'),
                'is_active' => GlobalRequest::is('account/document-converter*'),
            ], */
            [
                'route_name' => 'learner.invoice',
                'fa-icon' => 'fa fa-file-invoice',
                'label' => trans('site.learner.nav.invoice'),
                'is_active' => GlobalRequest::is('account/invoice*'),
            ],
            [
                'route_name' => 'learner.upgrade',
                'fa-icon' => 'fa fa-cloud-upload-alt',
                'label' => trans('site.learner.nav.upgrade'),
                'is_active' => GlobalRequest::is('account/upgrade*'),
            ],
            [
                'route_name' => 'learner.private-message',
                'fa-icon' => 'fa fa-comment',
                'label' => trans('site.learner.nav.message'),
                'is_active' => GlobalRequest::is('account/private-message*'),
            ],
            [
                'route_name' => 'learner.profile',
                'fa-icon' => 'fa fa-users',
                'label' => trans('site.learner.nav.profile'),
                'is_active' => GlobalRequest::is('account/profile*'),
            ],
        ];

        return $navs;
    }

    /**
     * Pilot reader navigation
     *
     * @param  null  $route
     */
    public static function pilotReaderNav($route = null)
    {
        $navs = [
            ['route_name' => 'learner.book-author-book-show', 'label' => 'Contents'],
            ['route_name' => 'learner.book-author-book-settings', 'label' => 'Settings'],
            ['route_name' => 'learner.book-author-book-invitation', 'label' => 'Invitations'],
            ['route_name' => 'learner.book-author-book-track-readers', 'label' => 'Track Readers'],
            ['route_name' => 'learner.book-author-book-feedback-list', 'label' => 'Feedbacks'],
        ];

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;

    }

    public static function pilotReaderReaderNav($route = null)
    {
        $navs = [
            ['route_name' => 'learner.book-author-book-show', 'label' => 'Contents'],
            ['route_name' => 'learner.book-author-book-settings', 'label' => 'Settings'],
            ['route_name' => 'learner.book-author-book-reader-feedback-list', 'label' => 'My Feedback'],
        ];

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;
    }

    public static function pilotReaderDirectoryNav($route = null)
    {
        $navs = [
            ['route_name' => 'learner.reader-directory.index', 'label' => 'Search'],
            ['route_name' => 'learner.reader-directory.about', 'label' => 'About'],
            ['route_name' => 'learner.reader-directory.query-sent-list', 'label' => 'Sent Queries'],
            ['route_name' => 'learner.reader-directory.query-received-list', 'label' => 'Received Queries'],
        ];

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;

    }

    public static function pilotReaderProfileNav($route = null)
    {
        $navs = [
            ['route_name' => 'learner.pilot-reader.account.index', 'label' => 'Preferences'],
            ['route_name' => 'learner.pilot-reader.account.reader-profile', 'label' => 'Reader Profile'],
        ];

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;

    }

    public static function privateGroupsNav($route = null)
    {
        $navs = [
            ['route_name' => 'learner.private-groups.show', 'label' => 'Home'],
            ['route_name' => 'learner.private-groups.discussion', 'label' => 'Discussion'],
            ['route_name' => 'learner.private-groups.books', 'label' => 'Books'],
            ['route_name' => 'learner.private-groups.preferences', 'label' => 'Preferences'],
            ['route_name' => 'learner.private-groups.members', 'label' => 'Members'],
            ['route_name' => 'learner.private-groups.edit-group', 'label' => 'Edit Group'],
        ];

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;

    }

    /**
     * Check if user is member of the group
     */
    public static function isPrivateGroupMember($group_id, $user_id): int
    {
        $isMember = 0;
        $groupMember = PrivateGroupMember::where(['private_group_id' => $group_id, 'user_id' => $user_id])->first();
        if ($groupMember) {
            $isMember++;
        }

        return $isMember;
    }

    /**
     * Check if logged in user is reading the book
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function isReadingBook($book_id)
    {
        $readingBook = PilotReaderBookReading::where(['book_id' => $book_id, 'user_id' => \Auth::user()->id])
            ->whereIn('status', [0, 1])->first();

        return $readingBook;
    }

    /**
     * Count the total reader for certain status
     */
    public static function countReaderWithStatus($book_id, $status): int
    {
        return PilotReaderBookReading::withTrashed()->where(['book_id' => $book_id, 'status' => $status])->get()->count();
    }

    public static function getCoachingTimerPlanType($plan_type)
    {
        $type_text = '30 min';
        if ($plan_type == 1) {
            $type_text = '1 hr';
        }

        return $type_text;
    }

    /**
     * @param  $book  PilotReaderBook
     * @return int|string
     */
    public static function getChapterTitle($book, $chapter_id)
    {
        $chapterCount = 0;
        foreach ($book->chaptersOnly as $k => $ch) {
            if ($chapter_id == $ch->id) {
                $chapterCount = $k + 1;
            }
        }

        $settings = $book->settings;
        $chapter_title = $settings ? $settings->book_units : 'Chapter';

        // check if the chapter name exists
        $front = new FrontendHelpers;
        $chapterCount = $front->checkChapterNameByNumber($chapterCount);

        return $chapter_title.' '.$chapterCount;
    }

    /**
     * Check if the chapter with number already exists then iterate
     */
    public function checkChapterNameByNumber($number): int
    {

        $checkChapterName = PilotReaderBookChapter::where('title', '=', 'Chapter '.$number)->first();
        if ($checkChapterName) {
            $number += 1;

            return $this->checkChapterNameByNumber($number);
        } else {
            return $number;
        }
    }

    public static function countWords($words)
    {
        return str_word_count(strip_tags($words));
    }

    public static function getQuestionnaireTitle($book, $chapter_id)
    {
        $chapterCount = 0;
        foreach ($book->chapterQuestionnaire as $k => $ch) {
            if ($chapter_id == $ch->id) {
                $chapterCount = $k + 1;
            }
        }

        return 'Questionnaire '.$chapterCount;
    }

    /**
     * Change the chapter name if it's empty
     *
     * @param  null  $chapter_title
     */
    public static function changeChapterName($chapter_title, $chapter_key): ?string
    {
        $chapter_name = $chapter_title;
        if (! $chapter_title) {
            $chapter_name = 'Chapter '.$chapter_key;
        }

        return $chapter_name;
    }

    /**
     * Get the chapter version
     *
     * @param  $chapter  PilotReaderBookChapter
     * @return mixed
     */
    public static function getChapterVersionNumber($chapter)
    {
        return $chapter->versions->count();
    }

    /**
     * Get the current chapter version
     *
     * @param  $chapter  PilotReaderBookChapter
     * @return mixed
     */
    public static function getCurrentChapterVersion($chapter)
    {
        return $chapter->versions()->orderBy('id', 'desc')->first();
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

    /**
     * get the word count with margin
     */
    public static function wordCountByMargin($word_count, float $margin = 0.03): int
    {
        $calculatedWords = ceil($word_count * $margin);
        $newWordCount = $word_count - $calculatedWords;

        return $newWordCount;
    }

    /**
     * Type of assignment uploaded
     *
     * @param  null  $id
     * @return array|string
     */
    public static function assignmentType($id = null)
    {
        $genre = Genre::all();

        if ($id >= 0 && ! is_null($id)) {
            $genre = 'None';
            $findGenre = Genre::find($id);
            if ($id > 0 && $findGenre) {
                $genre = $findGenre->name;
            }
        }

        return $genre;
        /*$types = array(
            array( 'id' => 1, 'option' => 'Barnebok'),
            array( 'id' => 2, 'option' => 'Fantasy'),
            array( 'id' => 3, 'option' => 'Skjønnlitterært'),
            array( 'id' => 4, 'option' => 'Serieroman'),
            array( 'id' => 5, 'option' => 'Sakprosa'),
            array( 'id' => 6, 'option' => 'Selvbiografi'),
            array( 'id' => 7, 'option' => 'Krim'),
            array( 'id' => 8, 'option' => 'Thriller'),
            array( 'id' => 9, 'option' => 'Grøsser'),
            array( 'id' => 10, 'option' => 'Lyrikk'),
            array( 'id' => 11, 'option' => 'Ungdom'),
            array( 'id' => 12, 'option' => 'Dokumentar'),
            array( 'id' => 13, 'option' => 'Sci-fi'),
            array( 'id' => 14, 'option' => 'Dystopi'),
            array( 'id' => 15, 'option' => 'Valgfri'),
            array( 'id' => 16, 'option' => 'Feelgood'),
        );

        if ($id) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;*/
    }

    public static function formatAssignmentType($id)
    {
        $assignmentTypes = explode(', ', $id);
        $displayTypes = '';
        foreach ($assignmentTypes as $assignmentType) {
            $displayTypes .= self::assignmentType($assignmentType).', ';
        }

        return rtrim($displayTypes, ', ');
    }

    /**
     * Where could it be found in manuscript
     * Manuscript type for assignment either whole, start, middle or last part of the manuscript
     *
     * @param  null  $id
     */
    public static function manuscriptType($id = null)
    {
        $types = [
            ['id' => 1, 'option' => 'Hele manuset'],
            ['id' => 2, 'option' => 'Starten av manuset'],
            ['id' => 3, 'option' => 'Midten av manuset'],
            ['id' => 4, 'option' => 'Slutten av manuset'],
        ];

        if ($id) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;
    }

    /**
     * Feedback marks
     *
     * @param  null  $setMark
     */
    public static function feedbackMarks($setMark = null)
    {
        $marks = [
            ['option' => 'unmarked', 'label' => 'Unmarked'],
            ['option' => 'ignore', 'label' => 'Ignore'],
            ['option' => 'consider', 'label' => 'Consider'],
            ['option' => 'todo', 'label' => 'Todo'],
            ['option' => 'done', 'label' => 'Done'],
            ['option' => 'keep', 'label' => 'Keep'],
        ];

        if ($setMark) {
            foreach ($marks as $mark) {
                if ($mark['option'] == $setMark) {
                    return $mark['label'];
                }
            }
        }

        return $marks;
    }

    public static function howReadyOptions($ready = null)
    {
        $options = [
            ['id' => 1, 'text' => 'Ikke så veldig. Men jeg skal gi det et forsøk (og så har jeg jo alltids angrefristen ...)'],
            ['id' => 2, 'text' => 'Ganske motivert. Jeg vil gi dette et realt forsøk, men er usikker på om jeg vil klare å fullføre.'],
            ['id' => 3, 'text' => 'Jeg vil veldig gjerne være med på dette. Det er nå jeg skal klare det.'],
            ['id' => 4, 'text' => 'Gira? Jeg kan knapt vente til vi er i gang. Det er nå eller aldri. Jeg skal bli forfatter!'],
        ];

        if ($ready) {
            foreach ($options as $option) {
                if ($option['id'] == $ready) {
                    return $option;
                }
            }
        }

        return $options;
    }

    /**
     * Get the webinar key from the link
     *
     * @return mixed
     */
    public static function extractWebinarKeyFromLink($link)
    {
        $expURL = explode('/', $link);
        $extractKey = explode('?', end($expURL));

        return $extractKey[0];
    }

    public static function checkIfWebinarRegistrant($webinar_id, $user_id)
    {
        $registrant = WebinarRegistrant::where(['webinar_id' => $webinar_id, 'user_id' => $user_id])->first();
        if (! $registrant) {
            return false;
        }

        return true;
    }

    public static function getWebinarJoinURL($webinar_id, $user_id)
    {
        $registrant = WebinarRegistrant::where(['webinar_id' => $webinar_id, 'user_id' => $user_id])->first();
        if ($registrant) {
            return $registrant->join_url;
        }

        return false;
    }

    public static function checkJpegImg($image)
    {
        $getExtension = explode('.', $image);
        $extension = $getExtension[1];
        // check if jpeg file
        if ($extension == 'jpeg') {
            // if the jpeg can't be found replace it with jpg
            if (! \File::exists(public_path($image))) {
                $image = $getExtension[0].'.jpg';
            }
        }

        return $image;
    }

    /**
     * Payment modes check if vipps option should be included
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function paymentModes(bool $showVipps = false)
    {
        $mode = PaymentMode::query();
        if (! $showVipps) {
            $mode->where('id', '!=', 5);
        }

        return $mode->get();
    }

    public static function getFreeCourses()
    {
        return \App\Course::where('is_free', '=', 1)->where('status', 1)->get();
    }

    /**
     * Generate unique code
     */
    public static function generateUniqueCode(int $codeLength = 20): string
    {

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersNumber = strlen($characters);

        $code = '';

        while (strlen($code) < $codeLength) {
            $position = rand(0, $charactersNumber - 1);
            $character = $characters[$position];
            $code = $code.$character;
        }

        return $code;

    }

    public static function gitCards($giftCard = null)
    {
        $giftCards = [
            [
                'label' => trans('site.gift-card.christmas-present'),
                'name' => 'christmas',
                'image' => '/images-new/gift-cards/christmas.png',
            ],

            [
                'label' => trans('site.gift-card.birthday-present'),
                'name' => 'birthday',
                'image' => '/images-new/gift-cards/birthday.png',
            ],

            [
                'label' => trans('site.gift-card.giftcard-present'),
                'name' => 'gift-card',
                'image' => '/images-new/gift-cards/gift-card.png',
            ],

            [
                'label' => trans('site.gift-card.love-present'),
                'name' => 'love-present',
                'image' => '/images-new/gift-cards/love-present.png',
            ],
        ];

        if ($giftCard) {
            foreach ($giftCards as $gift) {
                if ($gift['name'] === $giftCard) {
                    return $gift;
                }
            }
        }

        return $giftCards;
    }

    /**
     * Get content from .doc file
     *
     * @return bool|string
     */
    public static function readWord($filename)
    {
        if (file_exists($filename)) {
            if (($fh = fopen($filename, 'r')) !== false) {
                $headers = fread($fh, 0xA00);

                // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                $n1 = (ord($headers[0x21C]) - 1);

                // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                $n2 = ((ord($headers[0x21D]) - 8) * 256);

                // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                $n3 = ((ord($headers[0x21E]) * 256) * 256);

                // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                $n4 = (((ord($headers[0x21F]) * 256) * 256) * 256);

                // Total length of text in the document
                $textLength = ($n1 + $n2 + $n3 + $n4);

                if ($textLength > 0) {
                    // $extracted_plaintext = fread($fh, $textLength);
                    $extracted_plaintext = fread($fh, filesize($filename));

                    // if you want to see your paragraphs in a new line, do this
                    // return nl2br($extracted_plaintext);
                    return $extracted_plaintext;
                }

                return false;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Separate get content from doc file, the other is used for word count
     */
    public static function getContentFromDocFile($filename)
    {
        if (file_exists($filename)) {
            if (($fh = fopen($filename, 'r')) !== false) {
                $headers = fread($fh, 0xA00);

                // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                $n1 = (ord($headers[0x21C]) - 1);

                // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                $n2 = ((ord($headers[0x21D]) - 8) * 256);

                // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                $n3 = ((ord($headers[0x21E]) * 256) * 256);

                // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                $n4 = (((ord($headers[0x21F]) * 256) * 256) * 256);

                // Total length of text in the document
                $textLength = ($n1 + $n2 + $n3 + $n4);

                if ($textLength > 0) {
                    $extracted_plaintext = fread($fh, $textLength);

                    // simple print character stream without new lines
                    // echo $extracted_plaintext;

                    // if you want to see your paragraphs in a new line, do this
                    $extracted_plaintext = nl2br($extracted_plaintext);
                    $breaks = ['<br />', '<br>', '<br/>']; // get break tags
                    $extracted_plaintext = str_ireplace($breaks, "\r\n", $extracted_plaintext); // replace break tags

                    return $extracted_plaintext;
                    // need more spacing after each paragraph use another nl2br
                }

                return false;
            }
        }
    }

    /**
     * Get the text between specified text
     */
    public static function getTextBetween($content, $start, $end): string
    {
        $r = explode($start, $content);
        if (isset($r[1])) {
            $r = explode($end, $r[1]);

            return $r[0];
        }

        return '';
    }

    /**
     * Get the staffs order by sequence 0 last
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getStaffs($role = 'staff')
    {
        // order by field zero comes last
        $staffs = Staff::where('role', $role)->orderByRaw('sequence = 0, sequence')->get();

        return $staffs;
    }

    public static function getNews()
    {
        return \App\Settings::where('setting_name', 'news')->first();
    }

    public static function userProject($user_id, $project_id)
    {
        return Project::where('user_id', $user_id)->where('id', $project_id)->firstOrFail();
    }

    public static function userHasPaidCourse()
    {
        $hasPaidCourse = false;
        if (! \Auth::guest()) {
            foreach (\Auth::user()->coursesTakenNotOld as $courseTaken) {
                if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                    if ($courseTaken->package->course->is_free != 1) {
                        $hasPaidCourse = true;
                        break;
                    }
                }
            }
        }

        return $hasPaidCourse;
    }

    public static function checkIfLearnerHasAccessToLesson($user_id, $course_id, $lesson_id)
    {
        $user = User::find($user_id);
        $course = Course::find($course_id);
        $lesson = Lesson::findOrFail($lesson_id);

        $courseTaken = $user->coursesTaken()->whereIn('package_id', $course->packages()->pluck('id'))->first();
        if (! $courseTaken) {
            return false;
        }

        return \App\Http\FrontendHelpers::isLessonAvailable($courseTaken->started_at, $lesson->delay, $lesson->period) ||
            \App\Http\FrontendHelpers::hasLessonAccess($courseTaken, $lesson);
    }

    public static function checkSelfPublishingPortalRequest($user_id)
    {
        return SelfPublishingPortalRequest::where('user_id', $user_id)->first();
    }

    public static function getLearnerStandardProject($user_id)
    {
        $user = User::find($user_id);

        return $user->standardProject();
    }

    public static function checkIfLearnerHasBookSale($project_id = null)
    {
        $learner = Auth::user();
        $project_id = $project_id ?? optional(FrontendHelpers::getLearnerStandardProject($learner->id))->id;

        if (! $project_id) {
            return collect();
        }

        return ProjectBookSale::leftJoin('project_books', 'project_book_sales.project_book_id', '=', 'project_books.id')
            ->select(
                DB::raw('SUM(amount) as amount_total'),
                DB::raw("DATE_FORMAT(date, '%m') as month")
            )
            ->whereYear('date', now()->year)
            ->where('project_id', $project_id)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public static function countFileWords($type, $request)
    {
        $extensions = ['docx'];

        $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
        $original_filename = $request->manuscript->getClientOriginalName();

        if (! in_array($extension, $extensions)) {
            return redirect()->back();
        }

        $time = time();

        $destinationPath = 'uploads/manuscript-compute/'; // upload path
        if (! \File::exists($destinationPath)) {
            \File::makeDirectory($destinationPath, 0777, true, true);
        }

        $fileName = $original_filename; // $time.'.'.$extension; // rename document
        $request->manuscript->move($destinationPath, $fileName);

        $file = $destinationPath.$fileName;

        $docObj = new FileToText($file);
        // count characters with space
        $char_count = strlen($docObj->convertToText()) - 2;
        $word_count = str_word_count($docObj->convertToText());

        return [
            'char_count' => $char_count,
            'word_count' => $word_count,
            'file' => $file,
            'original_filename' => $original_filename,
        ];
    }

    public static function saveFile(Request $request, $folder, $fieldName)
    {
        $filePath = null;

        if ($request->hasFile($fieldName)) {
            $destinationPath = 'storage/'.$folder; // upload path

            self::createDirectory($destinationPath);

            $requestFile = \request()->file($fieldName);
            $extension = $requestFile->getClientOriginalExtension();
            $original_filename = $requestFile->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $fileName = self::checkFileName($destinationPath, $actual_name, $extension); // rename document
            $requestFile->move($destinationPath, $fileName);

            return '/'.$fileName;

        }

        return $filePath;
    }

    public static function createDirectory($name)
    {
        if (! \Illuminate\Support\Facades\File::exists($name)) {
            \Illuminate\Support\Facades\File::makeDirectory($name);
        }
    }

    public static function checkFileName($path, $filename, $extension)
    {
        $i = 1;

        // check first if the filename without the increment exists
        if (file_exists("$path/$filename.$extension")) {
            while (file_exists("$path/$filename ($i).$extension")) {
                $i++;
            }
            $newName = "$path/$filename ($i).$extension";
        } else {
            $newName = "$path/$filename.$extension";
        }

        return $newName;
    }

    public static function manuscriptExcessPerWordPrice()
    {
        $excessPerWordAmount = Settings::getDetailsByName('manuscript-excess-per-word-amount');

        return $excessPerWordAmount->setting_value;
    }

    public static function getLearnerSaleYear()
    {
        $learner = Auth::user();

        $uniqueYears = ProjectBookSale::selectRaw('YEAR(date) as year')
            ->leftJoin('project_books', 'project_book_sales.project_book_id', '=', 'project_books.id')
            ->where('user_id', $learner->id)
            ->distinct()
            ->pluck('year');

        $firstYear = $uniqueYears->first() ?? Carbon::now()->year;

        return $firstYear;
    }

    /**
     * get the order details from svea
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public static function sveaOrderDetails($svea_order_id)
    {
        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        // set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::PROD_ADMIN_BASE_URL;

        $connector = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);

        try {
            /**
             * Create Connector object
             *
             * Exception \Svea\Checkout\Exception\SveaConnectorException will be returned if
             * some of fields $merchantId, $sharedSecret and $baseUrl is missing
             *
             *
             * Deliver Order
             *
             * Possible Exceptions are:
             * \Svea\Checkout\Exception\SveaInputValidationException
             * \Svea\Checkout\Exception\SveaApiException
             * \Exception - for any other error
             */
            $conn = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
            $checkoutClient = new \Svea\Checkout\CheckoutAdminClient($conn);
            $data = [
                'orderId' => (int) $svea_order_id,
            ];

            $response = $checkoutClient->getOrder($data);

            return $response;
        } catch (\Svea\Checkout\Exception\SveaApiException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Svea\Checkout\Exception\SveaConnectorException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Svea\Checkout\Exception\SveaInputValidationException $ex) {
            return response()->json($ex->getMessage(), 400);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    public static function extractTextFromDocx($filePath)
    {
        $text = '';

        // Open the DOCX file as a ZIP archive
        $zip = new ZipArchive;
        if ($zip->open($filePath) === true) {
            // Locate document.xml inside the DOCX archive
            $xmlIndex = $zip->locateName('word/document.xml');

            if ($xmlIndex !== false) {
                // Extract XML content
                $xmlData = $zip->getFromIndex($xmlIndex);
                $zip->close();

                // Remove XML tags and extract plain text
                $text = strip_tags($xmlData);

                // Convert XML entities to readable text
                $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
            } else {
                return [
                    'error' => 'Error: document.xml not found in DOCX file',
                    'word_count' => 0,
                ];
            }
        } else {
            return [
                'error' => 'Error: Unable to open DOCX file',
                'word_count' => 0,
            ];
        }

        // Count words
        $wordCount = FrontendHelpers::countWordsLikeOpenOffice($text);

        return [
            'text' => nl2br(e($text)), // Convert new lines to <br> for display
            'word_count' => $wordCount,
        ];
    }

    public static function countWordsLikeOpenOffice($text)
    {
        // Remove HTML tags
        $text = strip_tags($text);

        // Normalize whitespace and remove special characters
        // $text = preg_replace('/[\r\n\t]+/', ' ', $text);
        // $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text); // Remove non-word characters except hyphens

        // Trim extra spaces
        $text = trim($text);

        // Count words using whitespace as delimiter
        $words = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        return count($words);
    }

    public static function parseShortcodes($content)
    {
        return preg_replace_callback('/\[video\s+([^\]]+)\]/i', function ($matches) {
            $attributes = [];
            preg_match_all('/(\w+)="([^"]+)"/', $matches[1], $attr_matches, PREG_SET_ORDER);

            foreach ($attr_matches as $attr) {
                $attributes[$attr[1]] = $attr[2];
            }

            $src = isset($attributes['src']) ? htmlspecialchars($attributes['src'], ENT_QUOTES) : '';
            $width = isset($attributes['width']) ? intval($attributes['width']) : 600;
            $height = isset($attributes['height']) ? intval($attributes['height']) : 300;

            if (! $src) {
                return '';
            } // invalid shortcode with no src

            return '<iframe width="'.$width.'" height="'.$height.'" src="'.$src.'" frameborder="0" allowfullscreen allow="autoplay; fullscreen"></iframe>';
        }, $content);
    }
}
