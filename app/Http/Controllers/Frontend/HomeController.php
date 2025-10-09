<?php

namespace App\Http\Controllers\Frontend;

use App\Address;
use App\Advisory;
use App\Application;
use App\Blog;
use App\CoachingTimerManuscript;
use App\Contract;
use App\CopyEditingManuscript;
use App\CorrectionManuscript;
use App\Course;
use App\CoursesTaken;
use App\EmailAttachment;
use App\EmailConfirmation;
use App\EmailHistory;
use App\Exports\GenericExport;
use App\Faq;
use App\FileUploaded;
use App\FreeCourse;
use App\FreeWebinar;
use App\GTWebinar;
use App\Helpers\ApiException;
use App\Helpers\ApiResponse;
use App\Helpers\FileToText;
use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Http\PowerOffice;
use App\Imports\WebinarRegistrantsImport;
use App\Invoice;
use App\Jobs\AddMailToQueueJob;
use App\Jobs\SveaUpdateOrderDetailsJob;
use App\Mail\SubjectBodyEmail;
use App\OptIn;
use App\Order;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\Poem;
use App\PublisherBook;
use App\Replay;
use App\Repositories\VippsRepository;
use App\Services\AssignmentService;
use App\Services\CoachingTimeService;
use App\Services\CourseService;
use App\Settings;
use App\ShopManuscriptsTaken;
use App\Solution;
use App\SolutionArticle;
use App\SosChildren;
use App\Testimonial;
use App\UpcomingSection;
use App\User;
use App\UserEmail;
use App\Webinar;
use App\WebinarRegistrant;
use App\Workshop;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

include_once $_SERVER['DOCUMENT_ROOT'].'/Docx2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Pdf2Text.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/Odt2Text.php';

class HomeController extends Controller
{
    protected $sosChildren;

    public function __construct(SosChildren $sosChildren)
    {
        $this->sosChildren = $sosChildren;
    }

    public function index(): View
    {
        $popular_courses = Course::where('display_order', '>', 0)->orderBy('display_order', 'asc')->limit(3)->get();
        $free_courses = FreeCourse::orderBy('created_at', 'desc')->get();
        $free_webinars = FreeWebinar::all();

        $webinar_pakke = Course::find(17);
        $next_webinar = $webinar_pakke->webinars()->where('start_date', '>=', Carbon::today())
            ->where('set_as_replay', 0)->first();
        $next_free_webinar = FreeWebinar::where('start_date', '>=', Carbon::today())->orderBy('start_date', 'ASC')->first();
        // check for workshop that has menu and is for sale and date is greater than equal to today
        $next_workshop = Workshop::has('menus')->where('date', '>=', Carbon::today())
            ->where('is_free', '=', 0)
            ->orderBy('date', 'ASC')->first();

        $latest_blog = Blog::activeOnly()->orderBy('created_at', 'desc')->first();
        $poems = Poem::orderBy('created_at', 'desc')->get();
        $testimonials = Testimonial::active()->get();
        $workshop = Workshop::find(12); // gro-dahle

        $upcomingSections = UpcomingSection::all();

        return view('frontend.home-new', compact('popular_courses', 'free_courses', 'free_webinars',
            'next_webinar', 'next_free_webinar', 'next_workshop', 'latest_blog', 'poems', 'testimonials', 'workshop',
            'upcomingSections'));
    }

    public function sampleAbout()
    {
        return view('frontend.about');
    }

