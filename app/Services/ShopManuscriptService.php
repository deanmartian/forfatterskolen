<?php

namespace App\Services;

use App\Address;
use App\Http\AdminHelpers;
use App\Http\FikenInvoice;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\OrderShopManuscript;
use App\Services\FileIntegrityService;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Storage;
use Str;

class ShopManuscriptService
{
    protected FileIntegrityService $fileIntegrityService;

    public function __construct(FileIntegrityService $fileIntegrityService)
    {
        $this->fileIntegrityService = $fileIntegrityService;
    }

    public function uploadManuscriptTest(Request $request)
    {
        return $this->performManuscriptUpload($request, 'storage/manuscript-tests', [
            'word_count_field' => 'word_count',
        ]);
    }

    public function uploadLearnerManuscript(Request $request, int $userId): array
    {
        return $this->performManuscriptUpload($request, 'storage/shop-manuscripts', [
            'apply_word_margin' => true,
            'prepend_slash' => true,
            'user_id' => $userId,
            'path_resolver' => function (string $directory, string $extension, $file, ?int $resolvedUserId) {
                $filenameBase = $resolvedUserId ?? time();

                return AdminHelpers::checkFileName($directory, $filenameBase, $extension);
            },
            'word_count_field' => 'word_count',
        ]);
    }

    protected function performManuscriptUpload(Request $request, string $destinationDirectory, array $options = []): array
    {
        $inputName = $options['input_name'] ?? 'manuscript';
        $wordCountField = array_key_exists('word_count_field', $options)
            ? $options['word_count_field']
            : 'word_count';

        $word_count = 0;
        $filepath = '';
        $absolutePath = null;
        $integrityPassed = false;
        $originalName = null;
        $mimeType = null;

        if ($request->hasFile($inputName) && $request->file($inputName)->isValid()) {
            $file = $request->file($inputName);
            $extension = strtolower($file->getClientOriginalExtension());
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();

            $relativeDirectory = trim($destinationDirectory, '/');
            $providedWordCount = null;

            if ($wordCountField !== null) {
                $providedValue = $request->input($wordCountField);
                $providedWordCount = is_numeric($providedValue) ? (int) $providedValue : null;
            }

            $resolvedPath = null;
            if (isset($options['path_resolver']) && is_callable($options['path_resolver'])) {
                $resolvedPath = $options['path_resolver'](
                    $relativeDirectory,
                    $extension,
                    $file,
                    $options['user_id'] ?? null
                );
                $resolvedPath = $resolvedPath ? ltrim($resolvedPath, '/') : null;
            }

            if ($resolvedPath) {
                $filepath = $resolvedPath;
                $targetDirectory = trim(dirname($filepath), '.');
                $targetDirectory = $targetDirectory === '' ? $relativeDirectory : $targetDirectory;
                $absoluteDirectory = public_path($targetDirectory);

                if (! is_dir($absoluteDirectory)) {
                    mkdir($absoluteDirectory, 0755, true);
                }

                $file->move($absoluteDirectory, basename($filepath));
            } else {
                $absoluteDirectory = public_path($relativeDirectory);

                if (! is_dir($absoluteDirectory)) {
                    mkdir($absoluteDirectory, 0755, true);
                }

                $fileNameGenerator = $options['file_name_generator'] ?? function (string $extension) {
                    return time().'.'.$extension;
                };

                $fileName = $fileNameGenerator($extension);
                $file->move($absoluteDirectory, $fileName);

                $filepath = $relativeDirectory.'/'.$fileName;
            }

            $absolutePath = $this->resolveFilePath($filepath);
            $word_count = $this->calculateWordCount($absolutePath, $extension, $providedWordCount);
            $integrityPassed = $this->fileIntegrityService->passes($absolutePath, $extension);
        }

        if ($filepath !== '' && ($options['prepend_slash'] ?? false)) {
            $filepath = '/'.ltrim($filepath, '/');
        }

        return [
            'manuscript_file' => $filepath,
            'word_count' => $word_count,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'absolute_path' => $absolutePath,
            'integrity_passed' => $integrityPassed,
        ];

    }

