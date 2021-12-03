<?php
namespace App\Services;

use App\Address;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\OrderShopManuscript;
use App\ShopManuscript;
use App\ShopManuscriptsTaken;
use App\User;
use Illuminate\Http\Request;

class ShopManuscriptService {

    /**
     * @param Request $request
     * @return int
     */
    public function countManuscriptWord( Request $request )
    {
        $word_count = 0;
        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);

            $time = time();
            $destinationPath = 'storage/manuscript-tests/'; // upload path
            $fileName = $time.'.'.$extension; // rename document
            $filePath = $destinationPath.$fileName;
            $request->manuscript->move($destinationPath, $fileName);

            if($extension == "pdf") :
                $pdf  =  new \PdfToText ( $filePath ) ;
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
                $docObj = new \Docx2Text($filePath);
                $docText= $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "doc") :
                $docText = FrontendHelpers::readWord($filePath);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
                $doc = odt2text($filePath);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count);
        endif;

        return $word_count;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processCheckout( Request $request )
    {

        // this is for not logged in user
        $addressData = [
            'street' => $request->street,
            'zip' => $request->zip,
            'city' => $request->city,
            'phone' => $request->phone
        ];
        $this->evaluateUser($request->email, $request->password, $request->first_name, $request->last_name, $addressData);

        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        return $this->generateSveaCheckout($request);
    }

    /**
     * @param $email
     * @param $password
     * @param $first_name
     * @param $last_name
     * @param $address
     */
    public function evaluateUser( $email, $password, $first_name, $last_name, $address )
    {
        if( \Auth::guest() ) :
            $user = User::where('email', $email)->first();
            if( $user ) :
                \Auth::login($user);
            else :
                $new_user = User::create([
                    'email' => $email,
                    'password' => bcrypt($password),
                    'first_name' => $first_name,
                    'last_name' => $last_name
                ]);
                \Auth::login($new_user);
                Address::create(array_merge($address, ['user_id' => $new_user->id]));
            endif;
        endif;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSveaCheckout( Request $request )
    {
        $orderRecord = $this->createOrder($request);

        if (!$request->has('order_type') ||
            ($request->has('order_type') && $request->order_type === Order::MANUSCRIPT_TYPE)) {
            $this->createOrderShopManuscript($orderRecord->id, $request);
        }

        $calculatedPrice = $orderRecord->price - $orderRecord->discount;
        $shopManuscript = ShopManuscript::find($orderRecord->item_id);

        $confirmationUrl = url('/shop-manuscript/' . $shopManuscript->id .'/thankyou?svea_ord='.$orderRecord->id);

        if ($request->has('order_type') && $request->order_type === Order::MANUSCRIPT_UPGRADE_TYPE) {
            $confirmationUrl = route('learner.upgrade',['svea_ord' => $orderRecord->id]);
        }

        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        //set endpoint url. Eg. test or prod
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
            $data = array(
                "countryCode" => env('SVEA_COUNTRY_CODE'),
                "currency" => env('SVEA_CURRENCY'),
                "locale" => env('SVEA_LOCALE'),
                "clientOrderNumber" => env('SVEA_IDENTIFIER').$orderRecord->id,//rand(10000,30000000),
                "merchantData" => $shopManuscript->title." order",
                "cart" => array(
                    "items" => array(
                        array(
                            "name" => str_limit($shopManuscript->title, 35),
                            "quantity" => 100,
                            "unitPrice" => $calculatedPrice*100,
                            "unit" => "pc"
                        )
                    )
                ),
                "presetValues" => array(
                    array(
                        "typeName" => "emailAddress",
                        "value" => $request->email,
                        "isReadonly" => false
                    ),
                    array(
                        "typeName" => "postalCode",
                        "value" => $request->zip,
                        "isReadonly" => false
                    ),
                    array(
                        "typeName" => "PhoneNumber",
                        "value" => $request->phone,
                        "isReadonly" => false
                    )
                ),
                "merchantSettings" => array(
                    "termsUri" => url('/terms/manuscript-terms'),
                    "checkoutUri" => url('/shop-manuscript/' . $shopManuscript->id . '/checkout?t=1'), // load checkout
                    "confirmationUri" => $confirmationUrl,
                    "pushUri" => url('/svea-callback?svea_order_id={checkout.order.uri}')
                    //"https://localhost:51925/push.php?svea_order_id={checkout.order.uri}",
                )
            );

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
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createOrder( Request $request )
    {

        $orderType = Order::MANUSCRIPT_TYPE;
        $discount = $request->discount;
        if ($request->has('order_type')) {
            $orderType = $request->order_type;

            if ($orderType === Order::MANUSCRIPT_UPGRADE_TYPE) {
                $discount = 0;
            }
        }

        $newOrder['user_id']    = \Auth::user()->id;
        $newOrder['item_id']    = $request->shop_manuscript_id;
        $newOrder['type']       = $orderType;
        $newOrder['package_id'] = 0;
        $newOrder['plan_id']    = $request->payment_plan_id;
        $newOrder['price']      = $request->price;
        $newOrder['discount']   = $discount;
        $newOrder['payment_mode_id']   = $request->payment_mode_id;
        $newOrder['is_processed'] = 0;

        $order = Order::create($newOrder);

        if ($orderType === Order::MANUSCRIPT_UPGRADE_TYPE) {
            $order->upgrade()->create([
                'parent' => $request->parent,
                'parent_id' => $request->parent_id
            ]);
        }

        return $order;
    }

    /**
     * @param $order_id
     * @param Request $request
     */
    public function createOrderShopManuscript( $order_id, Request $request )
    {

        $word_count = 0;
        $filePath = NULL;
        $synopsis = NULL;

        if ($request->hasFile('manuscript') && $request->file('manuscript')->isValid()) :;
            $extension = pathinfo($_FILES['manuscript']['name'],PATHINFO_EXTENSION);

            $time = time();
            $destinationPath = 'storage/shop-manuscripts/'; // upload path
            $fileName = $time.'.'.$extension; // rename document
            $filePath = $destinationPath.$fileName;
            $request->manuscript->move($destinationPath, $fileName);

            if($extension == "pdf") :
                $pdf  =  new \PdfToText ( $filePath ) ;
                $pdf_content = $pdf->Text;
                $word_count = FrontendHelpers::get_num_of_words($pdf_content);
            elseif($extension == "docx") :
                $docObj = new \Docx2Text($filePath);
                $docText= $docObj->convertToText();
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "doc") :
                $docText = FrontendHelpers::readWord($filePath);
                $word_count = FrontendHelpers::get_num_of_words($docText);
            elseif($extension == "odt") :
                $doc = odt2text($filePath);
                $word_count = FrontendHelpers::get_num_of_words($doc);
            endif;
            $word_count = FrontendHelpers::wordCountByMargin((int) $word_count);
        endif;

        if ($request->hasFile('synopsis') && $request->file('synopsis')->isValid()) :
            $extension = pathinfo($_FILES['synopsis']['name'],PATHINFO_EXTENSION);

            $time = time();
            $destinationPath = 'storage/shop-manuscripts-synopsis/';
            $fileName = $time.'.'.$extension; // rename document
            $request->synopsis->move($destinationPath, $fileName);
            $synopsis = '/'.$destinationPath.$fileName;
        endif;

        OrderShopManuscript::create([
           'order_id' => $order_id,
            'genre' => $request->genre,
            'file' => '/'.$filePath,
            'words' => $word_count,
            'description' => $request->description,
            'synopsis' => $synopsis,
            'coaching_time_later' => filter_var($request->coaching_time_later, FILTER_VALIDATE_BOOLEAN),
            'send_to_email' => filter_var($request->send_to_email, FILTER_VALIDATE_BOOLEAN)
        ]);
    }

    /**
     * @param $order
     * @return ShopManuscriptsTaken
     */
    public function addShopManuscriptToLearner( $order )
    {
        $shopManuscriptOrder = $order->shopManuscriptOrder;
        $shopManuscriptTaken                        = new ShopManuscriptsTaken();
        $shopManuscriptTaken->user_id               = $order->user_id;
        $shopManuscriptTaken->genre                 = $shopManuscriptOrder->genre;
        $shopManuscriptTaken->description           = $shopManuscriptOrder->description;
        $shopManuscriptTaken->shop_manuscript_id    = $order->item_id;
        $shopManuscriptTaken->file                  = $shopManuscriptOrder->file;
        $shopManuscriptTaken->words                 = $shopManuscriptOrder->words;
        $shopManuscriptTaken->synopsis              = $shopManuscriptOrder->synopsis;
        $shopManuscriptTaken->is_active             = false;
        $shopManuscriptTaken->coaching_time_later   = $shopManuscriptOrder->coaching_time_later;
        $shopManuscriptTaken->is_welcome_email_sent = 0;
        $shopManuscriptTaken->save();

        return $shopManuscriptTaken;
    }

    public function upgradeShopManuscript( $order )
    {
        $orderUpgrade = $order->upgrade;
        $shopManuscriptTaken = ShopManuscriptsTaken::find($orderUpgrade->parent_id);
        // change the manuscript plan/package
        $shopManuscriptTaken->shop_manuscript_id = $order->item_id;
        $shopManuscriptTaken->save();
    }

    /**
     * @param $order
     */
    public function notifyAdmin( $order )
    {
        $user = $order->user;
        $shopManuscript = ShopManuscript::find($order->item_id);

        $message = $user->full_name.' submitted a manuscript for shop manuscript '.$shopManuscript->title;
        $to = 'Camilla@forfatterskolen.no';
        $emailData = [
            'email_subject' => 'New manuscript submitted for shop manuscript',
            'email_message' => $message,
            'from_name' => '',
            'from_email' => 'post@forfatterskolen.no',
            'attach_file' => NULL
        ];
        \Mail::to($to)->queue(new SubjectBodyEmail($emailData));
    }

    /**
     * @param $order
     * @param $shopManuscriptTaken
     */
    public function notifyUser( $order, $shopManuscriptTaken )
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
}