    public function fbLeads(Request $request, CourseService $courseService)
    {
        \Log::info('FACEBOOK LEADS HERE');
        \Log::info(json_encode($request->all()));
        $user_email = $request->email;
        $user = User::where('email', $user_email)->first();

        if (! $user) {
            \Log::info('add user '.$user_email);
            $user = User::create([
                'email' => $user_email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'password' => bcrypt('Z5C5E5M2jv'),
                'default_password' => 'Z5C5E5M2jv',
                'need_pass_update' => 1,
            ]);

            $encode_email = encrypt($user->email);
            $email_template = AdminHelpers::emailTemplate('Fb Leads Registration');

            // Send welcome email
            $actionUrl = route('auth.login.email', $encode_email);

            $message = str_replace(
                [
                    ':login',
                    ':end_login',
                    ':firstname',
                    ':lastname',
                    ':password',
                ],
                [
                    "<a href='".$actionUrl."' class='redirect-button' target='_blank'>",
                    '</a>',
                    $request->first_name,
                    $request->last_name,
                    'Z5C5E5M2jv',
                ],
                $email_template->email_content
            );

            $to = $user->email;
            $emailData = [
                'email_subject' => $email_template->subject,
                'email_message' => view('emails.fb-leads-registration', compact('message'))->render(),
                'from_name' => '',
                'from_email' => 'post@forfatterskolen.no',
                'attach_file' => null,
            ];
            // \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        }

        if ($request->has('package_id')) {
            $user_id = $user->id;
            $package_id = $request->package_id;
            $courseTaken = $courseService->addCourseToLearner($user_id, $package_id, true);
            $courseService->notifyUser($user_id, $package_id, $courseTaken, false, true);
        }
    }

    // set cookie for gdpr
    public function agreeGdpr()
    {
        $cookie_name = '_gdpr';
        $cookie_value = 1;
        setcookie($cookie_name, $cookie_value, time() + (86400 * 365), '/'); // 86400 = 1 day
    }

    public function contact_us(Request $request)
    {
        if ($request->isMethod('post')) {

            $validates = [
                'fullname' => 'required|alpha_spaces',
                'email' => 'required|email',
                'message' => 'required',
                'terms' => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ];

            // validate the post request
            $request->validate($validates);

            $email_content = 'From: '.$request->fullname.'<br/>';
            $email_content .= 'Email: '.$request->email.'<br/>';
            $email_content .= 'Message: '.$request->message;

            $headers = "From: Forfatterskolen<no-reply@forfatterskolen.no>\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            // mail('post@forfatterskolen.no', 'Inquiry Message', $email_content, $headers);
            // AdminHelpers::send_email('Inquiry Message','post@forfatterskolen.no','post@forfatterskolen.no', $email_content);
            $to = 'post@forfatterskolen.no'; //
            $emailData = [
                'email_subject' => 'Inquiry Message',
                'email_message' => $email_content,
                'from_name' => $request->fullname,
                'from_email' => $request->email,
                'attach_file' => null,
            ];
            \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

            return redirect()->back()->with(['errors' => AdminHelpers::createMessageBag('Din melding er sendt'),
                'alert_type' => 'success']);
        }

        $advisory = Advisory::find(1);
        $from_date = Carbon::parse($advisory->from_date);
        $to_date = Carbon::parse($advisory->to_date);
        $checkBetween = Carbon::today()->between($from_date, $to_date);
        $hasAdvisory = 0;
        if ($checkBetween) {
            $hasAdvisory++;
        }

        return view('frontend.contact-us', compact('hasAdvisory', 'advisory'));
    }

    public function giftCards(): View
    {
        Session::remove('gift-card');

        return view('frontend.gift-cards');
    }

    public function setGiftCard(Request $request)
    {
        \Session::put('gift-card', $request->card);

        return Session::all();
    }

    public function faq(): View
    {
        $faqs = Faq::orderBy('created_at', 'asc')->get();

        return view('frontend.faq', compact('faqs'));
    }

    /**
     * Display support page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function support(): View
    {
        $solutions = Solution::where('is_instruction', 0)->get();
        $instructions = Solution::where('is_instruction', 1)->get();

        return view('frontend.solution', compact('solutions', 'instructions'));
    }

    public function children(): View
    {
        $primaryVideo = $this->sosChildren->getPrimaryVideo();
        $videoRecords = $this->sosChildren->getVideoRecords();
        $mainDescription = $this->sosChildren->getMainDescription();

        return view('frontend.children', compact('primaryVideo', 'videoRecords', 'mainDescription'));
    }

    /**
     * Display publishing records
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function publishing(): View
    {
        $books = PublisherBook::with('libraries')
            ->select(['*', \DB::raw('IF(display_order > 0, display_order, 1000000) display_order')])
            ->orderBy('display_order', 'asc')->get();

        return view('frontend.publishing-library', compact('books'));
        // return view('frontend.publishing', compact('books'));
    }

    public function competition(): View
    {
        return view('frontend.competition');
    }

    /**
     * Display all blog
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    /*public function blog()
    {
        $blogs = Blog::orderBy('created_at','DESC')->get();

        return view('frontend.blog', compact('blogs'));
    }*/

    public function blog(Request $request)
    {
        $mainBlog = Blog::activeOnly()->orderBy('created_at', 'DESC')->first();
        $blogs = Blog::activeOnly()->where('id', '!=', $mainBlog->id)
            ->orderBy('created_at', 'DESC')
            ->simplePaginate(4);

        // check if ajax to display the page without loading
        if ($request->ajax()) {
            return response()->json(\View::make('frontend.blog-post', ['blogs' => $blogs])->render());
        }

        return view('frontend.blog-new', compact('mainBlog', 'blogs'));
    }

    /**
     * Display the blog content
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function readBlog($id)
    {
        $blog = Blog::find($id);
        if ($blog && $blog->status == 1) {
            return view('frontend.blog-read', compact('blog'));
        }

        return redirect()->route('front.blog');
    }

    /**
     * Display copy editing page or calculate
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function copyEditing(Request $request)
    {
        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

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
                $price_per_word = 33;
                $rounded_word = FrontendHelpers::roundUpToNearestMultiple($word_count);

                $calculated_price = ($rounded_word / $word_per_price) * $price_per_word;

                session([
                    'os_price' => $calculated_price,
                    'os_file_location' => $file,
                    'os_file_name' => $original_filename,
                    'os_product_id' => 599886093,
                    'os_is_copy_editing' => 1,
                ]);

                $message = $word_count.' TEGN <br />
                    <h1>'.number_format($calculated_price, 2).' kr</h1>
                    <a href="'.route('front.other-service-checkout', ['plan' => 1, 'has_data' => 1]).'">Bestill</a>
                    ';

                return redirect()->back()->with('compute_manuscript', $message);
            }
        }

        return view('frontend.copy-editing');
    }

    public function otherServices(): View
    {
        return view('frontend.other-services');
    }

    /**
     * Display the
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function otherServiceCheckout($plan, $has_data, Request $request)
    {

        if (! $has_data) {
            $unset_datas = ['os_price', 'os_file_location', 'os_file_name'];
            foreach ($unset_datas as $unset_data) {
                session()->forget($unset_data);
            }
        }

        $title = $plan == 1 ? 'Språkvask' : 'Korrektur';
        $product_id = $plan == 1 ? 599886093 : 599110997;
        $is_copy_editing = $plan == 1 ? 1 : 0;
        session([
            'os_product_id' => $product_id,
            'os_is_copy_editing' => $is_copy_editing,
        ]);

        $data = [
            'price' => session('os_price'),
            'file_location' => session('os_file_location'),
            'plan_id' => $plan,
            'title' => $title,
            'file_name' => session('os_file_name'),
        ];

        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                $destinationPath = 'storage/manuscript-tests/'; // upload path
                $fileName = $original_filename; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $file = $destinationPath.$fileName;

                $docObj = new FileToText($file);
                // count characters with space
                $word_count = strlen($docObj->convertToText()) - 2;
                $word_per_price = 1000;
                $price_per_word = $plan == 1 ? 33 : 25;
                $rounded_word = FrontendHelpers::roundUpToNearestMultiple($word_count);

                $calculated_price = ($rounded_word / $word_per_price) * $price_per_word;

                $data['price'] = $calculated_price;
                $data['file_location'] = $file;
                $data['file_name'] = $original_filename;

            }
        }

        return view('frontend.other-service-checkout', compact('data'));
    }

    /**
     * Process order for other service
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function otherServiceOrder(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $data = $request->except('_token');
        $file = explode('/', $data['file_location']);
        $fileName = $file[2];
        $destination = 'storage/correction-manuscripts/';
        $time = time();
        $getExtension = explode('.', $fileName);
        $extension = $getExtension[1];

        if (session('os_is_copy_editing') == 1) {
            $destination = 'storage/copy-editing-manuscripts/';
        }

        $newFileLocation = $destination.$time.'.'.$extension;

        if (! \File::exists($data['file_location'])) {
            return redirect()->back()->withErrors([
                'file' => 'Please re-upload the file',
            ]);
        }

        if ($request->price < 484) {
            return redirect()->back()->withErrors([
                'price' => 'Price should be 484 or more',
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
        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        $comment = '(Manuskript: '.$title.', ';
        $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
        $comment .= 'Betalingsplan: 14 dager)';

        $dueDate = date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);

        $dueDate->addDays(14);

        $dueDate = date_format(date_create($dueDate), 'Y-m-d');
        $price = $data['price'] * 100;
        $user = Auth::user();

        $invoice_fields = [
            'user_id' => Auth::user()->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'netAmount' => $price,
            'dueDate' => $dueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $productID,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'address' => $user->street,
            'postalPlace' => $user->city,
            'postalCode' => $user->zip,
            'comment' => $comment,
            'payment_mode' => $paymentMode->mode,
        ];

        $invoice = new FikenInvoice;
        $invoice->create_invoice($invoice_fields);

        $copyEditingManuscript = null;
        $correctionManuscript = null;
        $newOrder = null;

        if (session('os_is_copy_editing') == 1) {
            $copyEditingManuscript = CopyEditingManuscript::create([
                'user_id' => Auth::user()->id,
                'file' => $newFileLocation,
                'payment_price' => $data['price'],
            ]);
            $newOrder['item_id'] = $copyEditingManuscript->id;
            $newOrder['type'] = Order::COPY_EDITING_TYPE;
        } else {
            $correctionManuscript = CorrectionManuscript::create([
                'user_id' => Auth::user()->id,
                'file' => $newFileLocation,
                'payment_price' => $data['price'],
            ]);
            $newOrder['item_id'] = $correctionManuscript->id;
            $newOrder['type'] = Order::CORRECTION_TYPE;
        }

        // order history
        $newOrder['user_id'] = Auth::user()->id;
        $newOrder['package_id'] = 0;
        $newOrder['plan_id'] = 8;

        Order::create($newOrder);

        // send email
        $user_email = Auth::user()->email;
        $parentID = null;
        $parent = null;

        if (session('os_is_copy_editing') == 1) {
            $parentID = $copyEditingManuscript->id;
            $parent = 'copy-editing-order';
            $emailTemplate = AdminHelpers::emailTemplate('Copy Editing Order');
        } else {
            $parentID = $correctionManuscript->id;
            $parent = 'correction-order';
            $emailTemplate = AdminHelpers::emailTemplate('Correction Order');
        }

        $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user_email,
            Auth::user()->first_name, '');

        dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, null, null, $parent, $parentID));

        if ($paymentMode->mode == 'Paypal') {
            $paypalForm = '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price / 100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>
            <script>document.getElementById("paypal_form").submit();</script>';

            return new Response($paypalForm);
        }

        return redirect()->to('/thank-you');

    }

    public function thankyou(Request $request, CoachingTimeService $coachingTimeService): View
    {
        // check if from svea payment
        if ($request->has('svea_ord') || $request->has('pl_ord')) {
            $order_id = $request->get('svea_ord') ?? $request->input('pl_ord');
            $order = Order::find($order_id);

            if ($request->has('svea_ord')) {
                SveaUpdateOrderDetailsJob::dispatch($order->id)->delay(Carbon::now()->addMinute(1));
            }

            // add course to user
            if (! $order->is_processed) {

                if ($order->type === 9) {
                    $coachingTime = $coachingTimeService->addCoachingTime($order);
                    $coachingTimeService->notifyUser($order, $coachingTime);
                    $coachingTimeService->notifyAdmin($order);
                }
            }

            $order->is_processed = 1;
            $order->save();
        }

        return view('frontend.thank-you');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function assignmentThankyou(Request $request, AssignmentService $assignmentService): View
    {
        // check if from svea payment
        if ($request->has('svea_ord')) {
            $order_id = $request->get('svea_ord');
            $order = Order::find($order_id);

            SveaUpdateOrderDetailsJob::dispatch($order->id)->delay(Carbon::now()->addMinute(1));

            // add course to user
            if (! $order->is_processed) {

                $assignmentService->upgradeAssignment($order);
            }

            $order->is_processed = 1;
            $order->save();
        }

        return view('frontend.shop.thankyou');
    }

    /**
     * Display correction page or calculate
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function correction(Request $request)
    {
        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                $time = time();
                $destinationPath = 'storage/manuscript-tests/'; // upload path
                $fileName = $original_filename; // $time.'.'.$extension; // rename document
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

                $calculated_price = ($rounded_word / $word_per_price) * $price_per_word;

                session([
                    'os_price' => $calculated_price,
                    'os_file_location' => $file,
                    'os_file_name' => $original_filename,
                    'os_product_id' => 599110997,
                    'os_is_copy_editing' => 0,
                ]);

                $message = $word_count.' TEGN <br />
                    <h1 class="no-margin-top">'.number_format($calculated_price, 2).' kr</h1>
                    <a href="'.route('front.other-service-checkout', ['plan' => 2, 'has_data' => 1]).'">Bestill</a>';

                return redirect()->back()->with('compute_manuscript', $message);
            }
        }

        return view('frontend.correction');
    }

    public function coachingTimer(Request $request)
    {
        if ($request->isMethod('post')) {
            $extensions = ['docx'];
            if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                $original_filename = $request->manuscript->getClientOriginalName();

                if (! in_array($extension, $extensions)) {
                    return redirect()->back();
                }

                $time = time();
                $destinationPath = 'storage/manuscript-tests/'; // upload path
                $fileName = $time.'.'.$extension; // rename document
                $request->manuscript->move($destinationPath, $fileName);

                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText = $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);

                $word_7500_price = 690;
                $excess_word = 0;
                $excess_word_price = 0;

                // the initial calculated word is 7500 if excess then calculate the total excess price
                if ($word_count > 7500) {
                    $excess_word = $word_count - 7500;
                    // 69 is the price for every 1250 that is excess
                    $excess_word_price = ceil($excess_word / 1250) * 69;
                }

                $price = $word_7500_price + $excess_word_price;

                $message = $word_count.' ORD <br />
                    <h3  class="no-margin-top">'.number_format($price, 2).' kr</h3>';

                return redirect()->back()->with('compute_manuscript', $message);
            }
        }

        return view('frontend.coaching-timer');
    }

    /**
     * Display the checkout page or calculate the add-on
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function coachingTimerCheckout($plan, Request $request)
    {
        // 1 is an hour
        // 2 is half hour
        $plans = [1, 2];
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
                if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
                    $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
                    $original_filename = $request->manuscript->getClientOriginalName();
                    $file_name = pathinfo($_FILES['manuscript']['name'], PATHINFO_FILENAME);

                    if (! in_array($extension, $extensions)) {
                        return redirect()->back();
                    }

                    $time = time();
                    $destinationPath = 'storage/manuscript-tests/'; // upload path
                    $fileName = $original_filename; // rename document
                    $request->manuscript->move($destinationPath, $fileName);

                    $docObj = new \Docx2Text($destinationPath.$fileName);
                    $docText = $docObj->convertToText();
                    $word_count = FrontendHelpers::get_num_of_words($docText);

                    $data['file_name'] = $original_filename;
                    $data['file_location'] = $destinationPath.$fileName;

                    $word_7500_price = 690;
                    $excess_word = 0;
                    $excess_word_price = 0;

                    // the initial calculated word is 7500 if excess then calculate the total excess price
                    if ($word_count > 7500) {
                        $excess_word = $word_count - 7500;
                        // 69 is the price for every 1250 that is excess
                        $excess_word_price = ceil($excess_word / 1250) * 69;
                    }

                    $price = $word_7500_price + $excess_word_price;
                    $data['price'] = $data['price'] + $price;

                    $message = '<h1>Add On </h1>'.$word_count.' ORD <br />
                    <h2>'.number_format($price, 2).' kr</h2>';

                    return redirect()->back()->with('compute_manuscript', $message)->with('data', $data);
                }
            }

            $user = \Auth::user();
            $userHasPaidCourse = FrontendHelpers::userHasPaidCourse();

            if ($user) {
                $user['address'] = $user->address;
            } else {
                return view('frontend.coaching-timer-login', compact('user'));
            }

            return view('frontend.coaching-timer-checkout', compact('data', 'user', 'userHasPaidCourse'));
        }

        return view('frontend.coaching-timer');
    }

    public function coachingTimeCalculate(Request $request): JsonResponse
    {
        /* $this->validate($request, [
            'manuscript' => 'mimes:docx'
        ]); */

        $data = [
            'file_name' => '',
            'file_location' => '',
            'additional_price' => 0,
        ];

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $original_filename = $request->manuscript->getClientOriginalName();
            $extension = $request->manuscript->getClientOriginalExtension();
            $extensions = ['doc', 'docx', 'odt', 'pdf'];

            if (! in_array($extension, $extensions)) {
                return response()->json([
                    'errors' => [
                        'manuscript' => [' The manuscript must be a file of type: doc, docx, odt, pdf.'],
                    ],
                ], 422);
            }

            $destinationPath = 'storage/manuscript-tests/'; // upload path
            $fileName = $original_filename; // rename document
            $request->manuscript->move($destinationPath, $fileName);
            $docObj = new \Docx2Text($destinationPath.$fileName);
            $docText = $docObj->convertToText();
            $word_count = FrontendHelpers::get_num_of_words($docText);

            $data['file_name'] = $original_filename;
            $data['file_location'] = $destinationPath.$fileName;

            $word_7500_price = 690;
            $excess_word_price = 0;

            // the initial calculated word is 7500 if excess then calculate the total excess price
            if ($word_count > 7500) {
                $excess_word = $word_count - 7500;
                // 69 is the price for every 1250 that is excess
                $excess_word_price = ceil($excess_word / 1250) * 69;
            }

            $additional_price = $word_7500_price + $excess_word_price;
            $data['additional_price'] = $additional_price;

        }