    protected function calculateWordCount(?string $absolutePath, string $extension, ?int $providedWordCount): int
    {
        $extension = strtolower($extension);

        if (in_array($extension, ['doc', 'docx'], true) && $providedWordCount && $providedWordCount > 0) {
            return (int) $providedWordCount;
        }

        $wordCount = $absolutePath ? $this->determineWordCount($absolutePath, $extension) : 0;

        if ((! $wordCount || $wordCount < 0) && $providedWordCount && $providedWordCount > 0) {
            $wordCount = $providedWordCount;
        }

        return (int) max(0, $wordCount);
    }

    protected function resolveFilePath(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        if (is_file($path)) {
            return $path;
        }

        $trimmed = ltrim($path, '/');

        $publicPath = public_path($trimmed);
        if (is_file($publicPath)) {
            return $publicPath;
        }

        $basePath = base_path($trimmed);
        if (is_file($basePath)) {
            return $basePath;
        }

        return null;
    }

    protected function determineWordCount(string $filePath, string $extension): int
    {
        $extension = strtolower($extension);

        if (! is_file($filePath)) {
            return 0;
        }

        try {
            switch ($extension) {
                case 'pdf':
                    if (! class_exists('PdfToText')) {
                        if (file_exists(public_path('Pdf2Text.php'))) {
                            include_once public_path('Pdf2Text.php');
                        } elseif (file_exists(base_path('app/Http/Pdf2Text.php'))) {
                            include_once base_path('app/Http/Pdf2Text.php');
                        }
                    }

                    if (class_exists('PdfToText')) {
                        $pdf = new \PdfToText($filePath);
                        $text = $pdf->Text;

                        return FrontendHelpers::get_num_of_words($text);
                    }
                    break;
                case 'docx':
                    $extractText = FrontendHelpers::extractTextFromDocx($filePath);

                    return (int) ($extractText['word_count'] ?? 0);
                case 'doc':
                    $text = FrontendHelpers::readWord($filePath);

                    return $text ? FrontendHelpers::get_num_of_words($text) : 0;
                case 'odt':
                    if (! function_exists('odt2text')) {
                        if (file_exists(public_path('Odt2Text.php'))) {
                            include_once public_path('Odt2Text.php');
                        } elseif (file_exists(base_path('app/Http/Odt2Text.php'))) {
                            include_once base_path('app/Http/Odt2Text.php');
                        }
                    }

                    if (function_exists('odt2text')) {
                        $text = odt2text($filePath);

                        return FrontendHelpers::get_num_of_words($text);
                    }
                    break;
                default:
                    return 0;
            }
        } catch (\Throwable $throwable) {
            Log::error('Unable to determine manuscript word count.', [
                'file' => $filePath,
                'extension' => $extension,
                'error' => $throwable->getMessage(),
            ]);
        }

        return 0;
    }

    public function uploadSynopsis(Request $request)
    {
        $extensions = ['pdf', 'doc', 'docx', 'odt'];
        $synopsis = null;
        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) {
            $extension = pathinfo($_FILES['synopsis']['name'], PATHINFO_EXTENSION);

            if (! in_array($extension, $extensions)) {
                return redirect()->back();
            }

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $synopsis = '/'.$destinationPath.$fileName;
        }

