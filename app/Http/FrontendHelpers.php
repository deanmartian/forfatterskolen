<?php

namespace App\Http;
use App\Advisory;
use App\Course;
use App\PaymentMode;
use App\PilotReaderBook;
use App\PilotReaderBookChapter;
use App\PilotReaderBookReading;
use App\PrivateGroupMember;
use App\Staff;
use App\WebinarRegistrant;
use Carbon\Carbon;

class FrontendHelpers
{
	public static function InCart($key, $value)
	{
        $in_cart = array_search($value, array_column(self::cart(), $key)); // Check if already in cart
        
        if( $in_cart === FALSE ) :
        	return false;
       	endif;

       	return true;
	}




	public static function cartIndex($arr_key, $arr_value)
	{
		$index = NULL;

		foreach(self::cart() as $key => $value){
	        if(is_array($value) && $value[$arr_key] == $arr_value) :
	        	$index = $key;
	        endif;
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
		return 'Kr ' . number_format($value, 2, ",", ".");
	}

    public static function formatCurrency($value)
    {
        return number_format($value, 2, ",", "");
    }

	public static function lessonAvailability($startedAt, $delay, $period)
	{
		if( empty($startedAt) ) return 'Course not started';
		$availableOn = Carbon::parse($startedAt);

		if(self::isDate($delay)) :
			$availableOn = date_create($delay);
		else :
			$availableOn->addDays($delay);
		endif;

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
		if( empty($startedAt) ) return 'Course not started';
		$availableOn = strtotime(self::lessonAvailability($startedAt, $delay, $period));
		$now = time();
		return $availableOn <= $now;
	}


	public static function hasLessonAccess($course_taken, $lesson)
	{
		$access_lessons = $course_taken ? $course_taken->access_lessons : []; //$course_taken->access_lessons
		return ( in_array($lesson->id, $access_lessons) );
	}


	public static function isCourseAvailable($course)
	{
		if( $course->start_date || $course->end_date ) :
			$now = time();
			if( $course->start_date ) :
				if( $now < strtotime($course->start_date)) return false;
			endif;
			if( $course->end_date ) :
				if( $now > strtotime($course->end_date)) return false;
			endif;
		endif;
		return true;
	}

    /**
     * Check if course is active
     * @param $course
     * @return bool
     */
    public static function isCourseActive($course)
    {

        if (!$course->status) {
            return false;
        }
        return true;
	}



	public static function isWebinarAvailable($webinar)
	{
		$now = time();
		if( $now < strtotime($webinar->start_date) ) :
			return false;
		endif;
		return true;
	}

    public static function isWebinarAvailablePlusHour($webinar)
    {
        $now = time();
        if( $now < (strtotime($webinar->start_date) + 60*60) ) :
            return false;
        endif;
        return true;
    }

	public static function isCourseTakenAvailable($courseTaken)
	{
		if( $courseTaken->start_date || $courseTaken->end_date ) :
			$now = time();
			if( $courseTaken->start_date ) :
				if( $now < strtotime($courseTaken->start_date)) return false;
			endif;
			if( $courseTaken->end_date ) :
				if( $now > strtotime($courseTaken->end_date)) return false;
			endif;
		endif;
		return true;
	}

    public static function roundUpToNearestMultiple($n, $increment = 1000)
    {
        return (int) ($increment * ceil($n / $increment));
    }

    public static function convertMonthLanguage($month_number = NULL)
    {
        $monthNames = array(
            array( 'id' => 1, 'option' => 'januar'),
            array( 'id' => 2, 'option' => 'februar'),
            array( 'id' => 3, 'option' => 'mars'),
            array( 'id' => 4, 'option' => 'april'),
            array( 'id' => 5, 'option' => 'mai'),
            array( 'id' => 6, 'option' => 'juni'),
            array( 'id' => 7, 'option' => 'juli'),
            array( 'id' => 8, 'option' => 'august'),
            array( 'id' => 9, 'option' => 'september'),
            array( 'id' => 10, 'option' => 'oktober'),
            array( 'id' => 11, 'option' => 'november'),
            array( 'id' => 12, 'option' => 'desember'),
        );

        if ($month_number) {
            foreach ($monthNames as $monthName) {
                if ($monthName['id'] == $month_number) {
                    return $monthName['option'];
                }
            }
        }

        return NULL;
	}

    public static function convertDayLanguage($day_number = NULL)
    {
        $dayNumbers = array(
            array( 'id' => 1, 'option' => 'mandag'),
            array( 'id' => 2, 'option' => 'tirsdag'),
            array( 'id' => 3, 'option' => 'onsdag'),
            array( 'id' => 4, 'option' => 'torsdag'),
            array( 'id' => 5, 'option' => 'fredag'),
            array( 'id' => 6, 'option' => 'lørdag'),
            array( 'id' => 7, 'option' => 'søndag')
        );

        if ($day_number) {
            foreach ($dayNumbers as $dayNumber) {
                if ($dayNumber['id'] == $day_number) {
                    return $dayNumber['option'];
                }
            }
        }

        return NULL;
    }

    /**
     * List of front pages and the route name
     * @return array
     */
    public static function frontPageList()
    {
        return array(
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
        );
    }

    public static function getShopManuscriptAdvisory()
    {
        return Advisory::getShopManuscriptAdvisory();
    }

    /**
     * Pilot reader navigation
     * @param null $route
     * @return array
     */
    public static function pilotReaderNav($route = NULL)
    {
        $navs = array(
            array( 'route_name' => 'learner.book-author-book-show', 'label' => 'Contents'),
            array( 'route_name' => 'learner.book-author-book-settings', 'label' => 'Settings'),
            array( 'route_name' => 'learner.book-author-book-invitation', 'label' => 'Invitations'),
            array( 'route_name' => 'learner.book-author-book-track-readers', 'label' => 'Track Readers'),
            array( 'route_name' => 'learner.book-author-book-feedback-list', 'label' => 'Feedbacks'),
        );

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;

    }

    public static function pilotReaderReaderNav($route = NULL)
    {
        $navs = array(
            array( 'route_name' => 'learner.book-author-book-show', 'label' => 'Contents'),
            array( 'route_name' => 'learner.book-author-book-settings', 'label' => 'Settings'),
            array( 'route_name' => 'learner.book-author-book-reader-feedback-list', 'label' => 'My Feedback')
        );

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;
    }

    public static function pilotReaderDirectoryNav($route = NULL)
    {
        $navs = array(
            array( 'route_name' => 'learner.reader-directory.index', 'label' => 'Search'),
            array( 'route_name' => 'learner.reader-directory.about', 'label' => 'About'),
            array( 'route_name' => 'learner.reader-directory.query-sent-list', 'label' => 'Sent Queries'),
            array( 'route_name' => 'learner.reader-directory.query-received-list', 'label' => 'Received Queries'),
        );

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;

    }

    public static function pilotReaderProfileNav($route = NULL)
    {
        $navs = array(
            array( 'route_name' => 'learner.pilot-reader.account.index', 'label' => 'Preferences' ),
            array( 'route_name' => 'learner.pilot-reader.account.reader-profile', 'label' => 'Reader Profile' )
        );

        if ($navs) {
            foreach ($navs as $nav) {
                if ($nav['route_name'] == $route) {
                    return $nav['label'];
                }
            }
        }

        return $navs;

    }

    public static function privateGroupsNav($route = NULL)
    {
        $navs = array(
            array( 'route_name' => 'learner.private-groups.show', 'label' => 'Home' ),
            array( 'route_name' => 'learner.private-groups.discussion', 'label' => 'Discussion' ),
            array( 'route_name' => 'learner.private-groups.books', 'label' => 'Books' ),
            array( 'route_name' => 'learner.private-groups.preferences', 'label' => 'Preferences' ),
            array( 'route_name' => 'learner.private-groups.members', 'label' => 'Members' ),
            array( 'route_name' => 'learner.private-groups.edit-group', 'label' => 'Edit Group' )
        );

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
     * @param $group_id
     * @param $user_id
     * @return int
     */
    public static function isPrivateGroupMember($group_id, $user_id)
    {
        $isMember = 0;
        $groupMember = PrivateGroupMember::where(['private_group_id' => $group_id,'user_id' => $user_id])->first();
        if ($groupMember) {
            $isMember++;
        }

        return $isMember;
    }

    /**
     * Check if logged in user is reading the book
     * @param $book_id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function isReadingBook($book_id)
    {
        $readingBook = PilotReaderBookReading::where(['book_id' => $book_id, 'user_id' => \Auth::user()->id])
            ->whereIn('status',[0,1])->first();
        return $readingBook;
    }

    /**
     * Count the total reader for certain status
     * @param $book_id
     * @param $status
     * @return int
     */
    public static function countReaderWithStatus($book_id, $status)
    {
        return PilotReaderBookReading::withTrashed()->where(['book_id' => $book_id, 'status' => $status ])->get()->count();
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
     * @param $book PilotReaderBook
     * @param $chapter_id
     * @return int|string
     */
    public static function getChapterTitle($book, $chapter_id)
    {
        $chapterCount = 0;
        foreach ($book->chaptersOnly as $k=>$ch) {
            if ($chapter_id == $ch->id) {
                $chapterCount = $k+1;
            }
        }

        $settings = $book->settings;
        $chapter_title = $settings ? $settings->book_units:'Chapter';

        // check if the chapter name exists
        $front = new FrontendHelpers();
        $chapterCount = $front->checkChapterNameByNumber($chapterCount);


        return $chapter_title.' '.$chapterCount;
    }

    /**
     * Check if the chapter with number already exists then iterate
     * @param $number
     * @return int
     */
    public function checkChapterNameByNumber($number)
    {

        $checkChapterName = PilotReaderBookChapter::where('title','=', 'Chapter '.$number)->first();
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
        foreach ($book->chapterQuestionnaire as $k=>$ch) {
            if ($chapter_id == $ch->id) {
                $chapterCount = $k+1;
            }
        }

        return 'Questionnaire '.$chapterCount;
    }

    /**
     * Change the chapter name if it's empty
     * @param null $chapter_title
     * @param $chapter_key
     * @return null|string
     */
    public static function changeChapterName($chapter_title = NULL, $chapter_key)
    {
        $chapter_name = $chapter_title;
        if (!$chapter_title) {
            $chapter_name = 'Chapter '.$chapter_key;
        }

        return $chapter_name;
    }

    /**
     * Get the chapter version
     * @param $chapter PilotReaderBookChapter
     * @return mixed
     */
    public static function getChapterVersionNumber($chapter)
    {
        return $chapter->versions->count();
    }

    /**
     * Get the current chapter version
     * @param $chapter PilotReaderBookChapter
     * @return mixed
     */
    public static function getCurrentChapterVersion($chapter)
    {
        return $chapter->versions()->orderBy('id', 'desc')->first();
    }


	public static function FikenConnect($url)
	{
		$username = "cleidoscope@gmail.com";
	    $password = "moonfang";
	    $headers = [];
	    $headers[] = 'Accept: application/hal+json, application/vnd.error+json';
	    $headers[] = 'Content-Type: application/hal+json';

		$ch = curl_init($url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $data = curl_exec($ch);
		$data = json_decode($data);

		return $data;
	}


	
	public static function get_num_of_words($string) {
	    $string = preg_replace('/\s+/', ' ', trim($string));
	    $words = explode(" ", strip_tags($string));
	    return count($words);
	}

    /**
     * get the word count with margin
     * @param $word_count
     * @param float $margin
     * @return int
     */
    public static function wordCountByMargin( $word_count, $margin = 0.03 )
    {
        $calculatedWords = ceil($word_count * $margin);
        $newWordCount = $word_count - $calculatedWords;
        return $newWordCount;
	}

    /**
     * Type of assignment uploaded
     * @param null $id
     * @return array|string
     */
    public static function assignmentType($id = NULL)
    {
        $types = array(
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
        );

        if ($id) {
            foreach ($types as $type) {
                if ($type['id'] == $id) {
                    return $type['option'];
                }
            }
        }

        return $types;
	}

    public static function formatAssignmentType($id)
    {
        $assignmentTypes = explode(', ',$id);
        $displayTypes = '';
        foreach ($assignmentTypes as $assignmentType) {
            $displayTypes .= self::assignmentType($assignmentType).', ';
        }
        return rtrim($displayTypes, ', ');
	}

    /**
     * Where could it be found in manuscript
     * Manuscript type for assignment either whole, start, middle or last part of the manuscript
     * @param null $id
     * @return array
     */
    public static function manuscriptType($id = NULL)
    {
        $types = array(
            array( 'id' => 1, 'option' => 'Hele manuset'),
            array( 'id' => 2, 'option' => 'Starten av manuset'),
            array( 'id' => 3, 'option' => 'Midten av manuset'),
            array( 'id' => 4, 'option' => 'Slutten av manuset'),
        );

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
     * @param null $setMark
     * @return array
     */
    public static function feedbackMarks($setMark = NULL)
    {
        $marks = array(
            array( 'option' => 'unmarked', 'label' => 'Unmarked'),
            array( 'option' => 'ignore', 'label' => 'Ignore'),
            array( 'option' => 'consider', 'label' => 'Consider'),
            array( 'option' => 'todo', 'label' => 'Todo'),
            array( 'option' => 'done', 'label' => 'Done'),
            array( 'option' => 'keep', 'label' => 'Keep'),
        );

        if ($setMark) {
            foreach ($marks as $mark) {
                if ($mark['option'] == $setMark) {
                    return $mark['label'];
                }
            }
        }

        return $marks;
	}

    public static function howReadyOptions($ready = NULL)
    {
        $options = array(
            array( 'id' => 1, 'text' => 'Ikke så veldig. Men jeg skal gi det et forsøk (og så har jeg jo alltids angrefristen ...)'),
            array( 'id' => 2, 'text' => 'Ganske motivert. Jeg vil gi dette et realt forsøk, men er usikker på om jeg vil klare å fullføre.'),
            array( 'id' => 3, 'text' => 'Jeg vil veldig gjerne være med på dette. Det er nå jeg skal klare det.'),
            array( 'id' => 4, 'text' => 'Gira? Jeg kan knapt vente til vi er i gang. Det er nå eller aldri. Jeg skal bli forfatter!'),
        );

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
     * @param $link
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
        if (!$registrant) {
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
            if (!\File::exists(public_path($image))) {
                $image = $getExtension[0].'.jpg';
            }
        }

        return $image;
	}

    /**
     * Payment modes check if vipps option should be included
     * @param bool $showVipps
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function paymentModes($showVipps = false)
    {
        $mode = PaymentMode::query();
        if (!$showVipps) {
            $mode->where('id', '!=', 5);
        }
        return $mode->get();
	}

    /**
     * Generate unique code
     * @param int $codeLength
     * @return string
     */
    public static function generateUniqueCode($codeLength = 20)
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
                'label' => 'Christmas Present',
                'name' => 'christmas',
                'image' => '/images-new/gift-cards/christmas.png'
            ],

            [
                'label' => 'Birthday Present',
                'name' => 'birthday',
                'image' => '/images-new/gift-cards/birthday.png'
            ],

            [
                'label' => 'Giftcard Present',
                'name' => 'gift-card',
                'image' => '/images-new/gift-cards/gift-card.png'
            ],

            [
                'label' => 'Love Present',
                'name' => 'love-present',
                'image' => '/images-new/gift-cards/love-present.png'
            ]
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
     * @param $filename
     * @return bool|string
     */
    public static function readWord($filename)
    {
        if(file_exists($filename))
        {
            if(($fh = fopen($filename, 'r')) !== false )
            {
                $headers = fread($fh, 0xA00);

                // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                $n1 = ( ord($headers[0x21C]) - 1 );

                // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

                // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

                // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

                // Total length of text in the document
                $textLength = ($n1 + $n2 + $n3 + $n4);

                if ($textLength > 0) {
                    //$extracted_plaintext = fread($fh, $textLength);
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
     * @param $filename
     * @return string
     */
    public static function getContentFromDocFile($filename)
    {
        if(file_exists($filename))
        {
            if(($fh = fopen($filename, 'r')) !== false )
            {
                $headers = fread($fh, 0xA00);

                // 1 = (ord(n)*1) ; Document has from 0 to 255 characters
                $n1 = ( ord($headers[0x21C]) - 1 );

                // 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
                $n2 = ( ( ord($headers[0x21D]) - 8 ) * 256 );

                // 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
                $n3 = ( ( ord($headers[0x21E]) * 256 ) * 256 );

                // 1 = (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
                $n4 = ( ( ( ord($headers[0x21F]) * 256 ) * 256 ) * 256 );

                // Total length of text in the document
                $textLength = ($n1 + $n2 + $n3 + $n4);

                if ($textLength > 0) {
                    $extracted_plaintext = fread($fh, $textLength);

                    // simple print character stream without new lines
                    //echo $extracted_plaintext;

                    // if you want to see your paragraphs in a new line, do this
                    $extracted_plaintext = nl2br($extracted_plaintext);
                    $breaks = array("<br />","<br>","<br/>"); // get break tags
                    $extracted_plaintext = str_ireplace($breaks, "\r\n", $extracted_plaintext); //replace break tags
                    return $extracted_plaintext;
                    // need more spacing after each paragraph use another nl2br
                }

                return false;
            }
        }
    }

    /**
     * Get the text between specified text
     * @param $content
     * @param $start
     * @param $end
     * @return string
     */
    public static function getTextBetween($content,$start,$end){
        $r = explode($start, $content);
        if (isset($r[1])){
            $r = explode($end, $r[1]);
            return $r[0];
        }
        return '';
    }

    /**
     * Get the staffs order by sequence 0 last
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getStaffs()
    {
        // order by field zero comes last
        $staffs = Staff::orderByRaw('sequence = 0, sequence')->get();
        return $staffs;
    }
}