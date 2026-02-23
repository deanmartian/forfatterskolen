<?php

namespace App\Http\Controllers\Api\V1;

use App\CheckoutLog;
use App\Course;
use App\CourseDiscount;
use App\Http\FikenInvoice;
use App\Jobs\SveaUpdateOrderDetailsJob;
use App\Mail\SubjectBodyEmail;
use App\Order;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\Http\FrontendHelpers;
use App\Jobs\SveaUpdateOrderDetailsTestJob;
use App\Services\CourseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends ApiController
{
    public function discount(Request $request, int $courseId, CourseService $courseService): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        Auth::setUser($user);

        $course = Course::query()->where('for_sale', 1)->find($courseId);

        if (! $course) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        if ($course->pay_later_with_application) {
            return $this->errorResponse('Course requires application checkout.', 'forbidden', 403);
        }

        if ($course->hide_price) {
            return $this->errorResponse('Course is not available for direct checkout.', 'forbidden', 403);
        }

        if (! $user->could_buy_course) {
            return $this->errorResponse('You are not allowed to buy courses.', 'forbidden', 403);
        }

        $coursePackages = $course->packages->pluck('id')->toArray();
        $alreadyTaken = $coursePackages
            ? $user->coursesTaken()->whereIn('package_id', $coursePackages)->exists()
            : false;

        if ($alreadyTaken) {
            return $this->errorResponse('You already have access to this course.', 'forbidden', 403);
        }

        $validator = Validator::make($request->all(), [
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'payment_plan_id' => ['required', 'integer', 'exists:payment_plans,id'],
            'coupon' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, $validator->errors()->toArray());
        }

        $validated = $validator->validated();
        $package = Package::find($validated['package_id']);
        $paymentPlan = PaymentPlan::find($validated['payment_plan_id']);

        if (! $package || (int) $package->course_id !== $course->id) {
            return $this->errorResponse('Package not available for this course.', 'forbidden', 403);
        }

        if (! $paymentPlan) {
            return $this->errorResponse('Payment plan not found.', 'not_found', 404);
        }

        if (! $this->planIsEnabled($package, (int) $paymentPlan->division)) {
            return $this->errorResponse('Payment plan not available for this package.', 'forbidden', 403);
        }

        $coupon = $validated['coupon'] ?? null;
        if ($coupon) {
            $discountCoupon = CourseDiscount::query()
                ->where('course_id', $course->id)
                ->whereRaw('BINARY coupon = ?', [$coupon])
                ->first();

            if (! $discountCoupon) {
                return $this->errorResponse('Invalid coupon.', 'validation_error', 422, [
                    'coupon' => ['Invalid coupon.'],
                ]);
            }

            if ($discountCoupon->valid_to) {
                $validFrom = Carbon::parse($discountCoupon->valid_from)->format('Y-m-d');
                $validTo = Carbon::parse($discountCoupon->valid_to)->format('Y-m-d');
                $today = Carbon::today()->format('Y-m-d');

                if (! (($today >= $validFrom) && ($today <= $validTo))) {
                    return $this->errorResponse('Coupon expired.', 'validation_error', 422, [
                        'coupon' => ['Coupon expired.'],
                    ]);
                }
            }
        }

        $payload = [
            'package_id' => $package->id,
            'payment_plan_id' => $paymentPlan->id,
            'coupon' => $coupon,
        ];

        $pricingRequest = new Request($payload);
        $baseRequest = new Request(array_merge($payload, ['coupon' => null]));

        $price = $courseService->calculatePlanPrice($course, $package, (int) $paymentPlan->division, $pricingRequest);
        $basePrice = $courseService->calculatePlanPrice($course, $package, (int) $paymentPlan->division, $baseRequest);
        $discount = max(0, $basePrice - $price);

        return response()->json([
            'course_id' => $course->id,
            'package_id' => $package->id,
            'payment_plan_id' => $paymentPlan->id,
            'base_price' => $basePrice,
            'price' => $price,
            'discount' => $discount,
            'coupon' => $coupon,
        ]);
    }

    public function startCourseCheckout(Request $request, int $courseId, CourseService $courseService): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        Auth::setUser($user);

        $course = Course::query()->where('for_sale', 1)->find($courseId);

        if (! $course) {
            return $this->errorResponse('Course not found.', 'not_found', 404);
        }

        if ($course->pay_later_with_application) {
            return $this->errorResponse('Course requires application checkout.', 'forbidden', 403);
        }

        if ($course->hide_price) {
            return $this->errorResponse('Course is not available for direct checkout.', 'forbidden', 403);
        }

        if (! $user->could_buy_course) {
            return $this->errorResponse('You are not allowed to buy courses.', 'forbidden', 403);
        }

        $coursePackages = $course->packages->pluck('id')->toArray();
        $alreadyTaken = $coursePackages
            ? $user->coursesTaken()->whereIn('package_id', $coursePackages)->exists()
            : false;

        if ($alreadyTaken) {
            return $this->errorResponse('You already have access to this course.', 'forbidden', 403);
        }

        $payload = array_merge([
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'street' => optional($user->address)->street ?? '',
            'zip' => optional($user->address)->zip ?? '',
            'city' => optional($user->address)->city ?? '',
            'phone' => optional($user->address)->phone ?? '',
        ], $request->all());

        if (empty($payload['payment_plan_id']) && ! empty($payload['payment_mode_id'])) {
            $paymentMode = PaymentMode::find($payload['payment_mode_id']);

            if ($paymentMode && $paymentMode->mode === 'Vipps') {
                $defaultPlan = PaymentPlan::query()
                    ->where('division', 1)
                    ->orderBy('id')
                    ->first();

                if ($defaultPlan) {
                    $payload['payment_plan_id'] = $defaultPlan->id;
                }
            }
        }

        $validator = Validator::make($payload, [
            'email' => ['required', 'email'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'street' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'city' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'payment_mode_id' => ['required', 'integer', 'exists:payment_modes,id'],
            'payment_plan_id' => ['required', 'integer', 'exists:payment_plans,id'],
            'coupon' => ['nullable', 'string'],
            'is_pay_later' => ['nullable', 'boolean'],
            'fallbackUrl' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, $validator->errors()->toArray());
        }

        $validated = $validator->validated();
        $package = Package::find($validated['package_id']);
        $paymentMode = PaymentMode::find($validated['payment_mode_id']);
        $paymentPlan = PaymentPlan::find($validated['payment_plan_id']);

        if (! $package || (int) $package->course_id !== $course->id) {
            return $this->errorResponse('Package not available for this course.', 'forbidden', 403);
        }

        if (! $paymentMode) {
            return $this->errorResponse('Payment mode not found.', 'not_found', 404);
        }

        if (! $paymentPlan) {
            return $this->errorResponse('Payment plan not found.', 'not_found', 404);
        }

        if (! $this->planIsEnabled($package, (int) $paymentPlan->division)) {
            return $this->errorResponse('Payment plan not available for this package.', 'forbidden', 403);
        }

        if (($validated['is_pay_later'] ?? false) && ! in_array((int) $paymentPlan->division, [1, 3, 6, 12], true)) {
            return $this->errorResponse('Fiken checkout støtter kun betalingsplanene 1, 3, 6 eller 12 måneder.', 'validation_error', 422, [
                'payment_plan_id' => ['Ugyldig betalingsplan for Fiken. Tillatte verdier er 1, 3, 6 eller 12 måneder.'],
            ]);
        }

        if (! in_array($paymentMode->mode, ['Vipps', 'Paypal', 'Faktura'], true)) {
            return $this->errorResponse('Unsupported payment mode for API checkout.', 'validation_error', 422);
        }

        if (($validated['is_pay_later'] ?? false) && $paymentMode->mode !== 'Faktura') {
            return $this->errorResponse('Fiken checkout må bruke Faktura betalingsmodus.', 'validation_error', 422, [
                'payment_mode_id' => ['Fiken checkout må bruke Faktura betalingsmodus.'],
            ]);
        }

        $isPayLaterCheckout = (bool) ($validated['is_pay_later'] ?? false);

        if ($isPayLaterCheckout) {
            $allowedPaymentPlanIds = collect($course->payment_plan_ids ?? [])
                ->map(static fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->values();

            if ($allowedPaymentPlanIds->isEmpty() || ! $allowedPaymentPlanIds->contains((int) $paymentPlan->id)) {
                return $this->errorResponse('Pay later er ikke tilgjengelig for valgt betalingsplan.', 'validation_error', 422, [
                    'payment_plan_id' => ['Valgt betalingsplan er ikke tillatt for pay later på dette kurset.'],
                ]);
            }
        }

        $payload = array_merge($payload, [
            'payment_mode_id' => $paymentMode->id,
            'payment_plan_id' => $paymentPlan->id,
            'package_id' => $package->id,
        ]);

        $request->replace($payload);

        $baseRequest = new Request(array_merge($request->all(), ['coupon' => null]));
        $basePrice = $courseService->calculatePlanPrice($course, $package, (int) $paymentPlan->division, $baseRequest);
        $finalPrice = $courseService->calculatePlanPrice($course, $package, (int) $paymentPlan->division, $request);
        $discount = max(0, $basePrice - $finalPrice);

        $request->merge([
            'price' => $basePrice,
            'discount' => $discount,
            'is_pay_later' => (bool) ($validated['is_pay_later'] ?? false),
        ]);

        if ($paymentMode->mode === 'Paypal') {
            return $this->errorResponse('Paypal checkout is not supported via API.', 'validation_error', 422);
        }

        if ($paymentMode->mode === 'Faktura' && $request->boolean('is_pay_later')) {
            $order = $courseService->createOrder($request);
            $this->createFikenInvoiceForCourseOrder($order, $package, $paymentPlan, $request, $finalPrice);
            $result = [
                'order' => $order,
                'redirect_url' => url('/thankyou?pl_ord='.$order->id),
            ];
        } elseif ($paymentMode->mode === 'Faktura' && ! $request->boolean('is_pay_later')) {
            $result = $courseService->startApiCheckout($request);

            if (isset($result['error'])) {
                return $this->errorResponse('Unable to start checkout.', 'validation_error', 422, [
                    'payment' => [$result['error']],
                ]);
            }

            $order = $result['order'];
        } else {
            $order = $courseService->createOrder($request);
            $result = [
                'order' => $order,
                'redirect_url' => url('/thankyou?pl_ord='.$order->id),
            ];
        }

        if ($paymentMode->mode === 'Vipps') {
            $amount = (int) (($order->price - $order->discount) * 100);
            $orderId = $order->id.'-'.$order->user_id;
            $transactionText = $course->title;

            $vippsData = [
                'amount' => $amount,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
                'fallbackUrl' => (string) $request->input('fallbackUrl', route('api.v1.vipps.fallback', ['t' => 'sm-'.$orderId])),
                'is_ajax' => true,
                'vipps_phone_number' => optional($user->address)->vipps_phone_number,
            ];

            $redirectUrl = $this->vippsInitiatePayment($vippsData);

            if (! is_string($redirectUrl) || $redirectUrl === '') {
                return $this->errorResponse('Unable to start Vipps checkout.', 'validation_error', 422);
            }

            return response()->json([
                'redirect_url' => $redirectUrl,
                'reference' => $order->id,
            ], 201);
        }

        if ($request->boolean('is_pay_later')) {
            return response()->json([
                'redirect_url' => $result['redirect_url'] ?? url('/thankyou?pl_ord='.$order->id),
                'reference' => $order->id,
            ], 201);
        }

        $guiSnippet = $result['gui_snippet'] ?? '';

        if ($guiSnippet === '') {
            return $this->errorResponse('Unable to build checkout snippet.', 'validation_error', 422);
        }

        return response()->json([
            'gui_snippet' => $guiSnippet,
            'reference' => $order->id,
        ], 201);
    }

    public function status(Request $request, string $reference, CourseService $courseService): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if (! is_numeric($reference)) {
            return $this->errorResponse('Checkout reference not found.', 'not_found', 404);
        }

        $order = Order::with('paymentMode')->find((int) $reference);

        if (! $order || (int) $order->user_id !== (int) $user->id) {
            return $this->errorResponse('Checkout reference not found.', 'not_found', 404);
        }

        if ($order->type !== Order::COURSE_TYPE && $order->type !== Order::COURSE_UPGRADE_TYPE) {
            return $this->errorResponse('Checkout reference not found.', 'not_found', 404);
        }

        $status = 'pending';
        $paymentModeLabel = optional($order->paymentMode)->mode;

        if ((int) $order->payment_mode_id === 3 && $order->svea_order_id) {
            $paymentModeLabel = 'Svea';
        }

        $details = [
            'order_id' => $order->id,
            'payment_mode' => $paymentModeLabel,
            'is_processed' => (bool) $order->is_processed,
        ];

        if ($order->is_processed) {
            $status = 'paid';
        }

        if ($order->is_pay_later && ! $order->is_processed) {
            $status = 'pending';
        }

        if ($order->svea_order_id && ! $order->is_processed) {
            $status = $this->resolveSveaStatus($order->svea_order_id);
            $details['svea_order_id'] = $order->svea_order_id;
        }

        if ($status === 'paid' && ! $order->is_processed) {
            $courseService->addCourseToLearner($order->user_id, $order->package_id);
            $order->is_processed = 1;
            $order->save();
        }

        return response()->json([
            'status' => $status,
            'order' => $details,
        ]);
    }

    public function thankyou(Request $request, int $id, CourseService $courseService): JsonResponse
    {
        $user = $this->apiUser($request);

        if (! $user) {
            return $this->errorResponse('Missing or invalid token.', 'unauthorized', 401);
        }

        if (! $request->has('svea_ord') && ! $request->has('pl_ord')) {
            return $this->errorResponse('Missing order reference.', 'validation_error', 422);
        }

        $orderId = (int) ($request->input('svea_ord') ?? $request->input('pl_ord'));

        $order = Order::query()
            ->with('package')
            ->where('id', $orderId)
            ->where('user_id', $user->id)
            ->whereIn('type', [Order::COURSE_TYPE, Order::COURSE_UPGRADE_TYPE])
            ->first();

        if (! $order || ! $order->package || (int) $order->package->course_id !== $id) {
            return $this->errorResponse('Order not found.', 'not_found', 404);
        }

        if ($request->has('svea_ord')) {
            SveaUpdateOrderDetailsJob::dispatch($order->id)->delay(Carbon::now()->addMinute(1));
        }

        if (! $order->is_processed) {
            $courseTakenId = null;

            try {
                DB::transaction(function () use ($courseService, $order, &$courseTakenId) {
                    if ($order->type === Order::COURSE_UPGRADE_TYPE) {
                        $courseTaken = $courseService->upgradeCourseTaken($order);
                        $courseTakenId = optional($courseTaken)->id;
                        $courseService->notifyUserForUpgrade($order, $courseTaken);
                    } else {
                        $courseTaken = $courseService->addCourseToLearner($order->user_id, $order->package_id);
                        $courseTakenId = optional($courseTaken)->id;
                        $courseTaken->is_pay_later = $order->is_pay_later;
                        $courseTaken->is_active = $order->is_pay_later ? 0 : 1;
                        $courseTaken->save();

                        $courseService->notifyUser($order->user_id, $order->package_id, $courseTaken, true, true);
                    }

                    $courseService->notifyAdmin($order->user_id, $order->package_id);

                    $order->is_processed = 1;
                    $order->save();
                });
            } catch (\Throwable $exception) {
                Log::error('Failed to process API course order on thankyou endpoint.', [
                    'order_id' => $order->id,
                    'learner_id' => $order->user_id,
                    'course_taken_id' => $courseTakenId,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'exception' => $exception->getMessage(),
                ]);

                $courseTakenMessage = $courseTakenId ? 'Course taken ID: '.$courseTakenId.'<br>' : '';
                $emailData = [
                    'email_subject' => 'Error processing course order',
                    'email_message' => 'An error occurred while processing the course order.<br>'
                        .'Learner ID: '.$order->user_id.'<br>'
                        .'Order ID: '.$order->id.'<br>'
                        .$courseTakenMessage
                        .'File: '.$exception->getFile().'<br>'
                        .'Line: '.$exception->getLine().'<br>'
                        .'Error: '.$exception->getMessage(),
                    'from_name' => 'Forfatterskolen',
                    'from_email' => 'post@forfatterskolen.no',
                    'attach_file' => null,
                ];

                \Mail::to('elybutabara@gmail.com')->queue(new SubjectBodyEmail($emailData));

                return $this->errorResponse('Unable to process order.', 'server_error', 500);
            }

            CheckoutLog::updateOrCreate([
                'user_id' => $user->id,
                'parent' => 'course',
                'parent_id' => $id,
            ], [
                'is_ordered' => true,
            ]);
        }

        if ($request->has('iu')) {
            $fikenUrl = decrypt($request->get('iu'));
            $fiken = new FikenInvoice;
            $fikenInvoice = $fiken->get_invoice_data($fikenUrl);
            $fiken->send_invoice($fikenInvoice);
        }

        return response()->json([
            'status' => 'ok',
            'order_id' => $order->id,
            'is_processed' => (bool) $order->is_processed,
            'pay_later' => (bool) $request->has('pl_ord'),
        ]);
    }


    private function createFikenInvoiceForCourseOrder(Order $order, Package $package, PaymentPlan $paymentPlan, Request $request, int $planPrice): void
    {
        $dueDate = date('Y-m-d');

        if ($package->issue_date && Carbon::parse($package->issue_date)->gt(Carbon::today())) {
            $dueDate = $package->issue_date;
        }

        $dueDate = Carbon::parse($dueDate);

        $productId = match ((int) $paymentPlan->division) {
            3 => $package->months_3_product,
            6 => $package->months_6_product,
            12 => $package->months_12_product,
            default => $package->full_price_product,
        };

        $invoiceDueDate = ((int) $paymentPlan->division === 1)
            ? (clone $dueDate)->addDays(14)->format('Y-m-d')
            : (clone $dueDate)->addMonth()->format('Y-m-d');

        $paymentPlanLabel = (int) $paymentPlan->division === 1
            ? 'Faktura (14 dagers betalingsfrist)'
            : 'Rentefri delbetaling ('.$paymentPlan->division.' måneder)';

        $comment = '(Kurs: '.$package->course->title.' ['.$package->variation.'], ';
        $comment .= 'Betalingsmodus: Bankoverføring, ';
        $comment .= 'Betalingsplan: '.$paymentPlanLabel.') API order';

        $invoiceFields = [
            'user_id' => $order->user_id,
            'first_name' => (string) $request->input('first_name'),
            'last_name' => (string) $request->input('last_name'),
            'netAmount' => $planPrice * 100,
            'dueDate' => $invoiceDueDate,
            'description' => 'Kursordrefaktura',
            'productID' => $productId,
            'email' => (string) $request->input('email'),
            'telephone' => (string) $request->input('phone'),
            'address' => (string) $request->input('street'),
            'postalPlace' => (string) $request->input('city'),
            'postalCode' => (string) $request->input('zip'),
            'comment' => $comment,
            'payment_mode' => 'Faktura',
        ];

        $invoice = new FikenInvoice(false);

        if ((int) $paymentPlan->division > 1) {
            for ($index = 1; $index <= (int) $paymentPlan->division; $index++) {
                $invoiceFields['dueDate'] = (clone $dueDate)->addMonth($index)->format('Y-m-d');
                $invoiceFields['index'] = $index;
                $invoice->create_invoice($invoiceFields);
            }

            return;
        }

        $invoice->create_invoice($invoiceFields);
    }

    private function resolveSveaStatus(string $sveaOrderId): string
    {
        $response = FrontendHelpers::sveaOrderDetails($sveaOrderId);

        if ($response instanceof JsonResponse) {
            return 'pending';
        }

        $status = $response['Status'] ?? null;

        return match ($status) {
            'Final' => 'paid',
            'Cancelled', 'Invalid' => 'failed',
            default => 'pending',
        };
    }

    private function planIsEnabled(Package $package, int $division): bool
    {
        return match ($division) {
            1 => true,
            3 => (bool) $package->months_3_enable,
            6 => (bool) $package->months_6_enable,
            12 => (bool) $package->months_12_enable,
            default => false,
        };
    }
}
