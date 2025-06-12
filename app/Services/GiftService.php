<?php

namespace App\Services;

use App\Address;
use App\CourseDiscount;
use App\CourseOrderAttachment;
use App\GiftPurchase;
use App\Http\AdminHelpers;
use App\Http\FrontendHelpers;
use App\Jobs\AddMailToQueueJob;
use App\Order;
use App\Package;
use App\ShopManuscript;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\SimpleType\DocProtect;

class GiftService
{
    public function processCheckout(Request $request, string $type = 'course'): JsonResponse
    {
        // update address
        Address::updateOrCreate(
            ['user_id' => \Auth::user()->id],
            $request->only('street', 'zip', 'city', 'phone')
        );

        return $this->sveaCheckout($request, $type);
    }

    public function sveaCheckout(Request $request, string $type = 'course'): JsonResponse
    {
        $discountedPrice = floatval($request->price);
        $merchantDataTitle = '';
        $checkoutUri = '';
        $confirmationUri = '';
        $terms = '';

        if ($type === 'course') {
            $package = Package::find($request->package_id);
            $course = $package->course;
            $discountedPrice = $this->calculateCourseDiscountedPrice($course, $package, $request);
            $merchantDataTitle = $course->title;
            $checkoutUri = '/gift/course/'.$course->id.'/checkout';
            $confirmationUri = '/gift/course/'.$course->id;
            $terms = '/terms/course-terms';
        }

        if ($type === 'shop-manuscript') {
            $shopManuscript = ShopManuscript::find($request->shop_manuscript_id);
            $merchantDataTitle = $shopManuscript->title;
            $checkoutUri = '/gift/shop-manuscript/'.$shopManuscript->id.'/checkout';
            $confirmationUri = '/gift/shop-manuscript/'.$shopManuscript->id;
            $terms = '/terms/manuscript-terms';
        }

        $discount = $request->price - $discountedPrice;
        $giftCard = $request->gift_card ? FrontendHelpers::gitCards($request->gift_card)['image'] : null;

        $request->merge(['discount' => $discount, 'gift_card' => $giftCard]);
        $orderRecord = $this->createOrder($request, $type);
        $checkoutMerchantId = config('services.svea.checkoutid');
        $checkoutSecret = config('services.svea.checkout_secret');

        // set endpoint url. Eg. test or prod
        $baseUrl = \Svea\Checkout\Transport\Connector::PROD_BASE_URL;

        $connector = \Svea\Checkout\Transport\Connector::init($checkoutMerchantId, $checkoutSecret, $baseUrl);
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
                'merchantData' => $merchantDataTitle.' order',
                'cart' => [
                    'items' => [
                        [
                            'name' => \Illuminate\Support\Str::limit($merchantDataTitle, 35),
                            'quantity' => 100,
                            'unitPrice' => $discountedPrice * 100,
                            'unit' => 'pc',
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
                    'termsUri' => url($terms),
                    'checkoutUri' => url($checkoutUri), // load checkout
                    'confirmationUri' => url($confirmationUri.'/thankyou?svea_ord='.$orderRecord->id),
                    'pushUri' => url('/svea-callback?svea_order_id={checkout.order.uri}'),
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
     * get the order details from svea
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function sveaOrderDetails($order)
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
                'orderId' => (int) $order->svea_order_id,
            ];
            // $response = $checkoutClient->deliverOrder($data);
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

    public function calculateCourseDiscountedPrice($course, $package, Request $request): int
    {

        $hasPaidCourse = false;

        foreach (\Auth::user()->coursesTakenNotOld as $courseTaken) {
            if ($courseTaken->package->course->type != 'Free' && $courseTaken->is_active) {
                if ($courseTaken->package->course->is_free != 1) {
                    $hasPaidCourse = true;
                }
                break;
            }
        }

        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $fromFull = \Carbon\Carbon::parse($package->full_payment_sale_price_from)->format('Y-m-d');
        $toFull = \Carbon\Carbon::parse($package->full_payment_sale_price_to)->format('Y-m-d');
        $isBetweenFull = (($today >= $fromFull) && ($today <= $toFull)) ? 1 : 0;

        $fromMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_from)->format('Y-m-d');
        $toMonths3 = \Carbon\Carbon::parse($package->months_3_sale_price_to)->format('Y-m-d');
        $isBetweenMonths3 = (($today >= $fromMonths3) && ($today <= $toMonths3)) ? 1 : 0;

        $fromMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_from)->format('Y-m-d');
        $toMonths6 = \Carbon\Carbon::parse($package->months_6_sale_price_to)->format('Y-m-d');
        $isBetweenMonths6 = (($today >= $fromMonths6) && ($today <= $toMonths6)) ? 1 : 0;

        // added 12th month
        $fromMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_from)->format('Y-m-d');
        $toMonths12 = \Carbon\Carbon::parse($package->months_12_sale_price_to)->format('Y-m-d');
        $isBetweenMonths12 = (($today >= $fromMonths12) && ($today <= $toMonths12)) ? 1 : 0;

        switch ($request->payment_plan_id) {
            case 1:
                $price = $isBetweenMonths3 && $package->months_3_sale_price
                    ? (int) $package->months_3_sale_price
                    : (int) $package->months_3_price;
                break;

            case 2:
                $price = $isBetweenMonths6 && $package->months_6_sale_price
                    ? (int) $package->months_6_sale_price
                    : (int) $package->months_6_price;
                break;

            case 4:
                $price = $isBetweenMonths12 && $package->months_12_sale_price
                    ? (int) $package->months_12_sale_price
                    : (int) $package->months_12_price;
                break;

            default:
                $price = $isBetweenFull && $package->full_payment_sale_price
                    ? (int) $package->full_payment_sale_price
                    : (int) $package->full_payment_price;
                break;
        }

        // check if the user has a paid course and the selected package have student discount
        if ($hasPaidCourse && $package->has_student_discount) {
            $price = $price - $course->student_discount;
        }

        if ($request->coupon) {
            $discountCoupon = CourseDiscount::where('coupon', $request->coupon)->where('course_id', $course->id)->first();

            if ($discountCoupon) {
                if ($discountCoupon->valid_to) {
                    $valid_from = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                    $valid_to = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                    $today = Carbon::today()->format('Y-m-d');

                    if (($today >= $valid_from) && ($today <= $valid_to)) {
                        // echo "valid date <br/>";
                    } else {
                        return $price;
                    }
                }

                $discount = ((int) $discountCoupon->discount);
                $price = $price - ((int) $discount);
            }

        }

        return $price;
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function createOrder(Request $request, string $parent = 'course')
    {
        $plan_id = $request->payment_plan_id;
        $totalPrice = $request->price;

        $item_id = 0;
        $type = '';
        $package_id = 0;
        if ($parent === 'course') {
            $package = Package::find($request->package_id);
            $item_id = $package->course_id;
            $type = Order::COURSE_TYPE;
            $package_id = $package->id;
        }

        if ($parent === 'shop-manuscript') {
            $item_id = $request->shop_manuscript_id;
            $type = Order::MANUSCRIPT_TYPE;
        }

        $newOrder['user_id'] = \Auth::user()->id;
        $newOrder['item_id'] = $item_id;
        $newOrder['type'] = $type;
        $newOrder['package_id'] = $package_id;
        $newOrder['plan_id'] = $plan_id;
        $newOrder['price'] = $totalPrice;
        $newOrder['discount'] = $request->discount;
        $newOrder['payment_mode_id'] = $request->payment_mode_id;
        $newOrder['is_processed'] = 0;
        $newOrder['gift_card'] = $request->gift_card;

        return Order::create($newOrder);
    }

    /**
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function addGiftPurchase($user_id, $parent, $parent_id)
    {

        do {
            $redeemCode = FrontendHelpers::generateUniqueCode(8);
        } while (GiftPurchase::where('redeem_code', $redeemCode)->first());

        return GiftPurchase::create([
            'user_id' => $user_id,
            'parent' => $parent,
            'parent_id' => $parent_id,
            'redeem_code' => $redeemCode,
            'expired_at' => Carbon::now()->addYear(3),
        ]);

    }

    /**
     * Send email to the buyer
     *
     * @return mixed
     */
    public function notifyGiftBuyer($giftPurchase, $order)
    {
        $user = $giftPurchase->buyer;
        $user_email = $user->email;

        $emailTemplate = AdminHelpers::emailTemplate('Gift Purchase');
        $emailContent = str_replace([
            ':redeem_code',
            ':redeem_link',
            ':end_redeem_link',
        ], [
            $giftPurchase->redeem_code,
            "<a class='btn buy-btn' href='".route('front.gift.show-redeem')."'>",
            '</a>',
        ], $emailTemplate->email_content);

        $giftAttachments = null;
        if ($giftPurchase->parent === 'course-package') {
            $package = $giftPurchase->coursePackage;
            $attachments = [asset($this->generateRegretForm($user->id, $package->id)),
                asset('/email-attachments/skjema-for-opplysninger-om-angrerett.docx')];

            dispatch(new AddMailToQueueJob($user_email, $package->course->title, '',
                'postmail@forfatterskolen.no', 'Forfatterskolen', $attachments,
                'gift-purchase', $giftPurchase->id));

        }

        if ($order->gift_card) {
            $giftAttachments[] = asset($order->gift_card);
        }

        dispatch(new AddMailToQueueJob($user_email, $emailTemplate->subject, $emailContent, $emailTemplate->from_email,
            null, $giftAttachments, 'gift-purchase', $giftPurchase->id));

        return $emailContent;
    }

    /**
     * Notify the admin regarding the purchase
     */
    public function notifyAdmin($giftPurchase)
    {
        $user = $giftPurchase->buyer;
        $itemName = '';
        if ($giftPurchase->parent === 'course-package') {
            $package = Package::find($giftPurchase->parent_id);
            $course = $package->course;
            $itemName = 'course '.$course->title;
        }

        if ($giftPurchase->parent === 'shop-manuscript') {
            $shopManuscript = ShopManuscript::find($giftPurchase->parent_id);
            $itemName = 'manuscript '.$shopManuscript->title;
        }

        $to = 'support@forfatterskolen.no';
        $from = 'post@forfatterskolen.no';
        $subject = 'New Gift Order';
        $message = $user->first_name.
            ' has purchased a gift '.$itemName;

        AdminHelpers::queue_mail($to, $subject, $message, $from);
    }

    /**
     * Generate regret form when user orders a course
     */
    public function generateRegretForm($user_id, $package_id): string
    {
        $user = User::find($user_id);
        $address = $user->address;
        $package = Package::find($package_id);
        $course = $package->course;

        $parseDate = Carbon::today()->addDays(13);
        if ($course->type === 'Group' && Carbon::today()->lt(Carbon::parse($course->start_date))) {
            $parseDate = Carbon::parse($course->start_date)->addDays(13);
        }

        $expirationDate = $parseDate->format('d.m.Y');
        $expirationDay = FrontendHelpers::convertDayLanguage($parseDate->format('N'));

        $phpWord = new \PhpOffice\PhpWord\PhpWord;
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        // prevent user from editing/copying from the file
        $documentProtection = $phpWord->getSettings()->getDocumentProtection();
        $documentProtection->setEditing(DocProtect::FORMS);

        $sectionStyle = [
            'marginTop' => 1150,
            'marginBottom' => 1150,
            'marginLeft' => 800,
            'marginRight' => 800,
        ];
        $section = $phpWord->addSection(
            $sectionStyle
        );

        $section->addText('Angreskjema',
            [
                'size' => 18,
            ],
            [
                'alignment' => 'center',
                'marginBottom' => 0,
                'space' => ['before' => 0, 'after' => 70],
            ]);

        $section->addText('ved kjøp av varer og tjenester som ikke er finansielle tjenester',
            ['size' => 10], [
                'alignment' => 'center',
                'space' => ['after' => 250],
            ]);

        $section->addText('Fyll ut og returner dette skjemaet dersom du ønsker å gå fra avtalen', [],
            [
                'alignment' => 'center',
                'space' => ['after' => 350],
            ]);

        $section->addText('Utfylt skjema sendes til:', [], [
            'space' => ['after' => 0],
        ]);
        $section->addText('(den næringsdrivende skal sette inn sitt navn, geografiske adresse og ev.'.
            'telefaksnummer og e-postadresse)', ['size' => 10], [
                'space' => ['after' => 350],
            ]);

        $width = 100 * 100;

        $table = $section->addTable([
            'width' => $width,
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('Forfatterskolen, Postboks 9233, 3064 DRAMMEN', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 150, 'after' => 0],
            'indent' => 0.1,
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('post@forfatterskolen.no', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 250, 'after' => 0],
            'indent' => 0.1,
        ]);

        $section->addTable($table);

        $listItemRun = $section->addTextRun([
            'space' => ['before' => 550],
        ]);
        $listItemRun->addText('Jeg/vi underretter herved om at jeg/vi ønsker å gå fra min/vår avtale om kjøp av følgende:');
        $listItemRun->addText(' (sett kryss)', ['size' => 10]);

        $checkBox = $section->addTextRun();
        $checkBox->addFormField('checkbox')->setValue(true);
        $checkBox->addText(' tjenester');
        $checkBox->addText(' (spesifiser på linjene nedenfor)', ['size' => 10]);

        $table = $section->addTable([
            'width' => $width,
        ]);
        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('Gjelder kjøp av '.$course->title, [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 150, 'after' => 0],
            'indent' => 0.1,
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addText('Frist for avbestilling for  å kunne benytte angreretten: Innen klokken 23.59 '
            .$expirationDay.' '.$expirationDate, [
                'bgColor' => 'CCCCCC',
            ], [
                'space' => ['before' => 150, 'after' => 0],
                'indent' => 0.1,
            ]);

        $section->addText('Sett kryss og dato:', ['size' => 10], [
            'space' => ['before' => 400],
        ]);

        $textRun = $section->addTextRun();
        $textRun->addFormField('checkbox')->setValue(true);
        $textRun->addText(' Avtalen ble inngått den');
        $textRun->addText(' (dato)', ['size' => 10]);
        $textRun->addText('     '); // spacing
        $textRun->addText(Carbon::today()->format('d.m.Y'), [
            'bgColor' => 'CCCCCC',
            'underline' => 'single',
        ]);
        $textRun->addText(' (ved kjøp av tjenester)', ['size' => 10]);

        $table = $section->addTable([
            'width' => $width,
        ]);
        $table->addRow(0);
        $table->addCell($width, [
            'height' => 1,
        ])->addText('Forbrukerens/forbrukemesnavn:', ['size' => 10], [
            'space' => ['before' => 500],
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addFormField('textinput', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 0, 'after' => 0],
            'indent' => 0.1,
        ])->setValue(' ');

        $table->addRow(0);
        $table->addCell($width, [
            'height' => 1,
        ])->addText('Forbrukerens/forbrukemes adresse:', ['size' => 10], [
            'space' => ['before' => 300, 'after' => 0],
        ]);

        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
            'height' => 1,
        ])->addFormField('textinput', [
            'bgColor' => 'CCCCCC',
        ], [
            'space' => ['before' => 200, 'after' => 0],
            'indent' => 0.1,
        ])->setValue(' ');

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell($width)->addTextRun([
            'space' => ['before' => 1800, 'after' => 0],
        ]);

        $cell->addText('Dato:', ['size' => 10]);
        $cell->addText('     '); // spacing
        $cell->addFormField('textinput', [
            'indent' => 2,
        ])->setValue('dd. dd. åååå');

        $table = $section->addTable();
        $table->addRow(0);
        $table->addCell($width, [
            'borderBottomSize' => 6,
        ])->addText('', [], [
            'space' => ['before' => 500, 'after' => 0],
        ]);

        $section->addText('Forbrukerens/forbrukemes underskrift (dersom papirskjema benyttes)',
            [
                'size' => 10,
            ],
            [
                'alignment' => 'center',
            ]);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $objWriter->save(public_path('email-attachments/angrerettskjema.docx'));

            $courseOrderAttachmentCopy = '/storage/course-order-attachments/'.
                str_replace(':', '-', $course->title).'-'.$user_id.'.docx';
            $objWriter->save(public_path($courseOrderAttachmentCopy));

            CourseOrderAttachment::create([
                'user_id' => $user_id,
                'course_id' => $course->id,
                'package_id' => $package_id,
                'file_path' => $courseOrderAttachmentCopy,
            ]);

            return 'email-attachments/angrerettskjema.docx';
        } catch (\Exception $e) {
            return '';
        }
    }
}
