<?php
namespace App\Http\Controllers\Frontend;

use App\Blog;
use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\CoursesTaken;
use App\EmailConfirmation;
use App\FreeWebinar;
use App\Helpers\Citrix;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Invoice;
use App\Mail\DiscussionEmail;
use App\Mail\DiscussionRepliesEmail;
use App\OptIn;
use App\PaymentMode;
use App\PaymentPlan;
use App\PilotReaderBook;
use App\PilotReaderBookReading;
use App\PilotReaderBookSettings;
use App\PublisherBook;
use App\Settings;
use App\Solution;
use App\SolutionArticle;
use App\SosChildren;
use App\UserEmail;
use App\Workshop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Course;
use App\FreeCourse;
use App\Package;
use App\Faq;
use App\Http\FikenInvoice;
use Illuminate\Support\Facades\Mail;

include_once($_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php');

class HomeController extends Controller
{

    protected $sosChildren = '';

    public function __construct(SosChildren $sosChildren)
    {
        $this->sosChildren = $sosChildren;
    }

    public function index()
    {
        $popular_courses = Course::where('display_order','>',0)->orderBy('display_order', 'asc')->limit(3)->get();
        $free_courses = FreeCourse::orderBy('created_at', 'desc')->get();
        $free_webinars = FreeWebinar::all();

        $webinar_pakke = Course::find(17);
        $next_webinar = $webinar_pakke->webinars()->where('start_date', '>=' ,Carbon::today())
            ->where('set_as_replay', 0)->first();
        $next_free_webinar = FreeWebinar::where('start_date', '>=' ,Carbon::today())->first();
        // check for workshop that has menu and is for sale and date is greater than equal to today
        $next_workshop = Workshop::has('menus')->where('date', '>=', Carbon::today())
            ->where('is_free', '=', 0)
            ->orderBy('date', 'ASC')->first();

        $latest_blog = Blog::orderBy('created_at', 'desc')->first();

        return view('frontend.home', compact('popular_courses', 'free_courses', 'free_webinars',
            'next_webinar', 'next_free_webinar', 'next_workshop','latest_blog'));
    }

    // set cookie for gdpr
    public function agreeGdpr()
    {
        $cookie_name = "_gdpr";
        $cookie_value = 1;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 365), "/"); // 86400 = 1 day
    }



    public function contact_us(Request $request)
    {
        if ($request->isMethod('post')) {

            $validates = [
                'g-recaptcha-response' => 'required|captcha'
            ];

            // validate the post request
            $this->validate($request, $validates);

            $email_content = 'From: '.$request->fullname."<br/>";
            $email_content .= 'Email: '.$request->email."<br/>";
            $email_content .= 'Message: '.$request->message;

            $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            //mail('post@forfatterskolen.no', 'Inquiry Message', $email_content, $headers);
            AdminHelpers::send_email('Inquiry Message','post@forfatterskolen.no','post@forfatterskolen.no', $email_content);
        }
        return view('frontend.contact-us');
    }


    public function faq()
    {
        $faqs = Faq::orderBy('created_at', 'asc')->get();
        return view('frontend.faq', compact('faqs'));
    }

    /**
     * Display support page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function support()
    {
        $solutions = Solution::where('is_instruction',0)->get();
        $instructions = Solution::where('is_instruction',1)->get();
        return view('frontend.solution', compact('solutions', 'instructions'));
    }

    public function children()
    {
        $primaryVideo = $this->sosChildren->getPrimaryVideo();
        $videoRecords = $this->sosChildren->getVideoRecords();
        $mainDescription = $this->sosChildren->getMainDescription();
        return view('frontend.children', compact('primaryVideo', 'videoRecords', 'mainDescription'));
    }

    /**
     * Display publishing records
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function publishing()
    {
        $books = PublisherBook::select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->orderBy('display_order', 'asc')->get();
        return view('frontend.publishing', compact('books'));
    }

    /**
     * Display all blog
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /*public function blog()
    {
        $blogs = Blog::orderBy('created_at','DESC')->get();

        return view('frontend.blog', compact('blogs'));
    }*/

    public function blog(Request $request)
    {
        $mainBlog = Blog::orderBy('created_at','DESC')->first();
        $blogs = Blog::where('id','!=', $mainBlog->id)
            ->orderBy('created_at','DESC')
            ->simplePaginate(4);

        // check if ajax to display the page without loading
        if ($request->ajax()) {
            return response()->json(\View::make('frontend.blog-post', array('blogs' => $blogs))->render());
        }

        return view('frontend.blog-new', compact('mainBlog','blogs'));
    }

    /**
     * Display the blog content
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function readBlog($id)
    {
        if($blog = Blog::find($id)) {
            return view('frontend.blog-read', compact('blog'));
        }

        return redirect()->route('front.blog');
    }

    /**
     * Display copy editing page or calculate
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function copyEditing(Request $request)
    {
        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
                $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $time = time();
                $destinationPath = 'storage/manuscript-tests/'; // upload path
                $fileName = $original_filename; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $file = $destinationPath.$fileName;

                $docObj = new FileToText($file);
                // count characters with space
                $word_count = strlen($docObj->convertToText()) - 2;

                /*if (\File::exists($destinationPath.$fileName)) {
                    \File::delete($destinationPath.$fileName);
                }*/

                $word_per_price = 1000;
                $price_per_word = 30;
                $rounded_word = FrontendHelpers::roundUpToNearestMultiple($word_count);

                $calculated_price = ($rounded_word/$word_per_price) * $price_per_word;

                session([
                    'os_price'          => $calculated_price,
                    'os_file_location'  => $file,
                    'os_file_name'      => $original_filename,
                    'os_product_id'     => 599886093,
                    'os_is_copy_editing' => 1
                ]);

                $message = $word_count.' TEGN <br />
                    <h3  class="no-margin-top">'.number_format($calculated_price, 2).' kr</h3>
                    <a href="'.route('front.other-service-checkout', ['plan' => 1, 'has_data' => 1]).'">Bestill</a>
                    ';
                return redirect()->back()->with('compute_manuscript', $message);
            endif;
        }
        return view('frontend.copy-editing');
    }

    public function otherServices()
    {
        return view('frontend.other-services');
    }

    /**
     * Display the
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function otherServiceCheckout($plan, $has_data = 0, Request $request)
    {

        if (!$has_data) {
            $unset_datas = ['os_price', 'os_file_location', 'os_file_name'];
            foreach($unset_datas as $unset_data) {
                session()->forget($unset_data);
            }
        }

        $title = $plan == 1 ? 'Språkvask' : 'Korrektur';
        $product_id = $plan == 1 ? 599886093 : 599110997;
        $is_copy_editing = $plan == 1 ? 1 : 0;
        session([
            'os_product_id' => $product_id,
            'os_is_copy_editing' => $is_copy_editing
        ]);

        $data = [
            'price'         => session('os_price'),
            'file_location' => session('os_file_location'),
            'plan_id'       => $plan,
            'title'         => $title,
            'file_name'     => session('os_file_name')
        ];

        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
                $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $destinationPath = 'storage/manuscript-tests/'; // upload path
                $fileName = $original_filename; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $file = $destinationPath.$fileName;

                $docObj = new FileToText($file);
                // count characters with space
                $word_count = strlen($docObj->convertToText()) - 2;
                $word_per_price = 1000;
                $price_per_word = $plan == 1 ? 35 : 30;
                $rounded_word = FrontendHelpers::roundUpToNearestMultiple($word_count);

                $calculated_price = ($rounded_word/$word_per_price) * $price_per_word;

                $data['price'] = $calculated_price;
                $data['file_location'] = $file;
                $data['file_name'] = $original_filename;

            endif;
        }

        return view('frontend.other-service-checkout', compact('data'));
    }

    /**
     * Process order for other service
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function otherServiceOrder(Request $request)
    {
        $data           = $request->except('_token');
        $file           = explode('/', $data['file_location']);
        $fileName       = $file[2];
        $destination    = 'storage/correction-manuscripts/';
        $time           = time();
        $getExtension   = explode('.', $fileName);
        $extension      = $getExtension[1];

        if (session('os_is_copy_editing') == 1) {
            $destination = 'storage/copy-editing-manuscripts/';
        }

        $newFileLocation = $destination.$time.'.'.$extension;

        // move the file from manuscript-tests to shop-manuscripts
        \File::move($data['file_location'], $newFileLocation);

        $title = 'Korrektur';
        $productID = session('os_product_id');
        if (session('os_is_copy_editing') == 1) {
            $title = 'Språkvask';
        }


        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail(6);
        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

        $comment = '(Manuskript: ' . $title . ', ';
        $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
        $comment .= 'Betalingsplan: 14 dager)';

        $dueDate = date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);

        $dueDate->addDays(14);

        $dueDate = date_format(date_create($dueDate), 'Y-m-d');
        $price = $data['price'] * 100;
        $user = Auth::user();

        $invoice_fields = [
            'user_id'       => Auth::user()->id,
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'netAmount'     => $price,
            'dueDate'       => $dueDate,
            'description'   => 'Kursordrefaktura',
            'productID'     => $productID,
            'email'         => $user->email,
            'telephone'     => $user->telephone,
            'address'       => $user->street,
            'postalPlace'   => $user->city,
            'postalCode'    => $user->zip,
            'comment'       => $comment,
        ];

        $invoice = new FikenInvoice();
        $invoice->create_invoice($invoice_fields);

        if (session('os_is_copy_editing') == 1) {
            CopyEditingManuscript::create([
                'user_id' => Auth::user()->id,
                'file'  => $newFileLocation,
                'payment_price' => $data['price']
            ]);
        } else {
            CorrectionManuscript::create([
                'user_id' => Auth::user()->id,
                'file'  => $newFileLocation,
                'payment_price' => $data['price']
            ]);
        }


        if( $paymentMode->mode == "Paypal" ) :
            echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price/100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
            echo '<script>document.getElementById("paypal_form").submit();</script>';
            return;
        endif;

        return redirect(route('front.simple.thankyou'));

    }

    public function thankyou()
    {
        return view('frontend.thank-you');
    }

    /**
     * Display correction page or calculate
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function correction(Request $request)
    {
        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
                $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $time = time();
                $destinationPath = 'storage/manuscript-tests/'; // upload path
                $fileName = $original_filename;//$time.'.'.$extension; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $file = $destinationPath.$fileName;

                $docObj = new FileToText($file);
                // count characters with space
                $word_count = strlen($docObj->convertToText()) - 2;

                /*if (\File::exists($destinationPath.$fileName)) {
                    \File::delete($destinationPath.$fileName);
                }*/


                $word_per_price = 1000;
                $price_per_word = 25;
                $rounded_word = FrontendHelpers::roundUpToNearestMultiple($word_count);

                $calculated_price = ($rounded_word/$word_per_price) * $price_per_word;

                session([
                    'os_price'          => $calculated_price,
                    'os_file_location'  => $file,
                    'os_file_name'      => $original_filename,
                    'os_product_id'     => 599110997,
                    'os_is_copy_editing' => 0
                ]);

                $message = $word_count.' TEGN <br />
                    <h3  class="no-margin-top">'.number_format($calculated_price, 2).' kr</h3>
                    <a href="'.route('front.other-service-checkout',['plan' => 2, 'has_data' => 1]).'">Bestill</a>';
                return redirect()->back()->with('compute_manuscript', $message);
            endif;
        }
        return view('frontend.correction');
    }

    public function coachingTimer(Request $request)
    {
        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
                $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if( !in_array($extension, $extensions) ) :
                    return redirect()->back();
                endif;

                $time = time();
                $destinationPath = 'storage/manuscript-tests/'; // upload path
                $fileName = $time.'.'.$extension; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText= $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);

                $word_7500_price    = 690;
                $excess_word        = 0;
                $excess_word_price  = 0;

                // the initial calculated word is 7500 if excess then calculate the total excess price
                if ($word_count > 7500) {
                    $excess_word = $word_count - 7500;
                    // 69 is the price for every 1250 that is excess
                    $excess_word_price = ceil($excess_word/1250) * 69;
                }

                $price = $word_7500_price + $excess_word_price;

                $message = $word_count.' ORD <br />
                    <h3  class="no-margin-top">'.number_format($price, 2).' kr</h3>';
                return redirect()->back()->with('compute_manuscript', $message);
            endif;
        }
        return view('frontend.coaching-timer');
    }

    /**
     * Display the checkout page or calculate the add-on
     * @param $plan
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function coachingTimerCheckout($plan, Request $request)
    {
        // 1 is an hour
        // 2 is half hour
        $plans = [1,2];
        if (in_array($plan, $plans)) {
            $data['price'] = 1690;
            $data['title'] = 'Coaching Time(1 hr)';
            $data['file_location'] = '';
            $data['file_name'] = '';
            $data['plan_id'] = $plan;
            if ($plan == 2) {
                $data['price'] = 1190;
                $data['title'] = 'Coaching Time(30 min)';
            }

            if ($request->isMethod('post')) {
                $extensions = ['docx'];
                if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
                    $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
                    $original_filename = $request->manuscript->getClientOriginalName();
                    $file_name = pathinfo($_FILES['manuscript']['name'], PATHINFO_FILENAME);

                    if( !in_array($extension, $extensions) ) :
                        return redirect()->back();
                    endif;

                    $time = time();
                    $destinationPath = 'storage/manuscript-tests/'; // upload path
                    $fileName = $original_filename; // rename document
                    $request->manuscript->move($destinationPath, $fileName);

                    $docObj = new \Docx2Text($destinationPath.$fileName);
                    $docText= $docObj->convertToText();
                    $word_count = FrontendHelpers::get_num_of_words($docText);

                    $data['file_name'] = $original_filename;
                    $data['file_location'] = $destinationPath.$fileName;

                    $word_7500_price    = 690;
                    $excess_word        = 0;
                    $excess_word_price  = 0;

                    // the initial calculated word is 7500 if excess then calculate the total excess price
                    if ($word_count > 7500) {
                        $excess_word = $word_count - 7500;
                        // 69 is the price for every 1250 that is excess
                        $excess_word_price = ceil($excess_word/1250) * 69;
                    }

                    $price = $word_7500_price + $excess_word_price;
                    $data['price'] = $data['price'] + $price;

                    $message = '<h2 class="no-margin-top">Add On </h2>'.$word_count.' ORD <br />
                    <h3  class="no-margin-top">'.number_format($price, 2).' kr</h3>';
                    return redirect()->back()->with('compute_manuscript', $message)->with('data', $data);
                endif;
            }

            return view('frontend.coaching-timer-checkout', compact('data'));
        }
        return view('front.coaching-timer');
    }

    /**
     * Process the order for coaching timer
     * @param $plan
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function coachingTimerPlaceOrder($plan, Request $request)
    {
        $data = $request->except('_token');
        $suggested_dates = $data['suggested_date'];
        $newFileLocation = NULL;

        // format the sent suggested dates
        foreach ($suggested_dates as $k => $suggested_date) {
            $suggested_dates[$k] = Carbon::parse($suggested_date)->format('Y-m-d H:i:s');
        }

        $title = 'Coaching time';
        if ($plan == 1) {
            $title .= ' (1 time)';
            $productID = 601355457;
        } else {
            $title .= ' (0,5 time)';
            $productID = 601355458;
        }
        $paymentMode = PaymentMode::findOrFail($request->payment_mode_id);
        $paymentPlan = PaymentPlan::findOrFail(6);
        $payment_plan = ( $paymentMode->mode == "Paypal" ) ?  "Hele beløpet" : $paymentPlan->plan;

        $comment = '(' . $title . ', ';
        $comment .= 'Betalingsmodus: ' . $paymentMode->mode . ', ';
        $comment .= 'Betalingsplan: 14 dager)';

        $dueDate = date("Y-m-d");
        $dueDate = Carbon::parse($dueDate);

        $dueDate->addDays(14);

        $dueDate = date_format(date_create($dueDate), 'Y-m-d');
        $price = $data['price'] * 100;
        $user = Auth::user();

        if ($request->file_location) {
             // move the file to another location
            $file           = explode('/', $data['file_location']);
            $fileName       = $file[2];
            $destination    = 'storage/coaching-timer-manuscripts/';
            $time           = time();
            $getExtension   = explode('.', $fileName);
            $extension      = $getExtension[1];

            $newFileLocation = $destination.$time.'.'.$extension;
            // move the file from manuscript-tests to shop-manuscripts
            \File::move($data['file_location'], $destination.$time.'.'.$extension);
        }

        $invoice_fields = [
            'user_id'       => Auth::user()->id,
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'netAmount'     => $price,
            'dueDate'       => $dueDate,
            'description'   => 'Kursordrefaktura',
            'productID'     => $productID,
            'email'         => $user->email,
            'telephone'     => $user->telephone,
            'address'       => $user->street,
            'postalPlace'   => $user->city,
            'postalCode'    => $user->zip,
            'comment'       => $comment,
        ];

        $invoice = new FikenInvoice();
        $invoice->create_invoice($invoice_fields);

        CoachingTimerManuscript::create([
           'user_id'        => Auth::user()->id,
           'file'           => $newFileLocation,
            'payment_price' => $data['price'],
            'plan_type'     => $plan,
            'suggested_date' => json_encode($suggested_dates),
            'help_with'     => $data['help_with']
        ]);

        if( $paymentMode->mode == "Paypal" ) :
            echo '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price/100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>';
            echo '<script>document.getElementById("paypal_form").submit();</script>';
            return;
        endif;

        return redirect(route('front.simple.thankyou'));
    }

    /**
     * Display the articles of the selected solution
     * @param Solution $support_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function supportArticles($support_id)
    {
       $solution = Solution::find($support_id);
       if ($solution) {
           $articles = $solution->articles;
           return view('frontend.solution-articles', compact('solution','articles'));
       }
       return redirect()->route('front.home');
    }

    /**
     * Display the article
     * @param $support_id
     * @param $article_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function supportArticle($support_id, $article_id)
    {
        $solution = Solution::find($support_id);
        $article = SolutionArticle::find($article_id);
        if ($solution && $article) {
            return view('frontend.solution-article', compact('solution','article'));
        }
        return redirect()->route('front.home');
    }

    /**
     * Display or register the user to the particular webinar
     * @param $id int FreeWebinar
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function freeWebinar($id, Request $request)
    {
        $freeWebinar = FreeWebinar::find($id);

        if (!$freeWebinar) {
            return redirect()->route('front.home');
        }

        if ($request->isMethod('post')) {

            $this->validate($request, ['email' => 'required|email', 'first_name' => 'required', 'last_name' => 'required']);

            $explodeName = explode(' ',$request->name);
            $sliced = array_slice($explodeName, 0, -1); // get all except the last

            $base_url = 'https://api.getgo.com/G2W/rest';
            $access_token = 'LFuxWWDUgAuqIAAB87xQJOdeAsiG'; // from here http://app.gotowp.com/
            $org_key = '5169031040578858252';
            $web_key = $freeWebinar->gtwebinar_id; // id of the webinar from gotowebinar

            $firstName = $request->first_name;//implode(" ", $sliced);
            $lastName = $request->last_name;//end($explodeName);
            $email = $request->email;

            $vals['body'] = (object) array(
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email
            );
            $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$web_key.'/registrants';
            $header = array();
            $header[] = 'Accept: application/json';
            $header[] = 'Content-type: application/json';
            $header[] = 'Accept: application/vnd.citrix.g2wapi-v1.1+json';
            $header[] = 'Authorization: OAuth oauth_token='.$access_token;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $long_url);
            curl_setopt( $ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($vals['body']));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            $decoded_response = json_decode($response);

            if (isset($decoded_response->status)) {
                if ($decoded_response->status == 'APPROVED') {
                    $message = $decoded_response->joinUrl;
                    return view('frontend.free-webinar-success', compact('freeWebinar'));
                }
            } else {
                //error
                $message = $decoded_response->description;
                if (str_word_count($request->name) < 2) {
                    return redirect()->back()->withInput()->with([
                        'errors' => AdminHelpers::createMessageBag($message)
                    ]);
                }
            }

        }
        return view('frontend.free-webinar', compact('freeWebinar'));
        //return view('frontend.free-webinar', compact('freeWebinar'));
    }

    public function freeWebinarThanks($id)
    {
        $freeWebinar = FreeWebinar::find($id);
        return view('frontend.free-webinar-success', compact('freeWebinar'));
    }

    public function webinarThanks()
    {
        return view('frontend.webinar-thanks');
    }

    /**
     * Display/insert opt-in
     * @param $slug
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function optIn($slug = null, Request $request)
    {

        $optIn = OptIn::find(1);

        if ($slug) {
            $optIn = OptIn::getBySlug($slug ?: 'terms');
        }

        if ($request->isMethod('post') && $optIn) {
            $validates = [
                'email' => 'required|email',
                'name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
                'terms' => 'required',
            ];

            // validate the post request
            $this->validate($request, $validates);
            $list_id = $optIn->list_id;
            AdminHelpers::addToActiveCampaignList($list_id, $request->except('_token','terms'));
            return redirect()->back()->with([
                'opt-in-message' => 1
            ]);
        }

        if ($optIn) {
            return view('frontend.opt-in', compact('optIn'));
        }

        return redirect()->route('front.home');
    }

    /**
     * Opt in tips page
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function optInRektor(Request $request)
    {
        if ($request->isMethod('post')) {
            $validates = [
                'email' => 'required|email',
                'name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
                'terms' => 'required',
            ];

            // validate the post request
            $this->validate($request, $validates);
            $list_id = 64;

            AdminHelpers::addToActiveCampaignList($list_id, $request->except('_token','terms'));
            return redirect()->back()->with([
                'opt-in-message' => 1
            ]);
        }

        return view('frontend.opt-in-rektor');
    }

    public function optInTerms()
    {
        return view('frontend.opt-in-terms');
    }

    public function terms($slug = null)
    {
        $terms = Settings::getByName($slug ?: 'terms');
        if ($terms) {
            return view('frontend.terms', compact('terms'));
        }

        return redirect()->route('front.home');
    }

    /**
     * Opt in form in home page
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function homeOptIn(Request $request)
    {
        if ($request->isMethod('post')) {
            $validates = [
                'email' => 'required|email',
                'name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
                'g-recaptcha-response' => 'required|captcha',
                'terms' => 'required',
            ];

            // validate the post request
            $this->validate($request, $validates);
            $list_id = 10;
            AdminHelpers::addToActiveCampaignList($list_id, $request->except('_token','terms'));
            return redirect()->route('front.subscribe-success');
        }

        return redirect()->back();
    }

    /**
     * Confirm the secondary email based by token
     * @param $token
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function emailConfirmation($token)
    {
        $model = EmailConfirmation::where('token', $token)->first();
        if (Auth::guest()) {
            return redirect()->to('/');
        }
        if($model)
        {
            $data = ['user_id' => $model->user_id, 'user' => $model->user, 'email' => $model->email];
            if(Auth::user()->id === $data['user_id'])
            {
                \DB::beginTransaction();
                if(! UserEmail::create([ 'user_id' => $model->user_id, 'email' => $model->email]))
                {
                    \DB::rollback();
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
                if(! $model->delete())
                {
                    \DB::rollback();
                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
                \DB::commit();
            }
            return view('frontend.learner.email.confirm')->with(compact('data'));
        }

        return view('frontend.learner.email.invalid');
    }

    public function testemail()
    {
        $from       = 'post@forfatterskolen.no';//$request->from_email;
        $subject    = 'Due Invoice';
        $dueTomorrow = Carbon::today()->addDay(1)->format('Y-m-d');

        $invoices = Invoice::whereDate('fiken_dueDate',  $dueTomorrow)
            ->where('fiken_is_paid', '=',0)
            ->get();

        foreach ($invoices as $invoice) {
            $balance            = $invoice->fiken_balance;
            $transactions_sum   = $invoice->transactions->sum('amount');
            $remaining          = $balance - $transactions_sum;

            $to = 'ely@mailinator.com';//$invoice->user->email;

            $message =  'Du har en faktura som har forfall i morgen <br/>
Pris: '.FrontendHelpers::currencyFormat($remaining).'<br/> Kontonummer: 9015 18 00393 <br/> Kid nummer: '.$invoice->kid_number.' <br/> 
<a href="'.route('learner.invoice.show', $invoice->id).'">View Invoice</a> <br><br> <small>*Note: You must be logged in to view the invoice.</small>';

            AdminHelpers::send_email($subject,
            $from, $to, $message);
        }

        echo "email sent";

    }

    public function testEmail2()
    {
        /*AdminHelpers::send_email('Subject','post@forfatterskolen.no','elybutabara@yahoo.com','this is a test only');
        echo "<br/>sent";*/

        /*$message = 'Inquiry Message <br/>'.PHP_EOL;
        $message .= 'Name: Ely <br/>'.PHP_EOL;
        $message .= 'Email: elybutabara@gmail.com <br/>'.PHP_EOL;
        $message .= 'Message: this is my message';

        $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail('elybutabara@yahoo.com', 'Inquiry Message', $message, $headers);
        echo "sent";*/

        foreach(Auth::user()->coursesTaken as $courseTaken) {
            $package = Package::find($courseTaken->package_id);
            if ($package && $package->course_id == 17) {

                $checkDate = date('Y-m-d', strtotime($courseTaken->started_at));
                if ($courseTaken->end_date) {
                    $checkDate = date('Y-m-d', strtotime($courseTaken->end_date));
                }

                // check if the date is in the past or today
                // and if the user wants to auto renew the courses
                if (Carbon::now()->gt(Carbon::parse($checkDate)) && Auth::user()->auto_renew_courses) {
                    $user = Auth::user();
                    $payment_mode   = 'Bankoverføring';
                    $price          = (int)1490*100;
                    $product_ID     = $package->full_price_product;
                    $send_to        = $user->email;
                    $dueDate = date("Y-m-d");

                    $comment = '(Kurs: ' . $package->course->title . ' ['.$package->variation.'], ';
                    $comment .= 'Betalingsmodus: ' . $payment_mode . ')';

                    $invoice_fields = [
                        'user_id'       => $user->id,
                        'first_name'    => $user->first_name,
                        'last_name'     => $user->last_name,
                        'netAmount'     => $price,
                        'dueDate'       => $dueDate,
                        'description'   => 'Kursordrefaktura',
                        'productID'     => $product_ID,
                        'email'         => $send_to,
                        'telephone'     => $user->address->phone,
                        'address'       => $user->address->street,
                        'postalPlace'   => $user->address->city,
                        'postalCode'    => $user->address->zip,
                        'comment'       => $comment
                    ];


                    $invoice = new FikenInvoice();
                    //$invoice->create_invoice($invoice_fields);

                    foreach (Auth::user()->coursesTaken as $coursesTaken) {
                        // check if course taken have set end date and add one year to it
                        if ($coursesTaken->end_date) {
                            $addYear = date("Y-m-d", strtotime(date("Y-m-d", strtotime($coursesTaken->end_date)) . " + 1 year"));
                            $coursesTaken->end_date = $addYear;
                        }

                        $coursesTaken->started_at = Carbon::now();
                        $coursesTaken->save();
                    }

                    // add to automation
                    $user_email     = Auth::user()->email;
                    $automation_id  = 73;
                    $user_name      = Auth::user()->first_name;

                    //AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);

                    // Email to support
                    //mail('support@forfatterskolen.no', 'All Courses Renewed', Auth::user()->first_name . ' has renewed all the courses');
                }

            }
        }

    }
}