        return response()->json($data);
    }

    public function coachingTimeValidate(Request $request, CourseService $courseService, CoachingTimeService $coachingTimeService)
    {

        $validation = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'phone' => 'required',
        ];

        if (! \Auth::check()) {
            $validation['password'] = 'required|min:3';
        }

        $validator = \Validator::make($request->all(), $validation);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! \Auth::check()) {
            $addressData = [
                'street' => $request->street,
                'zip' => $request->zip,
                'city' => $request->city,
                'phone' => $request->phone,
            ];
            $courseService->evaluateUser($request->email, $request->password, $request->first_name, $request->last_name, $addressData);
        }

        if (filter_var($request->is_pay_later, FILTER_VALIDATE_BOOLEAN)) {
            return $coachingTimeService->processPayLaterOrder($request);
        }

        return response()->json($coachingTimeService->generateSveaCheckout($request));
    }

    /**
     * Process the order for coaching timer
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|mixed
     */
    public function coachingTimerPlaceOrder($plan, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $data = $request->except('_token');
        $suggested_dates = []; // $data['suggested_date'];
        $newFileLocation = null;

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
        $payment_plan = ($paymentMode->mode == 'Paypal') ? 'Hele beløpet' : $paymentPlan->plan;

        $comment = '('.$title.', ';
        $comment .= 'Betalingsmodus: '.$paymentMode->mode.', ';
        $comment .= 'Betalingsplan: 14 dager)';

        $dueDate = date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);

        $dueDate->addDays(14);

        $dueDate = date_format(date_create($dueDate), 'Y-m-d');
        $price = $data['price'] * 100;
        $user = Auth::user();

        if ($request->file_location) {
            // move the file to another location
            $file = explode('/', $data['file_location']);
            $fileName = $file[2];
            $destination = 'storage/coaching-timer-manuscripts/';
            $time = time();
            $getExtension = explode('.', $fileName);
            $extension = $getExtension[1];

            $newFileLocation = $destination.$time.'.'.$extension;
            // move the file from manuscript-tests to shop-manuscripts
            \File::move($data['file_location'], $destination.$time.'.'.$extension);
        }

        $invoice_fields = [
            'user_id' => Auth::user()->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'netAmount' => $price,
            'dueDate' => $dueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $productID,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'address' => $user->street,
            'postalPlace' => $user->city,
            'postalCode' => $user->zip,
            'comment' => $comment,
            'payment_mode' => $paymentMode->mode,
        ];

        $invoice = new FikenInvoice;
        $invoice->create_invoice($invoice_fields);

        $coaching = CoachingTimerManuscript::create([
            'user_id' => Auth::user()->id,
            'file' => $newFileLocation,
            'payment_price' => $data['price'],
            'plan_type' => $plan,
            // 'suggested_date' => json_encode($suggested_dates),
            'help_with' => $data['help_with'],
        ]);

        /*AdminHelpers::send_email('New Coaching Session',
            'post@forfatterskolen.no', 'camilla@forfatterskolen.no', Auth::user()->first_name
            . ' has ordered the Coaching Time '.$title);*/
        $to = 'post@forfatterskolen.no'; //
        $emailData = [
            'email_subject' => 'New Coaching Session',
            'email_message' => Auth::user()->first_name.' has ordered the Coaching Time '.$title,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));

        $emailTemplate = AdminHelpers::emailTemplate('Coaching Order');
        $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user->email,
            Auth::user()->first_name, '');
        dispatch(new AddMailToQueueJob($user->email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, 'Forfatterskolen', null,
            'coaching-time-order', $coaching->id));

        if ($paymentMode->mode == 'Paypal') {
            $paypalForm = '<form name="_xclick" id="paypal_form" style="display:none" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="post.forfatterskolen@gmail.com">
                <input type="hidden" name="currency_code" value="NOK">
                <input type="hidden" name="custom" value="'.$invoice->invoiceID.'">
                <input type="hidden" name="item_name" value="Course Order Invoice">
                <input type="hidden" name="amount" value="'.($price / 100).'">
                <input type="hidden" name="return" value="'.route('front.shop.thankyou').'">
                <input type="image" name="submit" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="right" alt="PayPal - The safer, easier way to pay online">
            </form>
            <script>document.getElementById("paypal_form").submit();</script>';

            return new Response($paypalForm);
        }

        return redirect()->to('/thank-you'); // route('front.simple.thankyou') not working if route name is used
    }

    public function exportSingleBoughtCoaching()
    {
        $coaching = CoachingTimerManuscript::where('payment_price', '>', 0)->get();

        $userList = [];
        foreach ($coaching as $coach) {
            $userList[] = [
                'name' => $coach->user->full_name,
                'email' => $coach->user->email,
                'date' => FrontendHelpers::formatDate($coach->created_at),
            ];
        }

        $headers = ['name', 'email', 'date'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), 'Learner with single coaching time.xlsx');
    }

    /**
     * export learners with pay later and have active course
     *
     * @return void
     */
    public function exportCoursePayLaterWithActive()
    {
        $coursesTaken = CoursesTaken::where('is_pay_later', 1)
            ->whereIn('user_id', function ($query) {
                $query->select('user_id')
                    ->from('courses_taken')
                    ->whereNotNull('end_date')
                    ->whereDate('end_date', '>=', now())
                    ->whereNull('deleted_at')
                    ->distinct();
            })
            ->get();

        $userList = [];

        foreach ($coursesTaken as $courseTaken) {
            $userList[] = [
                'name' => $courseTaken->user->full_name,
                'email' => $courseTaken->user->email,
            ];
        }

        $headers = ['name', 'email'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), 'Learners with pay later.xlsx');
    }

    /**
     * Display the articles of the selected solution
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function supportArticles(Solution $support_id)
    {
        $solution = Solution::find($support_id);
        if ($solution) {
            $articles = $solution->articles;

            return view('frontend.solution-articles', compact('solution', 'articles'));
        }

        return redirect()->route('front.home');
    }

    /**
     * Display the article
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function supportArticle($support_id, $article_id)
    {
        $solution = Solution::find($support_id);
        $article = SolutionArticle::find($article_id);
        if ($solution && $article) {
            return view('frontend.solution-article', compact('solution', 'article'));
        }

        return redirect()->route('front.home');
    }

    /**
     * Display or register the user to the particular webinar
     *
     * @param  $id  int FreeWebinar
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function freeWebinarGTW($id, Request $request)
    {
        $freeWebinar = FreeWebinar::find($id);

        if (! $freeWebinar) {
            return redirect()->route('front.home');
        }

        if ($request->isMethod('post')) {

            $request->validate(['email' => 'required|email', 'first_name' => 'required', 'last_name' => 'required']);

            $explodeName = explode(' ', $request->name);
            $sliced = array_slice($explodeName, 0, -1); // get all except the last

            $base_url = 'https://api.getgo.com/G2W/rest/v2';
            $access_token = AdminHelpers::generateWebinarGTAccessToken(); // from here http://app.gotowp.com/
            $org_key = '5169031040578858252';
            $web_key = $freeWebinar->gtwebinar_id; // id of the webinar from gotowebinar

            $firstName = $request->first_name; // implode(" ", $sliced);
            $lastName = $request->last_name; // end($explodeName);
            $email = $request->email;

            $vals['body'] = (object) [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
            ];
            $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$web_key.'/registrants';
            $header = [];
            $header[] = 'Accept: application/json';
            $header[] = 'Content-type: application/json';
            $header[] = 'Accept: application/vnd.citrix.g2wapi-v1.1+json';
            $header[] = 'Authorization: OAuth oauth_token='.$access_token;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $long_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($vals['body']));
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
                // error
                $message = $decoded_response->description;
                if (str_word_count($request->name) < 2) {
                    return redirect()->back()->withInput()->with([
                        'errors' => AdminHelpers::createMessageBag($message),
                    ]);
                }
            }

        }

        return view('frontend.free-webinar', compact('freeWebinar'));
        // return view('frontend.free-webinar', compact('freeWebinar'));
    }

    /**
     * Display or register the user to the particular webinar
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function freeWebinar($id, Request $request)
    {
        $freeWebinar = FreeWebinar::find($id);

        if (! $freeWebinar) {
            return redirect()->route('front.home');
        }

        if ($request->isMethod('post')) {
            $request->validate(['email' => 'required|email', 'first_name' => 'required', 'last_name' => 'required']);

            $url = config('services.big_marker.register_link');
            $data = $request->except('_token');
            $data['id'] = $freeWebinar->gtwebinar_id; // id of the big marker webinar

            $ch = curl_init();
            $header[] = 'API-KEY: '.config('services.big_marker.api_key');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $response = curl_exec($ch);
            $decoded_response = json_decode($response);

            if (property_exists($decoded_response, 'conference_url')) {
                return view('frontend.free-webinar-success', compact('freeWebinar'));
            } else {
                $message = $decoded_response->error;

                return redirect()->back()->withInput()->with([
                    'errors' => AdminHelpers::createMessageBag($message),
                ]);
            }

        }

        return view('frontend.free-webinar', compact('freeWebinar'));
    }

    public function freeWebinarThanks($id): View
    {
        $freeWebinar = FreeWebinar::find($id);

        return view('frontend.free-webinar-success', compact('freeWebinar'));
    }

    public function webinarThanks(): View
    {
        return view('frontend.webinar-thanks');
    }

    /**
     * Display/insert opt-in
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function optIn($slug, Request $request)
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
            $request->validate($validates);
            $list_id = $optIn->list_id;
            AdminHelpers::addToActiveCampaignList($list_id, $request->except('_token', 'terms'));

            $slugIdList = [3, 4, 5, 7, 8]; // dikt, Gratis krimkurs, aldersgrupper, skrive
            if (in_array($optIn->id, $slugIdList)) {
                return redirect()->route('front.opt-in.thanks', $slug);
            }

            return redirect()->back()->with([
                'opt-in-message' => 1,
            ]);
        }

        if ($optIn) {
            return view('frontend.opt-in', compact('optIn'));
        }

        return redirect()->route('front.home');
    }

    /**
     * Display the thank you page for optin
     *
     * @param  null  $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function optInThanks($slug = null)
    {
        $webinar_pakke = Course::find(17);
        $next_webinars = $webinar_pakke->webinars()->where('start_date', '>=', Carbon::today())
            ->where('set_as_replay', 0)->get();

        $optIn = OptIn::getBySlug($slug ?: 'terms');

        if ($optIn) {
            switch ($optIn->id) {
                case 3 : // dikt
                    $data['camp_id'] = 61319;

                    return view('frontend.opt-in-thanks.dikt', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 4: // gratis-krimkurs
                    $data['camp_id'] = 61855;

                    return view('frontend.opt-in-thanks.crime', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 5:
                    $data['camp_id'] = 62483;

                    return view('frontend.opt-in-thanks.children', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 7 : // skrive
                    $data['camp_id'] = 61319;

                    return view('frontend.opt-in-thanks.skrive', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 'fiction':
                    $data['camp_id'] = 61832;

                    return view('frontend.opt-in-thanks.fiction', compact('next_webinars', 'slug', 'data',
                        'optIn'));
                    break;

                case 8 : // pdf
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
     *
     * @param  null  $slug
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
     *
     * @param  null  $slug
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
     *
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
            $request->validate($validates);
            $list_id = 64;

            AdminHelpers::addToActiveCampaignList($list_id, $request->except('_token', 'terms'));

            return redirect()->back()->with([
                'opt-in-message' => 1,
            ]);
        }

        return view('frontend.opt-in-rektor');
    }

    public function optInTerms(): View
    {
        return view('frontend.opt-in-terms');
    }

    public function terms($slug = null)
    {
        $terms = $slug == 'all' ? Settings::getAllTerms() : Settings::getByName($slug ?: 'terms');
        if ($terms || $slug == 'all') {
            if (\request()->ajax()) {
                return $terms;
            }

            return view('frontend.terms', compact('terms', 'slug'));
        }

        return redirect()->route('front.home');
    }

    /**
     * Opt in form in home page
     */
    public function homeOptIn(Request $request): JsonResponse
    {
        if ($request->isMethod('post')) {
            $validates = [
                'email' => 'required|email',
                'name' => 'required|regex:/^[\pL\s\-]+$/u|max:100',
                'g-recaptcha-response' => 'required|captcha',
                'terms' => 'required',
            ];

            // validate the post request
            $request->validate($validates);
            $list_id = 136;
            AdminHelpers::addToActiveCampaignList($list_id, $request->except('_token', 'terms'));

            return response()->json(['redirect_link' => route('front.subscribe-success')]);
        }

        return response()->json(['redirect_link' => route('front.home')]);
    }

    /**
     * Confirm the secondary email based by token
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function emailConfirmation($token)
    {
        $model = EmailConfirmation::where('token', $token)->first();
        if (Auth::guest()) {
            return redirect()->to('/');
        }
        if ($model) {
            $data = ['user_id' => $model->user_id, 'user' => $model->user, 'email' => $model->email];
            if (Auth::user()->id === $data['user_id']) {
                \DB::beginTransaction();
                if (! UserEmail::create(['user_id' => $model->user_id, 'email' => $model->email])) {
                    \DB::rollback();

                    return response()->json(['error' => 'Opss. Something went wrong'], 500);
                }
                if (! $model->delete()) {
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
        echo $to.'<br/>';
        echo env('MAIL_PORT').' '.env('MAIL_PORT_SITE').'<br/>';
        // AdminHelpers::send_email($subject,'postmail@forfatterskolen.no', $to, $content);
        $emailData['email_subject'] = $subject;
        $emailData['email_message'] = $content." using queue with plain text <a href='#'>link here</a>";
        $emailData['from_name'] = null;
        $emailData['from_email'] = null;
        $emailData['attach_file'] = null;
        // \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        $parent = 'test';
        $parent_id = 1;
        dispatch(new AddMailToQueueJob($to, $subject, 'testing', 'post@forfatterskolen.no', null, null,
            $parent, $parent_id));

        $emailData = [
            'email_subject' => 'testing',
            'email_message' => 'testing mail queue',
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
        echo env('MAIL_MAILER');
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

        foreach (Auth::user()->coursesTaken as $courseTaken) {
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
                    $payment_mode = 'Bankoverføring';
                    $price = (int) 1490 * 100;
                    $product_ID = $package->full_price_product;
                    $send_to = $user->email;
                    $dueDate = date('Y-m-d');

                    $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
                    $comment .= 'Betalingsmodus: '.$payment_mode.')';

                    $invoice_fields = [
                        'user_id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'netAmount' => $price,
                        'dueDate' => $dueDate,
                        'description' => 'Kursordrefaktura',
                        'productID' => $product_ID,
                        'email' => $send_to,
                        'telephone' => $user->address->phone,
                        'address' => $user->address->street,
                        'postalPlace' => $user->address->city,
                        'postalCode' => $user->address->zip,
                        'comment' => $comment,
                        'payment_mode' => 'Faktura',
                    ];

                    $invoice = new FikenInvoice;
                    // $invoice->create_invoice($invoice_fields);

                    foreach (Auth::user()->coursesTaken as $coursesTaken) {
                        // check if course taken have set end date and add one year to it
                        if ($coursesTaken->end_date) {
                            $addYear = date('Y-m-d', strtotime(date('Y-m-d', strtotime($coursesTaken->end_date)).' + 1 year'));
                            $coursesTaken->end_date = $addYear;
                        }

                        $coursesTaken->started_at = Carbon::now();
                        $coursesTaken->save();
                    }

                    // add to automation
                    $user_email = Auth::user()->email;
                    $automation_id = 73;
                    $user_name = Auth::user()->first_name;

                    // AdminHelpers::addToAutomation($user_email,$automation_id,$user_name);

                    // Email to support
                    // mail('support@forfatterskolen.no', 'All Courses Renewed', Auth::user()->first_name . ' has renewed all the courses');
                }

            }
        }

    }

    public function webinarPakkeRef(): View
    {
        return view('frontend.upviral-campaign.webinar-pakke');
    }

    public function henrikPage(): View
    {
        abort(404);

        return view('frontend.henrik-langeland');
    }

    public function skrive2020(): View
    {
        return view('frontend.skrive2020');
    }

    public function grodahlePage(): View
    {
        return view('frontend.gro-dahle');
    }

    public function poems(): View
    {
        $poems = Poem::orderBy('created_at', 'desc')->get();

        return view('frontend.poems', compact('poems'));
    }

    /**
     * Download an email attachment based on token
     */
    public function emailAttachment($token)
    {
        $emailAttachment = EmailAttachment::where('hash', '=', $token)->first();
        if ($emailAttachment) {
            return response()->download(public_path($emailAttachment->filename));
        }

        return abort(404);
    }

    public function emailTracking($code): View
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
                $subject = $webinar_details['subject'];
                $from = $webinar_details['organizerEmail'];
                $to = $extended['email'];

                $replaceTime = str_replace("'", '"', str_replace("u'", "'", $webinar_details['times']));
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
                    .$webinar_details['organizerEmail'].'</a>';

                $join_button = "<p style='margin-left: 170px'><a href='".$joinURL."' style='font-size:16px;font-family:Helvetica,Arial,sans-serif;color:#ffffff;
