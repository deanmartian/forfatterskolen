<?php
namespace App\Http\Controllers\Frontend;

use App\Address;
use App\Advisory;
use App\Blog;
use App\CoachingTimerManuscript;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\CoursesTaken;
use App\EmailAttachment;
use App\EmailConfirmation;
use App\EmailHistory;
use App\FileUploaded;
use App\FreeWebinar;
use App\GTWebinar;
use App\Helpers\Citrix;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Http\Middleware\Admin;
use App\Invoice;
use App\Jobs\AddMailToQueueJob;
use App\Log;
use App\Mail\DiscussionEmail;
use App\Mail\DiscussionRepliesEmail;
use App\Mail\SubjectBodyEmail;
use App\OptIn;
use App\Order;
use App\PaymentMode;
use App\PaymentPlan;
use App\PilotReaderBook;
use App\PilotReaderBookReading;
use App\PilotReaderBookSettings;
use App\Poem;
use App\PublisherBook;
use App\Repositories\Services\SaleService;
use App\Repositories\VippsRepository;
use App\Settings;
use App\Solution;
use App\SolutionArticle;
use App\SosChildren;
use App\Testimonial;
use App\UserEmail;
use App\Webinar;
use App\WebinarRegistrant;
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
        $next_free_webinar = FreeWebinar::where('start_date', '>=' ,Carbon::today())->orderBy('start_date', 'ASC')->first();
        // check for workshop that has menu and is for sale and date is greater than equal to today
        $next_workshop = Workshop::has('menus')->where('date', '>=', Carbon::today())
            ->where('is_free', '=', 0)
            ->orderBy('date', 'ASC')->first();

        $latest_blog = Blog::activeOnly()->orderBy('created_at', 'desc')->first();
        $poems = Poem::orderBy('created_at', 'desc')->get();
        $testimonials = Testimonial::active()->get();
        $workshop = Workshop::find(12); // gro-dahle

        return view('frontend.home', compact('popular_courses', 'free_courses', 'free_webinars',
            'next_webinar', 'next_free_webinar', 'next_workshop','latest_blog', 'poems', 'testimonials', 'workshop'));
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
                'fullname' => 'required|alpha_spaces',
                'email' => 'required|email',
                'message' => 'required',
                'terms' => 'required',
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
            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Din melding er sendt'),
                'alert_type' => 'success']);
        }

        $advisory       = Advisory::find(1);
        $from_date      = Carbon::parse($advisory->from_date);
        $to_date        = Carbon::parse($advisory->to_date);
        $checkBetween   = Carbon::today()->between($from_date, $to_date);
        $hasAdvisory    = 0;
        if ($checkBetween) {
            $hasAdvisory++;
        }

        return view('frontend.contact-us', compact('hasAdvisory', 'advisory'));
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
        $mainBlog = Blog::activeOnly()->orderBy('created_at','DESC')->first();
        $blogs = Blog::activeOnly()->where('id','!=', $mainBlog->id)
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
        $blog = Blog::find($id);
        if($blog && $blog->status == 1) {
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
                    <h1>'.number_format($calculated_price, 2).' kr</h1>
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

        if(!\File::exists($data['file_location'])){
            return redirect()->back()->withErrors([
                'file' => 'Please re-upload the file'
            ]);
        }
       
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
            'payment_mode'  => $paymentMode->mode,
        ];

        $invoice = new FikenInvoice();
        $invoice->create_invoice($invoice_fields);

        $copyEditingManuscript = null;
        $correctionManuscript = null;

        if (session('os_is_copy_editing') == 1) {
            $copyEditingManuscript = CopyEditingManuscript::create([
                'user_id' => Auth::user()->id,
                'file'  => $newFileLocation,
                'payment_price' => $data['price']
            ]);
        } else {
            $correctionManuscript = CorrectionManuscript::create([
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

        // send email 
        $user_email = Auth::user()->email;
        $parentID = null;
        $parent = null;
        $emailTemplate = AdminHelpers::emailTemplate('Other Services Order');

        if (session('os_is_copy_editing') == 1) {
            $parentID = $copyEditingManuscript->id;
            $parent = 'Copy Editing Order';
        }else{
            $parentID = $correctionManuscript->id;
            $parent = 'Correction Order';
        }

        $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user_email,
                Auth::user()->first_name, '');

        dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, null, null, $parent, $parentID));

        return redirect(route('front.simple.thankyou'));
        // return redirect()->to('/thank-you');

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
                    <h1 class="no-margin-top">'.number_format($calculated_price, 2).' kr</h1>
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

                    $message = '<h1>Add On </h1>'.$word_count.' ORD <br />
                    <h2>'.number_format($price, 2).' kr</h2>';
                    return redirect()->back()->with('compute_manuscript', $message)->with('data', $data);
                endif;
            }

            return view('frontend.coaching-timer-checkout', compact('data'));
        }
        return view('frontend.coaching-timer');
    }

    /**
     * Process the order for coaching timer
     * @param $plan
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
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
            'payment_mode'  => $paymentMode->mode,
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

        AdminHelpers::send_email('New Coaching Session',
            'post@forfatterskolen.no', 'camilla@forfatterskolen.no', Auth::user()->first_name
            . ' has ordered the Coaching Time '.$title);

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

        return redirect()->to('/thank-you'); //route('front.simple.thankyou') not working if route name is used
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
    public function freeWebinarGTW($id, Request $request)
    {
        $freeWebinar = FreeWebinar::find($id);

        if (!$freeWebinar) {
            return redirect()->route('front.home');
        }

        if ($request->isMethod('post')) {

            $this->validate($request, ['email' => 'required|email', 'first_name' => 'required', 'last_name' => 'required']);

            $explodeName = explode(' ',$request->name);
            $sliced = array_slice($explodeName, 0, -1); // get all except the last

            $base_url = 'https://api.getgo.com/G2W/rest/v2';
            $access_token = AdminHelpers::generateWebinarGTAccessToken(); // from here http://app.gotowp.com/
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

    /**
     * Display or register the user to the particular webinar
     * @param $id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function freeWebinar($id, Request $request)
    {
        $freeWebinar = FreeWebinar::find($id);

        if (!$freeWebinar) {
            return redirect()->route('front.home');
        }

        if ($request->isMethod('post')) {
            $this->validate($request, ['email' => 'required|email', 'first_name' => 'required', 'last_name' => 'required']);

            $url = config('services.big_marker.register_link');
            $data = $request->except('_token');
            $data['id'] = $freeWebinar->gtwebinar_id; // id of the big marker webinar

            $ch = curl_init();
            $header[] = 'API-KEY: '.config('services.big_marker.api_key');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $response = curl_exec($ch);
            $decoded_response = json_decode($response);

            if (array_key_exists('conference_url', $decoded_response)) {
                return view('frontend.free-webinar-success', compact('freeWebinar'));
            } else {
                $message = $decoded_response->error;
                return redirect()->back()->withInput()->with([
                    'errors' => AdminHelpers::createMessageBag($message)
                ]);
            }

        }

        return view('frontend.free-webinar', compact('freeWebinar'));
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

            $slugIdList = [3,4,5,7,8]; //dikt, Gratis krimkurs, aldersgrupper, skrive
            if (in_array($optIn->id, $slugIdList)) {
                return redirect()->route('front.opt-in.thanks', $slug);
            }

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
     * Display the thank you page for optin
     * @param null $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function optInThanks($slug = null)
    {
        $webinar_pakke = Course::find(17);
        $next_webinars = $webinar_pakke->webinars()->where('start_date', '>=' ,Carbon::today())
            ->where('set_as_replay', 0)->get();

        $optIn = OptIn::getBySlug($slug ?: 'terms');

        if ($optIn) {
            switch ($optIn->id) {
                case 3 : //dikt
                    $data['camp_id'] = 61319;
                    return view('frontend.opt-in-thanks.dikt', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 4: //gratis-krimkurs
                    $data['camp_id'] = 61855;
                    return view('frontend.opt-in-thanks.crime', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 5:
                    $data['camp_id'] = 62483;
                    return view('frontend.opt-in-thanks.children', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 7 : //skrive
                    $data['camp_id'] = 61319;
                    return view('frontend.opt-in-thanks.skrive', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 'fiction':
                    $data['camp_id'] = 61832;
                    return view('frontend.opt-in-thanks.fiction', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 8 : //pdf
                    $data['camp_id'] = 8;
                    return view('frontend.opt-in-thanks.pdf', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                default:
                    break;
            }
        }

        return redirect()->route('front.home');
    }

    /**
     * Display the referral points page
     * @param null $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function optInReferral($slug = null)
    {
        $optIn = OptIn::getBySlug($slug ?: 'terms');
        if ($optIn) {
            $data = [];
            switch ($optIn->id) {
                case 3 : // dikt
                    $data['camp'] = '&M4(M$';
                    $data['camp_id'] = 61319;
                    $data['image'] = 'poem-bg-low-blur.png';
                    break;

                case 4 :
                    $data['camp'] = '844GM$';
                    $data['camp_id'] = 61855;
                    $data['image'] = 'crime-bg.png';
                    break;

                case 5 :
                    $data['camp'] = 'MRMSA$';
                    $data['camp_id'] = 62483;
                    $data['image'] = 'children-bg.png';
                    break;

                case 'fiction' :
                    $data['camp'] = 'SR4GM$';
                    $data['camp_id'] = 61832;
                    $data['image'] = 'fiction-bg.png';
                    break;

                default:
                    break;
            }
            return view('frontend.opt-in-thanks.referral', compact('slug', 'data', 'optIn'));
        }

        return redirect()->route('front.home');
    }

    /**
     * Download the opt-inf file
     * @param null $slug
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadOptIn($slug = null)
    {
        $optIn = OptIn::getBySlug($slug ?: 'terms');

        if ($optIn) {
            $file = 'storage/opt-in-files/';
            $downloadFile = $optIn->pdf_file ?: $file.'Diktkurset.pdf';
            /*switch ($optIn->id) {
                case 4 :
                    $file = $file.'Gratiskurs_Krimkurs_FS.pdf';
                    break;

                case 5 :
                    $file = $file.'Barnebok_skrive_for_ulike_aldre.pdf';
                    break;

                default:
                    $file = $file.'Diktkurset.pdf';
                    break;
            }*/
            return response()->download(public_path($downloadFile));
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
        $terms = $slug == 'all' ? Settings::getAllTerms() :Settings::getByName($slug ?: 'terms');
        if ($terms || $slug == 'all') {
            return view('frontend.terms', compact('terms', 'slug'));
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
        $subject = 'Fresh email subject';
        $from = 'post@forfatterskolen.no';
        $from_name = 'Forfatterskolen';
        $to = 'elybutabara@gmail.com';
        $content = 'this is a test only from PORT '.env('MAIL_PORT');
        echo $to."<br/>";
        echo env('MAIL_PORT')." ".env('MAIL_PORT_SITE')."<br/>";
        //AdminHelpers::send_email($subject,'postmail@forfatterskolen.no', $to, $content);
        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $content." using queue with plain text <a href='#'>link here</a>";
        $emailData['from_name'] = NULL;
        $emailData['from_email'] = NULL;
        $emailData['attach_file'] = NULL;
        //\Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        $parent = 'test';
        $parent_id = 1;
        dispatch(new AddMailToQueueJob($to, $subject, 'testing', 'post@forfatterskolen.no', null, null,
            $parent, $parent_id));
        echo env('MAIL_DRIVER');
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
                        'comment'       => $comment,
                        'payment_mode'  => "Faktura",
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

    public function webinarPakkeRef()
    {
        return view('frontend.upviral-campaign.webinar-pakke');
    }

    public function henrikPage()
    {
        return view('frontend.henrik-langeland');
    }

    public function skrive2020()
    {
        return view('frontend.skrive2020');
    }

    public function grodahlePage()
    {
        return view('frontend.gro-dahle');
    }

    public function poems()
    {
        $poems = Poem::orderBy('created_at', 'desc')->get();
        return view('frontend.poems', compact('poems'));
    }

    /**
     * Download an email attachment based on token
     * @param $token
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function emailAttachment($token)
    {
        $emailAttachment = EmailAttachment::where('hash', '=', $token)->first();
        if ($emailAttachment) {
            return response()->download(public_path($emailAttachment->filename));
        }

        return abort(404);
    }

    /**
     * @param $code
     */
    public function emailTracking( $code )
    {
        $code = str_replace('.png', '', $code);
        $email = EmailHistory::where('track_code', '=', $code)
            ->whereNull('date_open')
            ->first();

        if ($email) {
            $email->date_open = Carbon::now();
            $email->save();
        }

        return view('frontend.email-tracking');
    }

    public function gtWebinarSendEmail(Request $request)
    {
        if ($request->get('status') == 'APPROVED') {
            $extended = $request->get('extended');
            $webinar_details = $request->get('webinar_details');

            $gtWebinar = GTWebinar::where('gt_webinar_key', '=', $webinar_details['webinarKey'])->first();
            if ($gtWebinar) {
                $subject    = $webinar_details['subject'];
                $from       = $webinar_details['organizerEmail'];
                $to         = $extended['email'];

                $replaceTime = str_replace("'","\"",str_replace("u'","'",$webinar_details['times']));
                $decode_time = json_decode($replaceTime);
                $startTime = $decode_time[0]->startTime;
                $endTime = $decode_time[0]->endTime;

                $formattedDate = AdminHelpers::convertTZNoFormat($startTime, $webinar_details['timeZone'])->format('D, M d, H:i').' - '
                    .AdminHelpers::convertTZNoFormat($endTime, $webinar_details['timeZone'])->format('H:i');

                $joinURL = $request->get('joinUrl');
                $explodeJoinURL = explode('/', $joinURL);
                $user_id = end($explodeJoinURL);

                $calendar_link = 'https://global.gotowebinar.com/icsCalendar.tmpl?webinar='
                    .$webinar_details['webinarKey'].'&user='.$user_id;
                $outlook_calendar = "<a href='".$calendar_link."&cal=outlook' style='text-decoration: none'>Outlook<sup>®</sup> Calendar</a>";
                $google_calendar = "<a href='".$calendar_link."&cal=google' style='text-decoration: none'>Google Calendar™</a>";
                $i_calendar = "<a href='".$calendar_link."&cal=ical' style='text-decoration: none'>iCal<sup>®</sup></a>";

                $admin_email = "<a href='mailto:".$webinar_details['organizerEmail']."' style='text-decoration: none'>"
                    .$webinar_details['organizerEmail']."</a>";

                $join_button = "<p style='margin-left: 170px'><a href='".$joinURL."' style='font-size:16px;font-family:Helvetica,Arial,sans-serif;color:#ffffff;
text-decoration:none;border-radius:3px;padding:12px 18px;border:1px solid #114c7f;display:inline-block;background-color:#114c7f'>Bli med på webinar</a></p>";
                $system_req = "<a href='https://link.gotowebinar.com/email-welcome?role=attendee&source=registrationConfirmationEmail
&language=english&experienceType=CLASSIC' style='text-decoration: none'>Test ditt system før webinaret</a>";
                // add dash after every 3rd character
                $webinarID = implode("-", str_split($webinar_details['webinarID'], 3));
                $cancel_reg = "<a href='https://attendee.gotowebinar.com/cancel/".$webinar_details['webinarKey']."/"
                    .$request->get('registrantKey')."' style='text-decoration: none'>kanselere registreringen</a>";

                $search_string = [
                    '[first_name]', '[webinar_title]', '[admin_email]', '[webinar_date]', '[outlook_calendar]',
                    '[google_calendar]', '[i_cal]', '[join_button]', '[check_system_requirements]', '[webinar_id]',
                    '[cancel_registration]'
                ];
                $replace_string = [
                    $request->get('firstName'), $subject, $admin_email, $formattedDate, $outlook_calendar,
                    $google_calendar, $i_calendar, $join_button, $system_req, $webinarID, $cancel_reg
                ];

                $content = str_replace($search_string, $replace_string, $gtWebinar->confirmation_email);

                $emailData['email_subject'] = $subject;
                $emailData['email_message'] = $content;
                $emailData['from_name'] = NULL;
                $emailData['from_email'] = $from;
                $emailData['attach_file'] = NULL;

                \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
                //AdminHelpers::send_email($subject, $from, $to, $content);
            }
        }
    }

    /**
     * Register user to bigmarker when they click the link from their email
     * @param $webinar_key
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function gotoWebinarEmailRegistration($webinar_key, $email) {
        $webinar_key    = decrypt($webinar_key);
        $email          = decrypt($email);
        $webinar        = Webinar::where('link', '=', $webinar_key)->first();
        $user           = User::where('email', '=', $email)->first();

        if (!$user) {
            return redirect()->to('/');
        }

        $data = [
            'id'            => $webinar_key,
            'email'         => $user->email,
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
        ];

        $url = config('services.big_marker.register_link');
        $ch = curl_init();
        $header[] = 'API-KEY: '.config('services.big_marker.api_key');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        if (array_key_exists('conference_url', $decoded_response)) {
            // add to webinar registrant to mark as Pameldt
            if ($webinar) {
                $registrant['user_id'] = $user->id;
                $registrant['webinar_id'] = $webinar->id;
                $webRegister = WebinarRegistrant::firstOrNew($registrant);
                $webRegister->join_url = $decoded_response->conference_url;
                $webRegister->save();
            }

            Auth::loginUsingId($user->id);
            return redirect()->route('front.thank-you');
        }

        return redirect()->to('/');
    }

    /**
     * Register the user to gotowebinar using the email and the webinar key sent
     * @param $webinar_key
     * @param $email
     * @return \Illuminate\Http\RedirectResponse
     */
    public function gotoWebinarEmailRegistrationOrig($webinar_key, $email)
    {
        $webinar_key    = decrypt($webinar_key);
        $email          = decrypt($email);
        $webinar        = Webinar::where('link', 'LIKE', '%'.$webinar_key.'%')->first();
        $user           = User::where('email', '=', $email)->first();

        if (!$user) {
            return redirect()->to('/');
        }

        $base_url = 'https://api.getgo.com/G2W/rest/v2';
        $access_token = AdminHelpers::generateWebinarGTAccessToken(); // from here http://app.gotowp.com/
        $org_key = '5169031040578858252';
        $web_key = $webinar_key; // id of the webinar from gotowebinar

        $firstName = $user->first_name;//implode(" ", $sliced);
        $lastName = $user->last_name;//end($explodeName);
        $user_email = $user->email;

        $vals['body'] = (object) array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $user_email
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
                // add to webinar registrant to mark as Pameldt
                if ($webinar) {
                    $registrant['user_id'] = $user->id;
                    $registrant['webinar_id'] = $webinar->id;
                    $webRegister = WebinarRegistrant::firstOrNew($registrant);
                    $webRegister->join_url = $decoded_response->joinUrl;
                    $webRegister->save();
                }
                Auth::loginUsingId($user->id);
                return redirect()->route('front.thank-you');
            }
        }

        return redirect()->to('/');
    }

    /**
     * Webinar Registrant convert to learner
     * @param $course_id
     * @param Request $request
     */
    public function gtWebinarCourseRegister($course_id, Request $request)
    {
        if ($request->get('status') == 'APPROVED') {
            $extended   = $request->get('extended');
            $user_email = $extended['email'];
            $firstName  = $extended['firstName'];
            $lastName   = $extended['lastName'];

            $course     = Course::find($course_id);
            $package    = $course->packages()->first();
            $user       = User::where('email', $user_email)->first();

            if (!$user) {
                $user = User::create([
                    'email'             => $user_email,
                    'first_name'        => $firstName,
                    'last_name'         => $lastName,
                    'password'          => bcrypt('Z5C5E5M2jv'),
                    'need_pass_update'  => 1
                ]);
            }

            CoursesTaken::create([
                'package_id'    => $package->id,
                'user_id'       => $user->id,
                'is_free'       => 1
            ]);

            $emailOut   = $course->emailOut()->where('for_free_course', 1)->first();
            $subject    = $emailOut->subject;

            $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
            $attachmentText = '';
            if ($emailAttachment) {
                $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                    .AdminHelpers::extractFileName($emailAttachment->filename)."</a></p>";
            }

            $search_string = [
                '[login_link]', '[username]', '[password]'
            ];

            $encode_email = encrypt($user_email);
            $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
            $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';
            $replace_string = [
                $loginLink, $user_email, $password
            ];
            $message = str_replace($search_string, $replace_string, $emailOut->message).$attachmentText;

            $emailData['email_subject'] = $subject;
            $emailData['email_message'] = $message;
            $emailData['from_name'] = NULL;
            $emailData['from_email'] = 'postmail@forfatterskolen.no';
            $emailData['attach_file'] = NULL;

            \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
            //AdminHelpers::send_email($subject,'postmail@forfatterskolen.no', $user_email, $message);
        }
    }

    public function testCampaign()
    {
        return view('frontend.upviral-campaign.test');
    }

    public function testFiken()
    {
        $sales = new FikenInvoice();
        $sales = $sales->getSales();
        $sales = $sales->_embedded->{'https://fiken.no/api/v1/rel/sales'};

        foreach ($sales as $sale) {
            //echo "<pre>";
            print_r($sale);
            echo "<br/><br/>";
            //echo "</pre>";
        }
    }

    /**
     * Process the payment callback
     * @param $orderId
     * @param Request $request
     * @param VippsRepository $vippsRepository
     */
    public function paymentCallback($orderId, Request $request, VippsRepository $vippsRepository)
    {
        $vippsRepository->paymentCallback($orderId, $request);
    }

    /**
     * Check if the file is saved
     * @param $hash
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkFileFromDB($hash)
    {
        $file = FileUploaded::where('hash', $hash)->first();

        if (!$file) {
            abort(404);
        }

        $extension = explode('.', basename($file->file_location));
        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            return redirect()->to("/js/ViewerJS/#../../".trim($file->file_location)."");
        } else {
            return redirect()->to("https://view.officeapps.live.com/op/embed.aspx?src=".url('')
                .trim("/".$file->file_location)."");
        }
    }

    public function testExcel()
    {
        $from = date('2019-06-26');
        $to = date('2019-06-27');
        $coursesTaken = CoursesTaken::whereBetween('created_at', ["2019-06-26 00:00:00.000000", "2019-06-27 23:59:59.999999"])->get();
        $excel          = \App::make('excel');
        $learnerList    = [];
        $learnerList[]  = ['course_taken_id', 'learner_id','learner', 'email', 'course']; // first row in excel
        foreach ($coursesTaken as $courseTaken) {
            $learnerList[] = [$courseTaken->id, $courseTaken->user->id, $courseTaken->user->full_name,
                $courseTaken->user->email, $courseTaken->package->course->title];
        }

        $excel->create("Orders", function($excel) use ($learnerList) {

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function($sheet) use ($learnerList) {
                // prevent inserting an empty first row
                $sheet->fromArray($learnerList, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    /**
     * Payment is complete
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bamboraAccept(Request $request)
    {
        \Illuminate\Support\Facades\Log::info(json_encode($request->all()));
        return redirect()->to('/thank-you');
    }

    /**
     * Payment is complete and authorized
     * @param Request $request parameters sent by bambora
     */
    public function bamboraPaymentComplete( Request $request )
    {
        \Illuminate\Support\Facades\Log::info("bambora callback");
        \Illuminate\Support\Facades\Log::info(json_encode($request->all()));

        /* TODO add course or manuscript to the user based on the details on order */

        $order = Order::find($request->orderid);
        \Illuminate\Support\Facades\Log::info("order details");
        \Illuminate\Support\Facades\Log::info(json_encode($order));

        // payment is success now capture the payment automatically
        $apiKey = app('Bambora')->credentials;

        $transactionId = $request->txnid;
        $endpointUrl = "https://transaction-v1.api-eu.bambora.com/transactions/".$transactionId."/capture";

        $postRequest = array();
        $postRequest["amount"] = $request->amount;
        $postRequest["currency"] = $request->currency;

        $requestJson = json_encode($postRequest);

        $contentLength = isset($requestJson) ? strlen($requestJson) : 0;
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . $contentLength,
            'Accept: application/json',
            'Authorization: Basic ' . $apiKey
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($curl, CURLOPT_URL, $endpointUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $rawResponse = curl_exec($curl);

        $response = json_decode($rawResponse);

        \Illuminate\Support\Facades\Log::info("capture response");
        \Illuminate\Support\Facades\Log::info(json_encode($response));

    }

    public function personalTrainer()
    {
        return view('frontend.personal-trainer.checkout');
    }

    public function personalTrainerSend( Request $request )
    {
        $messages = array(
            'reason_for_applying.required'  => 'Hva er årsaken til at du søker dette kurset (kort begrunnelse) field is required.',
            'need_in_course.required'       => 'Hva skal til for at du fullfører dette kurset field is required.',
            'expectations.required'         => 'Hvilke forventninger har du til deg selv – og oss field is required.',
        );
        $this->validate($request, [
            'email'                 => 'required',
            'first_name'            => 'required|alpha_spaces',
            'last_name'             => 'required|alpha_spaces',
            'phone'                 => 'required',
            'reason_for_applying'   => 'required',
            'need_in_course'        => 'required',
            'expectations'          => 'required',
            'how_ready'             => 'required',
        ], $messages);

        // check if have value
        if ($request->optional_words) {
            // check if it reached the maximum allowed words
            if (count(explode(' ', $request->optional_words)) > 1000) {
                return redirect()->back()->withInput()->with([
                    'errors' => AdminHelpers::createMessageBag('You entered more than the allowed 1000 words')
                ]);
            }
        }

        if( Auth::guest() ) :
            $user = User::where('email', $request->email)->first();
            if( $user ) :
                Auth::login($user);
            else :
                // register new user
                $new_user = new User();
                $new_user->email = $request->email;
                $new_user->first_name = $request->first_name;
                $new_user->last_name = $request->last_name;
                $new_user->password = bcrypt($request->password);
                $new_user->save();
                Auth::login($new_user);
            endif;
        endif;

        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->phone = $request->phone;
        $address->save();

        Auth::user()->personalTrainerApplication()->create($request->all());

        return redirect()->route('front.personal-trainer.thank-you');
    }

    public function personalTrainerThanks()
    {
        return view('frontend.personal-trainer.thank-you');
    }

    public function innleveringCompetition()
    {
        return view('frontend.competition.innlevering');
    }

    public function innleveringCompetitionSend( Request $request )
    {

        abort(404);
        $validates = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'g-recaptcha-response' => 'required|captcha',
            'manuscript' => 'required|mimes:pdf,doc,docx,odt'
        ];

        $this->validate($request, $validates);

        if( Auth::guest() ) :
            $user = User::where('email', $request->email)->first();
            if( $user ) :
                Auth::login($user);
            else :
                // register new user
                $new_user = new User();
                $new_user->email = $request->email;
                $new_user->first_name = $request->first_name;
                $new_user->last_name = $request->last_name;
                $new_user->password = bcrypt($request->password);
                $new_user->save();
                Auth::login($new_user);
            endif;
        endif;

        if( $request->hasFile('manuscript') &&  $request->file('manuscript')->isValid() ) :
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $destinationPath = 'storage/competition-manuscripts/'; // upload path
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension);// rename document
            $request->manuscript->move($destinationPath, $fileName);

            $file = '/'.$fileName;

            $data = $request->except('_token');
            $data['manuscript'] = $file;

            Auth::user()->comeptitionApplication()->create($data);

            $list_id = 110;
            $activeCampaign['email'] = $request->email;
            $activeCampaign['name'] = $request->first_name;
            $activeCampaign['last_name'] = $request->last_name;
            AdminHelpers::addToActiveCampaignList($list_id, $activeCampaign);

            return redirect()->route('front.innlevering.thank-you');
        endif;


        return redirect()->back();
    }

    public function innleveringCompetitionThanks()
    {
        return view('frontend.competition.thank-you');
    }

    /**
     * Replay page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function replay()
    {
        return view('frontend.replay');
    }

    public function barn()
    {
        return view('frontend.barn');
    }

    public function skrivdittliv()
    {
        return view('frontend.skrivdittliv');
    }

}
