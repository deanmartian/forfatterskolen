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
    public function uploadManuscriptTest(Request $request)
    {
        $word_count = 0;
        $filepath = '';
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) {
            $extension = pathinfo($_FILES['manuscript']['name'], PATHINFO_EXTENSION);
            $file = $request->file('manuscript');
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();

            $time = time();
            $destinationPath = 'storage/manuscript-tests/'; // upload path
            $fileName = $time.'.'.$extension; // rename document
            $filepath = $destinationPath.$fileName;
            $request->manuscript->move($destinationPath, $fileName);
            /* if($extension == "pdf") :
                $pdf  =  new \PdfToText ( $destinationPath.$fileName ) ;
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
                $docObj = new \Docx2Text($destinationPath.$fileName);
                $docText= $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "doc") :
                $docText = FrontendHelpers::readWord($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
                $doc = odt2text($destinationPath.$fileName);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count); */
            $extractText = FrontendHelpers::extractTextFromDocx($destinationPath.$fileName);
            $word_count = $extractText['word_count'];

            $providedWordCount = $request->input('word_count');
            if (is_numeric($providedWordCount)) {
                $providedWordCount = (int) $providedWordCount;

                if ($providedWordCount > 0) {
                    $word_count = $providedWordCount;
                }
            }
            /* $word_to_deduct = $word_count * 0.02;
            $word_count = ceil($word_count - $word_to_deduct); */
        }

        return [
            'manuscript_file' => $filepath,
            'word_count' => $word_count,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
        ];

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
            $uploadedManuscript = $this->uploadManuscriptTest($request);
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
            $newDirectory = 'shop-manuscripts';
            $filename = basename($originalPath);
            $newPath = $newDirectory . '/' . $filename;

            Storage::disk('public')->copy($originalPath, $newPath);
            $filePath = 'storage/'.$newPath;
            $word_count = $tempFile['word_count'];
        }

        OrderShopManuscript::create([
            'order_id' => $order_id,
            'genre' => $request->genre,
            'file' => '/'.$filePath,
            'words' => $word_count,
            'description' => $request->description,
            'synopsis' => $synopsis,
            'coaching_time_later' => filter_var($request->coaching_time_later, FILTER_VALIDATE_BOOLEAN),
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
}