text-decoration:none;border-radius:3px;padding:12px 18px;border:1px solid #114c7f;display:inline-block;background-color:#114c7f'>Bli med på webinar</a></p>";
                $system_req = "<a href='https://link.gotowebinar.com/email-welcome?role=attendee&source=registrationConfirmationEmail
&language=english&experienceType=CLASSIC' style='text-decoration: none'>Test ditt system før webinaret</a>";
                // add dash after every 3rd character
                $webinarID = implode('-', str_split($webinar_details['webinarID'], 3));
                $cancel_reg = "<a href='https://attendee.gotowebinar.com/cancel/".$webinar_details['webinarKey'].'/'
                    .$request->get('registrantKey')."' style='text-decoration: none'>kanselere registreringen</a>";

                $search_string = [
                    '[first_name]', '[webinar_title]', '[admin_email]', '[webinar_date]', '[outlook_calendar]',
                    '[google_calendar]', '[i_cal]', '[join_button]', '[check_system_requirements]', '[webinar_id]',
                    '[cancel_registration]',
                ];
                $replace_string = [
                    $request->get('firstName'), $subject, $admin_email, $formattedDate, $outlook_calendar,
                    $google_calendar, $i_calendar, $join_button, $system_req, $webinarID, $cancel_reg,
                ];

                $content = str_replace($search_string, $replace_string, $gtWebinar->confirmation_email);

                $emailData['email_subject'] = $subject;
                $emailData['email_message'] = $content;
                $emailData['from_name'] = null;
                $emailData['from_email'] = $from;
                $emailData['attach_file'] = null;

                \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
                // AdminHelpers::send_email($subject, $from, $to, $content);
            }
        }
    }

    /**
     * Register user to bigmarker when they click the link from their email
     */
    public function gotoWebinarEmailRegistration($webinar_key, $email): RedirectResponse
    {
        FacadesLog::info('---------------- inside gotowebinaremail registration --------------');
        FacadesLog::info($webinar_key);
        FacadesLog::info($email);
        $webinar_key = decrypt($webinar_key);
        $email = decrypt($email);
        $webinar = Webinar::where('link', '=', $webinar_key)->first();
        $user = User::where('email', '=', $email)->first();

        if (! $user) {
            return redirect()->to('/');
        }

        $data = [
            'id' => $webinar_key,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ];

        $url = config('services.big_marker.register_link');
        $ch = curl_init();
        $header[] = 'API-KEY: '.config('services.big_marker.api_key');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response = curl_exec($ch);
        $decoded_response = json_decode($response);

        if (property_exists($decoded_response, 'conference_url')) {
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

        return redirect()->route('learner.webinar');
    }

    /**
     * Register the user to gotowebinar using the email and the webinar key sent
     */
    public function gotoWebinarEmailRegistrationOrig($webinar_key, $email): RedirectResponse
    {
        $webinar_key = decrypt($webinar_key);
        $email = decrypt($email);
        $webinar = Webinar::where('link', 'LIKE', '%'.$webinar_key.'%')->first();
        $user = User::where('email', '=', $email)->first();

        if (! $user) {
            return redirect()->to('/');
        }

        $base_url = 'https://api.getgo.com/G2W/rest/v2';
        $access_token = AdminHelpers::generateWebinarGTAccessToken(); // from here http://app.gotowp.com/
        $org_key = '5169031040578858252';
        $web_key = $webinar_key; // id of the webinar from gotowebinar

        $firstName = $user->first_name; // implode(" ", $sliced);
        $lastName = $user->last_name; // end($explodeName);
        $user_email = $user->email;

        $vals['body'] = (object) [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $user_email,
        ];
        $long_url = $base_url.'/organizers/'.$org_key.'/webinars/'.$web_key.'/registrants';
        $header = [];
        $header[] = 'Accept: application/json';
        $header[] = 'Content-type: application/json';
        $header[] = 'Accept: application/vnd.citrix.g2wapi-v1.1+json';
        $header[] = 'Authorization: OAuth oauth_token='.$access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $long_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($vals['body']));
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
     */
    public function gtWebinarCourseRegister($course_id, Request $request)
    {
        if ($request->get('status') == 'APPROVED') {
            $extended = $request->get('extended');
            $user_email = $extended['email'];
            $firstName = $extended['firstName'];
            $lastName = $extended['lastName'];

            $course = Course::find($course_id);
            $package = $course->packages()->first();
            $user = User::where('email', $user_email)->first();

            if (! $user) {
                $user = User::create([
                    'email' => $user_email,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => bcrypt('Z5C5E5M2jv'),
                    'need_pass_update' => 1,
                ]);
            }

            CoursesTaken::create([
                'package_id' => $package->id,
                'user_id' => $user->id,
                'is_free' => 1,
            ]);

            $emailOut = $course->emailOut()->where('for_free_course', 1)->first();
            $subject = $emailOut->subject;

            $emailAttachment = EmailAttachment::where('hash', $emailOut->attachment_hash)->first();
            $attachmentText = '';
            if ($emailAttachment) {
                $attachmentText = "<p style='margin-top: 10px'><b>Vedlegg:</b> 
<a href='".route('front.email-attachment', $emailAttachment->hash)."'>"
                    .AdminHelpers::extractFileName($emailAttachment->filename).'</a></p>';
            }

            $search_string = [
                '[login_link]', '[username]', '[password]',
            ];

            $encode_email = encrypt($user_email);
            $loginLink = "<a href='".route('auth.login.email', $encode_email)."'>Klikk her for å logge inn</a>";
            $password = $user->need_pass_update ? 'Z5C5E5M2jv' : 'Skjult (kan endres inne i portalen eller via glemt passord)';
            $replace_string = [
                $loginLink, $user_email, $password,
            ];
            $message = str_replace($search_string, $replace_string, $emailOut->message).$attachmentText;

            $emailData['email_subject'] = $subject;
            $emailData['email_message'] = $message;
            $emailData['from_name'] = null;
            $emailData['from_email'] = 'postmail@forfatterskolen.no';
            $emailData['attach_file'] = null;

            \Mail::to($user_email)->queue(new SubjectBodyEmail($emailData));
            // AdminHelpers::send_email($subject,'postmail@forfatterskolen.no', $user_email, $message);
        }
    }

    public function testCampaign(): View
    {
        return view('frontend.upviral-campaign.test');
    }

    public function testFiken()
    {
        $sales = new FikenInvoice;
        $sales = $sales->getSales();
        $sales = $sales->_embedded->{'https://fiken.no/api/v1/rel/sales'};

        foreach ($sales as $sale) {
            // echo "<pre>";
            print_r($sale);
            echo '<br/><br/>';
            // echo "</pre>";
        }
    }

    /**
     * Process the payment callback
     */
    public function paymentCallback($orderId, Request $request, VippsRepository $vippsRepository)
    {
        $vippsRepository->paymentCallback($orderId, $request);
    }

    public function vippsFallback(Request $request): RedirectResponse
    {
        $expOrder = explode('-', $request->t);
        $vippsOrder = $this->checkVippsOrderStatus($request->t);

        // check for order status
        if ($vippsOrder['data'] && $transactionHistory = $vippsOrder['data']->transactionLogHistory[0]
                && $order = Order::find($expOrder[0])) {

            $transactionHistory = $vippsOrder['data']->transactionLogHistory[0];
            $route = $order->type === Order::MANUSCRIPT_TYPE ? 'front.shop-manuscript.cancelled-order' : 'front.course.cancelled-order';
            // check if capture and operation is success
            if ($transactionHistory->operation === 'CAPTURE' && $transactionHistory->operationSuccess) {
                $route = $order->type === Order::MANUSCRIPT_TYPE ? 'front.shop-manuscript.thankyou' : 'front.shop.thankyou';
            }

            return redirect()->route($route, $order->item_id);
        }

        return redirect()->route('front.thank-you');
    }

    /**
     * Check if the file is saved
     */
    public function checkFileFromDB($hash): RedirectResponse
    {
        $file = FileUploaded::where('hash', $hash)->first();

        if (! $file) {
            abort(404);
        }

        $extension = explode('.', basename($file->file_location));
        if (end($extension) == 'pdf' || end($extension) == 'odt') {
            return redirect()->to('/js/ViewerJS/#../../'.trim($file->file_location).'');
        } else {
            return redirect()->to('https://view.officeapps.live.com/op/embed.aspx?src='.url('')
                .trim('/'.$file->file_location).'');
        }
    }

    public function testExcel()
    {
        $from = date('2019-06-26');
        $to = date('2019-06-27');
        $coursesTaken = CoursesTaken::whereBetween('created_at', ['2019-06-26 00:00:00.000000', '2019-06-27 23:59:59.999999'])->get();
        $excel = \App::make('excel');
        $learnerList = [];
        $learnerList[] = ['course_taken_id', 'learner_id', 'learner', 'email', 'course']; // first row in excel
        foreach ($coursesTaken as $courseTaken) {
            $learnerList[] = [$courseTaken->id, $courseTaken->user->id, $courseTaken->user->full_name,
                $courseTaken->user->email, $courseTaken->package->course->title];
        }

        $excel->create('Orders', function ($excel) use ($learnerList) {

            // Build the spreadsheet, passing in the payments array
            $excel->sheet('sheet1', function ($sheet) use ($learnerList) {
                // prevent inserting an empty first row
                $sheet->fromArray($learnerList, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    /**
     * Payment is complete
     */
    public function bamboraAccept(Request $request): RedirectResponse
    {
        \Illuminate\Support\Facades\Log::info(json_encode($request->all()));

        return redirect()->to('/thank-you');
    }

    /**
     * Payment is complete and authorized
     *
     * @param  Request  $request  parameters sent by bambora
     */
    public function bamboraPaymentComplete(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('bambora callback');
        \Illuminate\Support\Facades\Log::info(json_encode($request->all()));

        /* TODO add course or manuscript to the user based on the details on order */

        $order = Order::find($request->orderid);
        \Illuminate\Support\Facades\Log::info('order details');
        \Illuminate\Support\Facades\Log::info(json_encode($order));

        // payment is success now capture the payment automatically
        $apiKey = app('Bambora')->credentials;

        $transactionId = $request->txnid;
        $endpointUrl = 'https://transaction-v1.api-eu.bambora.com/transactions/'.$transactionId.'/capture';

        $postRequest = [];
        $postRequest['amount'] = $request->amount;
        $postRequest['currency'] = $request->currency;

        $requestJson = json_encode($postRequest);

        $contentLength = isset($requestJson) ? strlen($requestJson) : 0;
        $headers = [
            'Content-Type: application/json',
            'Content-Length: '.$contentLength,
            'Accept: application/json',
            'Authorization: Basic '.$apiKey,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestJson);
        curl_setopt($curl, CURLOPT_URL, $endpointUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $rawResponse = curl_exec($curl);

        $response = json_decode($rawResponse);

        \Illuminate\Support\Facades\Log::info('capture response');
        \Illuminate\Support\Facades\Log::info(json_encode($response));

    }

    public function personalTrainer(): View
    {
        return view('frontend.personal-trainer.checkout');
    }

    public function personalTrainerSend(Request $request): RedirectResponse
    {
        $messages = [
            'reason_for_applying.required' => 'Hva er årsaken til at du søker dette kurset (kort begrunnelse) field is required.',
            'need_in_course.required' => 'Hva skal til for at du fullfører dette kurset field is required.',
            'expectations.required' => 'Hvilke forventninger har du til deg selv – og oss field is required.',
        ];
        $request->validate([
            'email' => 'required',
            'first_name' => 'required|alpha_spaces',
            'last_name' => 'required|alpha_spaces',
            'phone' => 'required',
            'reason_for_applying' => 'required',
            'need_in_course' => 'required',
            'expectations' => 'required',
            'how_ready' => 'required',
        ], $messages);

        // check if have value
        if ($request->optional_words) {
            // check if it reached the maximum allowed words
            if (count(explode(' ', $request->optional_words)) > 1000) {
                return redirect()->back()->withInput()->with([
                    'errors' => AdminHelpers::createMessageBag('You entered more than the allowed 1000 words'),
                ]);
            }
        }

        if (Auth::guest()) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                Auth::login($user);
            } else {
                // register new user
                $new_user = new User;
                $new_user->email = $request->email;
                $new_user->first_name = $request->first_name;
                $new_user->last_name = $request->last_name;
                $new_user->password = bcrypt($request->password);
                $new_user->save();
                Auth::login($new_user);
            }
        }

        $address = Address::firstOrNew(['user_id' => Auth::user()->id]);
        $address->phone = $request->phone;
        $address->save();

        Auth::user()->personalTrainerApplication()->create($request->all());

        return redirect()->route('front.personal-trainer.thank-you');
    }

    public function personalTrainerThanks(): View
    {
        return view('frontend.personal-trainer.thank-you');
    }

    public function innleveringCompetition(): View
    {
        return view('frontend.competition.innlevering');
    }

    public function innleveringCompetitionSend(Request $request): RedirectResponse
    {

        abort(404);
        $validates = [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'g-recaptcha-response' => 'required|captcha',
            'manuscript' => 'required|mimes:pdf,doc,docx,odt',
        ];

        $request->validate($validates);

        if (Auth::guest()) {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                Auth::login($user);
            } else {
                // register new user
                $new_user = new User;
                $new_user->email = $request->email;
                $new_user->first_name = $request->first_name;
                $new_user->last_name = $request->last_name;
                $new_user->password = bcrypt($request->password);
                $new_user->save();
                Auth::login($new_user);
            }
        }

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $original_filename = $request->manuscript->getClientOriginalName();
            $actual_name = pathinfo($original_filename, PATHINFO_FILENAME);

            $destinationPath = 'storage/competition-manuscripts/'; // upload path
            $fileName = AdminHelpers::checkFileName($destinationPath, $actual_name, $extension); // rename document
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
        }

        return redirect()->back();
    }

    public function innleveringCompetitionThanks(): View
    {
        return view('frontend.competition.thank-you');
    }

    /**
     * Replay page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function replay(): View
    {
        $replays = Replay::latest()->get();

        return view('frontend.replay', compact('replays'));
    }

    public function barn(): View
    {
        return view('frontend.barn');
    }

    public function skrivdittliv(): View
    {
        return view('frontend.skrivdittliv');
    }

    public function hereIam(): View
    {
        $replays = Replay::latest()->get();

        return view('frontend.here-i-am', compact('replays'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function contract($code): View
    {
        $contract = Contract::where('code', $code)->firstOrFail();

        return view('frontend.contract', compact('contract'));
    }

    /**
     * @return mixed
     */
    public function contractDownload($code)
    {
        $contract = Contract::where('code', $code)->firstOrFail();
        $pdf = PDF::loadView('frontend.pdf.contract', compact('contract'));

        return $pdf->download($code.'.pdf');
    }

    public function contractSign($code, Request $request)
    {

        $contract = Contract::where('code', $code)->firstOrFail();

        if (! $request->signed || $contract->signature) {
            return redirect()->back();
        }

        $folderPath = 'storage/contract-signatures/'; // upload path
        if (! \File::exists($folderPath)) {
            \File::makeDirectory($folderPath);
        }

        $image_parts = explode(';base64,', $request->signed);

        $image_type_aux = explode('image/', $image_parts[0]);

        $image_type = $image_type_aux[1];

        $image_base64 = base64_decode($image_parts[1]);

        $file = $folderPath.uniqid().'.'.$image_type;
        file_put_contents($file, $image_base64);

        $contract->signature = $file;
        $contract->signed_date = Carbon::now();
        $contract->save();

        return back()->with('success', 'Contract signed successfully');
    }

    public function checkVippsOrderStatus($order_id)
    {
        $repository = new VippsRepository;
        $result = $repository->getAccessToken(); // get the access token

        if ($result instanceof ApiException) {
            return ApiResponse::error($result->getMessage(), $result->getData(), $result->getCode());
        }

        /*$access_token = $result['data']->access_token;
        $url = "/ecomm/v2/payments/$order_id/details";

        $header = array();
        $header[] = 'Accept: application/json;charset=UTF-8';
        $header[] = 'Authorization: '.$access_token;
        $header[] = 'Merchant-Serial-Number: '.env('VIPPS_MSN');
        $body = [];

        $response = AdminHelpers::vippsAPI('GET', $url, $body, $header);
        */

        $response = $repository->getPaymentDetails($order_id, $result['data']->access_token);

        return $response;

    }

    public function formatMoney($number): JsonResponse
    {
        return response()->json(\App\Http\FrontendHelpers::currencyFormat($number));
    }

    public function checkNearlyExpiredCourse()
    {
        \App\Http\AdminHelpers::checkNearlyExpiredCourses();

        return 'done';
    }

    public function langJS()
    {
        $strings = \Cache::rememberForever('lang.js', function () {
            $lang = config('app.locale');

            $files = glob(resource_path('lang/'.$lang.'/*.php'));
            $strings = [];

            foreach ($files as $file) {
                $name = basename($file, '.php');
                $strings[$name] = require $file;
            }

            return $strings;
        });

        header('Content-Type: text/javascript');
        echo 'window.i18n = '.json_encode($strings).';';
        exit();
    }

    public function powerOffice(PowerOffice $powerOffice)
    {
        $emailToSearch = 'elybutabara@gmail.com';

        $foundEntries = array_filter($powerOffice->customers(), function ($entry) use ($emailToSearch) {
            return $entry['EmailAddress'] === $emailToSearch;
        });

        if (! empty($foundEntries)) {
            // Email address found
            foreach ($foundEntries as $foundEntry) {
                // Process or display the found entry
                print_r($foundEntry);
            }
        } else {
            // Email address not found
            //return $powerOffice->registerCustomer();
        }
    }

    public function importWebinarRegistrants(): View
    {
        return view('frontend.import-webinar-registrant');
    }

    public function processImportWebinarRegistrants(Request $request)
    {
        Excel::import(new WebinarRegistrantsImport($request->link), request()->file('file'));
        echo 'after import';
    }

    public function application(Request $request)
    {
        if (request()->isMethod('post')) {
            $request->validate([
                'first_name' => 'required|alpha',
                'last_name' => 'required|alpha',
                'phone' => 'required',
                'email' => 'required|email',
                'address' => 'required',
                'zip' => 'required',
                'city' => 'required',
                'file' => 'required|mimes:doc,docx,pdf,txt,odt',
            ]);

            $data = $request->except('_token');

            if ($request->has('file')) {
                $file = FrontendHelpers::saveFile($request, 'application', 'file');
                $data['file'] = $file;
            }

            Application::create(
                $data
            );

            return redirect()->to('/thank-you');
        }

        return view('frontend.application');
    }

    public function forgetSessionKey($key)
    {
        // Remove from session
        session()->forget($key);

        return response()->json(['success' => true]);
    }

    public function exportCourseTakenByYear($year)
    {
        $coursesTaken = CoursesTaken::whereYear('created_at', $year)
            ->where('is_free', 0)
            ->get();

        $userList = [];
        foreach ($coursesTaken as $courseTaken) {
            $userList[] = [
                'name' => $courseTaken->user->full_name,
                'email' => $courseTaken->user->email,
                'course' => $courseTaken->package->course->title,
            ];
        }

        $headers = ['name', 'email', 'course'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), $year.' Bought Courses.xlsx');
    }

    public function exportCourseTakenByYearWithAdditionalCheck($year)
    {
        $coursesTaken = CoursesTaken::where('is_free', 0)
            ->whereYear('created_at', $year)
            ->whereNotNull('started_at')
            ->whereNotNull('end_date')
            ->whereNotIn('package_id', [261, 262, 282, 286])
            ->withTrashed()
            ->get()->map(function ($courseTaken) {
                $courseTaken->order = Order::where('user_id', $courseTaken->user_id)
                    ->where('package_id', $courseTaken->package_id)
                    ->where('type', 1)
                    ->first();

                return $courseTaken;
            });

        $userList = [];
        foreach ($coursesTaken as $courseTaken) {
            $userList[] = [
                'name' => $courseTaken->user->full_name,
                'email' => $courseTaken->user->email,
                'course' => $courseTaken->package->course->title,
                'price' => $courseTaken->order?->price,
                'discount' => $courseTaken->order?->discount,
                'total_amount' => $courseTaken->order?->price - $courseTaken->order?->discount
            ];
        }

        $headers = ['name', 'email', 'course', 'price', 'discount', 'total_amount'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($userList, $headers), $year.' Bought Courses.xlsx');
    }

    public function exportShopManuscriptsTakenByYear($year)
    {
        $manuscriptsTaken = ShopManuscriptsTaken::whereYear('created_at', $year)->get();

        $list = [];
        foreach ($manuscriptsTaken as $manuscriptTaken) {
            $list[] = [
                'name' => $manuscriptTaken->user->full_name,
                'email' => $manuscriptTaken->user->email,
                'manuscript' => $manuscriptTaken->shop_manuscript->title,
            ];
        }

        $headers = ['name', 'email', 'manuscript'];
        $excel = \App::make('excel');

        return $excel->download(new GenericExport($list, $headers), $year.' Bought Shop Manuscripts.xlsx');
    }

    public function saveManualInvoice(Request $request)
    {
        $learner = User::find($request->user_id);

        $paymentMode = PaymentMode::findOrFail(3);
        $payment_mode = 'Bankoverføring';

        $payment_plan = $request->payment_plan_in_months.' måneder';
        $divisor = $request->payment_plan_in_months;

        $inputtedComment = '';
        $comment = '('.$inputtedComment.' ';
        $comment .= 'Betalingsmodus: '.$payment_mode.', ';
        $comment .= 'Betalingsplan: '.$payment_plan.')';

        $product_ID = 884373255;

        $dueDate = $request->date;

        // always split the invoice
        $request->merge([
            'split_invoice' => 1
        ]);

        $division = $divisor * 100; // multiply the split count to get the correct value
        $price = (int) $request->amount * 100;
        $has_vat = false;

        $baseDate = Carbon::parse($dueDate); // starting due date

        $division = $divisor * 100; // multiply the split count to get the correct value
        $price = round($price / $division, 2); // round the value to the nearest tenths
        $price = (int) $price * 100;

        for ($i = 1; $i <= $divisor; $i++) { // loop based on the split count
            //$dueDate = Carbon::parse($dueDate)->addMonth($i)->format('Y-m-d'); // due date on every month on the same day
            $dueDate = $baseDate->copy()->addMonth($i)->format('Y-m-d');
            
            $invoice_fields = [
                'user_id' => $learner->id,
                'first_name' => $learner->first_name,
                'last_name' => $learner->last_name,
                'netAmount' => $price,
                'dueDate' => $dueDate,
                'description' => 'Kursordrefaktura',
                'productID' => $product_ID,
                'email' => $learner->email,
                'telephone' => $learner->address->telephone,
                'address' => $learner->address->street,
                'postalPlace' => $learner->address->city,
                'postalCode' => $learner->address->zip,
                'comment' => $comment,
                'payment_mode' => $paymentMode->mode,
                'index' => $i,
                'issueDate' => $request->issueDate ? $request->issueDate : date('Y-m-d')
            ];

            if ($request->product_type === 'manuscript_vat') {
                $invoice_fields['vat'] = ($price / 100) * 25;
                $has_vat = true;
            }

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields, $has_vat);
        }

        return redirect()->back()->with([
            'errors' => AdminHelpers::createMessageBag('Invoice created successfully.'),
            'alert_type' => 'success'
        ]);
    }
}