        return $synopsis;
    }

    public function processCheckout(Request $request)
    {

        // this is for not logged in user
        $addressData = [
            'street' => $request->street,
            'zip' => $request->zip,
            'city' => $request->city,
            'phone' => $request->phone,
        ];
        $this->evaluateUser($request->email, $request->password, $request->first_name, $request->last_name, $addressData);

        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        /* removed because it's now showing as has_vat
        if (filter_var($request->is_pay_later, FILTER_VALIDATE_BOOLEAN)) {
            return $this->processPayLaterOrder($request);
        } */

        return $this->generateSveaCheckout($request);
    }

    public function evaluateUser($email, $password, $first_name, $last_name, $address)
    {
        if (\Auth::guest()) {
            $user = User::where('email', $email)->first();
            if ($user) {
                \Auth::login($user);
            } else {
                $new_user = User::create([
                    'email' => $email,
                    'password' => bcrypt($password),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                ]);
                \Auth::login($new_user);
                Address::create(array_merge($address, ['user_id' => $new_user->id]));
            }
        }
    }

    public function processPayLaterOrder(Request $request)
    {
        $orderRecord = $this->createOrder($request);

        if (! $request->has('order_type') ||
            ($request->has('order_type') && $request->order_type === Order::MANUSCRIPT_TYPE)) {
            $this->createOrderShopManuscript($orderRecord->id, $request);

            $shopManuscript = ShopManuscript::findOrFail($request->shop_manuscript_id);
            $user = $orderRecord->user;
            $price = $request->price;
            $dueDate = date('Y-m-d');
            $dueDate = Carbon::parse($dueDate);
            $dueDate->addDays($shopManuscript->full_price_due_date);

            $comment = '(Manuskript: '.$shopManuscript->title.', ';
            $comment .= 'Betalingsmodus: Faktura, ';
            $comment .= 'Betalingsplan: Hele beløpet)';

            $invoice_fields = [
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'netAmount' => $price * 100,
                'vat' => $request->additional * 100,
                'dueDate' => $dueDate->format('Y-m-d'),
                'description' => 'Kursordrefaktura',
                'productID' => 5686476118, // this is MVA productid //$shopManuscript->full_price_product,
                'email' => $user->email,
                'telephone' => $user->address->phone,
                'address' => $user->address->street,
                'postalPlace' => $user->address->city,
                'postalCode' => $user->address->zip,
                'comment' => $comment,
                'payment_mode' => 'Faktura',
            ];

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields, true);
        }

        $shopManuscript = ShopManuscript::find($orderRecord->item_id);
        $redirectUrl = url('/shop-manuscript/'.$shopManuscript->id.'/thankyou?pl_ord='.$orderRecord->id);

        if ($request->has('order_type') && $request->order_type === Order::MANUSCRIPT_UPGRADE_TYPE) {
            $redirectUrl = route('learner.upgrade', ['pl_ord' => $orderRecord->id]);
        }

        return [
            'redirect_url' => $redirectUrl,
        ];
    }

    public function generateSveaCheckout(Request $request)
    {
        $orderRecord = $this->createOrder($request);
        $userHasPaidCourse = FrontendHelpers::userHasPaidCourse();
        $vatPercent = ! $userHasPaidCourse ? 2500 : 0;  // 25% or 0

        if (! $request->has('order_type') ||
            ($request->has('order_type') && $request->order_type === Order::MANUSCRIPT_TYPE)) {
            $this->createOrderShopManuscript($orderRecord->id, $request);
        }

        $calculatedPrice = ($orderRecord->price + $orderRecord->additional) - $orderRecord->discount;
        $shopManuscript = ShopManuscript::find($orderRecord->item_id);

        $confirmationUrl = url('/shop-manuscript/'.$shopManuscript->id.'/thankyou?svea_ord='.$orderRecord->id);

        if ($request->has('order_type') && $request->order_type === Order::MANUSCRIPT_UPGRADE_TYPE) {
            $confirmationUrl = route('learner.upgrade', ['svea_ord' => $orderRecord->id]);
        }

        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        // set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::PROD_BASE_URL;

        try {
            /**
             * Create Connector object
             *
             * Exception \Svea\Checkout\Exception\SveaConnectorException will be returned if
             * some of fields $merchantId, $sharedSecret and $baseUrl is missing
             *
             *
             * Create Order
             *
             * Possible Exceptions are:
             * \Svea\Checkout\Exception\SveaInputValidationException - if $orderId is missing
             * \Svea\Checkout\Exception\SveaApiException - is there is some problem with api connection or
             *      some error occurred with data validation on API side
             * \Exception - for any other error
             */
            $conn = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
            $checkoutClient = new \Svea\Checkout\CheckoutClient($conn);

            /**
             * create order
             */
            $data = [
                'countryCode' => config('services.svea.country_code'),
                'currency' => config('services.svea.currency'),
                'locale' => config('services.svea.locale'),
                'clientOrderNumber' => config('services.svea.identifier').$orderRecord->id, // rand(10000,30000000),
                'merchantData' => $shopManuscript->title.' order',
                'cart' => [
                    'items' => [
                        [
                            'name' => \Illuminate\Support\Str::limit($shopManuscript->title, 35),
                            'quantity' => 100,
                            'unitPrice' => $calculatedPrice * 100,
                            'unit' => 'pc',
                            'vatPercent' => $vatPercent,
                        ],
                    ],
                ],
                'presetValues' => [
                    [
                        'typeName' => 'emailAddress',
                        'value' => $request->email,
                        'isReadonly' => false,
                    ],
                    [
                        'typeName' => 'postalCode',
                        'value' => $request->zip,
                        'isReadonly' => false,
                    ],
                    [
                        'typeName' => 'PhoneNumber',
                        'value' => $request->phone,
                        'isReadonly' => false,
                    ],
                ],
                'merchantSettings' => [
                    'termsUri' => url('/terms/manuscript-terms'),
                    'checkoutUri' => url('/shop-manuscript/'.$shopManuscript->id.'/checkout?t=1'), // load checkout
                    'confirmationUri' => $confirmationUrl,
                    'pushUri' => url('/svea-callback?svea_order_id={checkout.order.uri}'),
                    // "https://localhost:51925/push.php?svea_order_id={checkout.order.uri}",
                ],
            ];

            $response = $checkoutClient->create($data);
            $orderId = $response['OrderId'];
            $guiSnippet = $response['Gui']['Snippet'];
            $orderStatus = $response['Status'];
            $orderRecord->svea_order_id = $orderId;
            $orderRecord->save(); // update the checkout and save the order id from svea

            return $guiSnippet;

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

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createOrder(Request $request)
    {

        $orderType = Order::MANUSCRIPT_TYPE;
        $discount = $request->has('totalDiscount') ? $request->totalDiscount : $request->discount;
        if ($request->has('order_type')) {
            $orderType = $request->order_type;

            if ($orderType === Order::MANUSCRIPT_UPGRADE_TYPE) {
                $discount = 0;
            }
        }

        $newOrder['user_id'] = \Auth::user()->id;
        $newOrder['item_id'] = $request->shop_manuscript_id;
        $newOrder['type'] = $orderType;
        $newOrder['package_id'] = 0;
        $newOrder['plan_id'] = $request->payment_plan_id;
        $newOrder['price'] = $request->price;
        $newOrder['discount'] = $discount;
        $newOrder['payment_mode_id'] = $request->payment_mode_id;
        $newOrder['is_processed'] = 0;
        // commented out pay later since it's now creating an invoice
        // $newOrder['is_pay_later'] = filter_var($request->is_pay_later, FILTER_VALIDATE_BOOLEAN);

        if ($request->has('additional')) {
            $newOrder['additional'] = $request->additional;
        }

        $order = Order::create($newOrder);

        if ($orderType === Order::MANUSCRIPT_UPGRADE_TYPE) {
            $order->upgrade()->create([
                'parent' => $request->parent,
                'parent_id' => $request->parent_id,
            ]);
        }

        return $order;
    }

    public function createOrderShopManuscript($order_id, Request $request)
    {

        $word_count = 0;
        $filePath = null;
        $synopsis = null;
        $userId = (int) \Auth::id();

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            /* $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);

            $time = time();
            $destinationPath = 'storage/shop-manuscripts/'; // upload path
            $fileName = $time.'.'.$extension; // rename document
            $filePath = $destinationPath.$fileName;
            $request->manuscript->move($destinationPath, $fileName);

            if ($extension == 'pdf') {
                $pdf = new \PdfToText($filePath);
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            } elseif ($extension == 'docx') {
                $extractText = FrontendHelpers::extractTextFromDocx($filePath);
                $word_count = $extractText['word_count'];
            } elseif ($extension == 'doc') {
                $docText = FrontendHelpers::readWord($filePath);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            } elseif ($extension == 'odt') {
                $doc = odt2text($filePath);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            }
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count); */
            $uploadedManuscript = $this->uploadLearnerManuscript($request, $userId);

            $word_count = $uploadedManuscript['word_count'];
            $filePath = $uploadedManuscript['manuscript_file'];
        }

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) {
            $extension = pathinfo($_FILES['synopsis']['name'], PATHINFO_EXTENSION);

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $synopsis = '/'.$destinationPath.$fileName;
        }

        if ($request->temp_file && $request->temp_file !== 'null') {
            $tempFile = session('temp_uploaded_file');
            $fullPath = $tempFile['path'];
            $originalPath = Str::after($fullPath, 'storage/');
            $newDirectory = 'storage/shop-manuscripts/'; // need storage for checking unique name
            $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            $filename = AdminHelpers::checkFileName($newDirectory, $userId ?: time(), $extension);
            $newPath = $newDirectory.basename($filename);

            // remove storage for copying the file to new file name
            Storage::disk('public')->copy($originalPath, Str::after($newPath, 'storage/'));
            $filePath = '/'.$newPath;
            $word_count = $tempFile['word_count'];
        }

        // Coaching file upload (valgfritt manus for gjennomlesing)
        $coachingFilePath = null;
        $coachingFileName = null;
        if ($request->hasFile('coaching_file') && $request->file('coaching_file')->isValid()) {
            $coachingFile = $request->file('coaching_file');
            $coachingFileName = $coachingFile->getClientOriginalName();
            $coachingExt = strtolower($coachingFile->getClientOriginalExtension());
            $coachingDestination = 'storage/coaching-files/';

            if (! is_dir(public_path($coachingDestination))) {
                mkdir(public_path($coachingDestination), 0755, true);
            }

            $coachingNewName = time() . '_coaching.' . $coachingExt;
            $coachingFile->move(public_path($coachingDestination), $coachingNewName);
            $coachingFilePath = '/' . $coachingDestination . $coachingNewName;
        }

        OrderShopManuscript::create([
            'order_id' => $order_id,
            'genre' => $request->genre,
            'file' => $filePath ? '/'.ltrim($filePath, '/') : null,
            'words' => $word_count,
            'description' => $request->description,
            'synopsis' => $synopsis,
            'coaching_time_later' => in_array((int) $request->coaching_time_later, [30, 60]) ? (int) $request->coaching_time_later : 0,
            'coaching_topic' => $request->coaching_topic,
            'coaching_file' => $coachingFilePath,
            'coaching_file_name' => $coachingFileName,
            'send_to_email' => filter_var($request->send_to_email, FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function addShopManuscriptToLearner($order): ShopManuscriptsTaken
    {
        $shopManuscriptOrder = $order->shopManuscriptOrder;
        $file = $shopManuscriptOrder->file;
        if (strpos($shopManuscriptOrder->file, 'manuscript-tests') !== false) {
            $destinationPath = '/storage/shop-manuscripts/'.basename($file);
            \File::copy(public_path($file), public_path($destinationPath));
            $file = $destinationPath;
        }

        $shopManuscriptTaken = new ShopManuscriptsTaken;
        $shopManuscriptTaken->user_id = $order->user_id;
        $shopManuscriptTaken->genre = $shopManuscriptOrder->genre;
        $shopManuscriptTaken->description = $shopManuscriptOrder->description;
        $shopManuscriptTaken->shop_manuscript_id = $order->item_id;
        $shopManuscriptTaken->file = $file;
        $shopManuscriptTaken->words = $shopManuscriptOrder->words;
        $shopManuscriptTaken->synopsis = $shopManuscriptOrder->synopsis;
        $shopManuscriptTaken->is_active = false;
        $shopManuscriptTaken->coaching_time_later = $shopManuscriptOrder->coaching_time_later;
        $shopManuscriptTaken->is_welcome_email_sent = 0;
        $shopManuscriptTaken->is_pay_later = $order->is_pay_later;
        $shopManuscriptTaken->save();

        return $shopManuscriptTaken;
    }

    public function upgradeShopManuscript($order)
    {
        $orderUpgrade = $order->upgrade;
        $shopManuscriptTaken = ShopManuscriptsTaken::find($orderUpgrade->parent_id);
        // change the manuscript plan/package
        $shopManuscriptTaken->shop_manuscript_id = $order->item_id;
        $shopManuscriptTaken->save();
    }

    public function notifyAdmin($order)
    {
        $user = $order->user;
        $shopManuscript = ShopManuscript::find($order->item_id);

        $message = $user->full_name.' submitted a manuscript for shop manuscript '.$shopManuscript->title;
        $headEditor = User::where('head_editor', 1)->first();
        $to = $headEditor->email; // 'Camilla@forfatterskolen.no'; head editor email
        $emailData = [
            'email_subject' => 'New manuscript submitted for shop manuscript',
            'email_message' => $message,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => null,
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
    }

    public function notifyUser($order, $shopManuscriptTaken)
    {
        // Send Email
        $user = $order->user;
        $user_email = $user->email;
        $emailTemplate = AdminHelpers::emailTemplate('Shop Manuscript Welcome Email');
        $emailContent = AdminHelpers::formatEmailContent($emailTemplate->email_content, $user_email,
            $user->first_name, 'shop-manuscripts-taken');

        dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent,
            $emailTemplate->from_email, null, null, 'shop-manuscripts-taken', $shopManuscriptTaken->id));
    }

    public function createInvoiceFromOder($order)
    {
        Log::info('inside createInvoiceFromOder');
        $user = $order->user;
        $price = $order->price - $order->discount;
        $shopManuscript = ShopManuscript::find($order->item_id);
        $dueDate = date('Y-m-d');
        $dueDate = Carbon::parse($dueDate);
        $dueDate->addDays($shopManuscript->full_price_due_date);
        $dueDate = $dueDate->format('Y-m-d');
        $comment = '(Manuskript: '.$shopManuscript->title.', ';
        $comment .= 'Betalingsmodus: Vipps, ';
        $comment .= 'Betalingsplan: Hele beløpet)';

        $invoice_fields = [
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'netAmount' => $price * 100,
            'dueDate' => $dueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $shopManuscript->fiken_product,
            'email' => $user->email,
            'telephone' => $user->address->phone,
            'address' => $user->address->street,
            'postalPlace' => $user->address->city,
            'postalCode' => $user->address->zip,
            'comment' => $comment,
            'payment_mode' => 'Vipps',
        ];
        Log::info(json_encode($invoice_fields));
        $invoice = new FikenInvoice;
        $invoice->create_invoice($invoice_fields);
        Log::info('after create invoice');
    }

    /**
     * Opprett Fiken-faktura for ny betalingsflyt (Vipps/Kredittkort/Faktura)
     *
     * @param Order $order
     * @param string $paymentMethod  'Vipps', 'Kredittkort', eller 'Faktura'
     * @param bool $hasVat           true = med mva, false = uten mva (aktiv elev)
     * @param string $installmentPlan 'full' eller '3months'
     */
    public function createInvoiceForOrder(Order $order, string $paymentMethod, bool $hasVat, string $installmentPlan = 'full')
    {
        $user = $order->user;
        $shopManuscript = ShopManuscript::find($order->item_id);
        $basePrice = $order->price;
        $mva = $order->additional ?? 0;

        // Velg riktig Fiken produkt-ID
        $productId = $hasVat ? 5686476118 : 884373255;

        if ($installmentPlan === '3months') {
            // 3 måneder delbetaling — 3 fakturaer
            $totalPrice = $basePrice + $mva;
            $perInstallment = round($totalPrice / 3);
            $perInstallmentBase = round($basePrice / 3);
            $perInstallmentMva = round($mva / 3);

            for ($i = 1; $i <= 3; $i++) {
                // Siste faktura tar rest for å unngå avrundingsfeil
                if ($i === 3) {
                    $thisBase = $basePrice - ($perInstallmentBase * 2);
                    $thisMva = $mva - ($perInstallmentMva * 2);
                } else {
                    $thisBase = $perInstallmentBase;
                    $thisMva = $perInstallmentMva;
                }

                $dueDate = Carbon::now()->addMonths($i)->format('Y-m-d');

                $comment = '(Manuskript: ' . $shopManuscript->title;
                $shopManuscriptOrder = $order->shopManuscriptOrder;
                if ($shopManuscriptOrder && $shopManuscriptOrder->coaching_time_later > 0) {
                    $comment .= ', Coaching: ' . $shopManuscriptOrder->coaching_time_later . ' min';
                }
                $comment .= ', Betalingsmodus: ' . $paymentMethod . ', ';
                $comment .= 'Betalingsplan: 3 måneder - del ' . $i . ' av 3)';

                $invoice_fields = [
                    'user_id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'netAmount' => $thisBase * 100,
                    'dueDate' => $dueDate,
                    'description' => 'Kursordrefaktura',
                    'productID' => $productId,
                    'email' => $user->email,
                    'telephone' => optional($user->address)->phone,
                    'address' => optional($user->address)->street,
                    'postalPlace' => optional($user->address)->city,
                    'postalCode' => optional($user->address)->zip,
                    'comment' => $comment,
                    'payment_mode' => $paymentMethod,
                    'index' => $i, // Kun faktura 1 sendes automatisk
                ];

                if ($hasVat) {
                    $invoice_fields['vat'] = $thisMva * 100;
                }

                $invoice = new FikenInvoice;
                $invoice->create_invoice($invoice_fields, $hasVat);
            }
        } else {
            // Hele beløpet — én faktura
            $dueDate = Carbon::now()->addDays($shopManuscript->full_price_due_date)->format('Y-m-d');

            $comment = '(Manuskript: ' . $shopManuscript->title;
            $shopManuscriptOrder = $order->shopManuscriptOrder;
            if ($shopManuscriptOrder && $shopManuscriptOrder->coaching_time_later > 0) {
                $comment .= ', Coaching: ' . $shopManuscriptOrder->coaching_time_later . ' min';
            }
            $comment .= ', Betalingsmodus: ' . $paymentMethod . ', ';
            $comment .= 'Betalingsplan: Hele beløpet)';

            $invoice_fields = [
                'user_id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'netAmount' => $basePrice * 100,
                'dueDate' => $dueDate,
                'description' => 'Kursordrefaktura',
                'productID' => $productId,
                'email' => $user->email,
                'telephone' => optional($user->address)->phone,
                'address' => optional($user->address)->street,
                'postalPlace' => optional($user->address)->city,
                'postalCode' => optional($user->address)->zip,
                'comment' => $comment,
                'payment_mode' => $paymentMethod,
            ];

            if ($hasVat) {
                $invoice_fields['vat'] = $mva * 100;
            }

            $invoice = new FikenInvoice;
            $invoice->create_invoice($invoice_fields, $hasVat);
        }
    }
}
