<?php

namespace App\Http\Controllers\Api\V1;

use App\Course;
use App\Order;
use App\Package;
use App\PaymentMode;
use App\PaymentPlan;
use App\Http\FrontendHelpers;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends ApiController
{
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

        $validator = Validator::make($payload, [
            'email' => ['required', 'email'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'street' => ['required', 'string'],
            'zip' => ['required', 'string'],
            'city' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'variant_id' => ['nullable', 'string'],
            'package_id' => ['required_without:variant_id', 'integer', 'exists:packages,id'],
            'payment_mode_id' => ['required_without:variant_id', 'integer', 'exists:payment_modes,id'],
            'payment_plan_id' => ['required_without:variant_id', 'integer', 'exists:payment_plans,id'],
            'coupon' => ['nullable', 'string'],
            'is_pay_later' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed.', 'validation_error', 422, $validator->errors()->toArray());
        }

        $validated = $validator->validated();
        $paymentMode = null;
        $paymentPlan = null;

        if (! empty($validated['variant_id'])) {
            $resolvedVariant = $this->resolveVariant($course, $validated['variant_id']);

            if (isset($resolvedVariant['error'])) {
                return $this->errorResponse($resolvedVariant['error'], 'validation_error', 422);
            }

            $package = $resolvedVariant['package'];
            $paymentPlan = $resolvedVariant['payment_plan'];
            $paymentMode = $resolvedVariant['payment_mode'];
        } else {
            $package = Package::find($validated['package_id']);
            $paymentMode = PaymentMode::find($validated['payment_mode_id']);
            $paymentPlan = PaymentPlan::find($validated['payment_plan_id']);
        }

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

        if (! in_array($paymentMode->mode, ['Vipps', 'Paypal', 'Faktura'], true)) {
            return $this->errorResponse('Unsupported payment mode for API checkout.', 'validation_error', 422);
        }

        $payload = array_merge($payload, [
            'payment_mode_id' => $paymentMode->id,
            'payment_plan_id' => $paymentPlan->id,
            'package_id' => $package->id,
        ]);

        $request->replace($payload);

        $request->merge([
            'price' => $courseService->calculatePlanPrice($course, $package, (int) $paymentPlan->division, $request),
            'is_pay_later' => (bool) ($validated['is_pay_later'] ?? false),
        ]);

        $result = $courseService->startApiCheckout($request);

        if (isset($result['error'])) {
            return $this->errorResponse('Unable to start checkout.', 'validation_error', 422, [
                'payment' => [$result['error']],
            ]);
        }

        $order = $result['order'];

        if ($paymentMode->mode === 'Vipps') {
            $amount = (int) (($order->price - $order->discount) * 100);
            $orderId = $order->id.'-'.$order->user_id;
            $transactionText = $course->title;

            $vippsData = [
                'amount' => $amount,
                'orderId' => $orderId,
                'transactionText' => $transactionText,
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

        if ($paymentMode->mode === 'Paypal') {
            return $this->errorResponse('Paypal checkout is not supported via API.', 'validation_error', 422);
        }

        if ($request->boolean('is_pay_later')) {
            return response()->json([
                'redirect_url' => $result['redirect_url'] ?? url('/thankyou?pl_ord='.$order->id),
                'reference' => $order->id,
            ], 201);
        }

        $redirectUrl = $result['gui_page_url'] ?? $this->extractCheckoutUrl($result['gui_snippet'] ?? '');

        if (! $redirectUrl) {
            return $this->errorResponse('Unable to build checkout redirect.', 'validation_error', 422);
        }

        return response()->json([
            'redirect_url' => $redirectUrl,
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
        $details = [
            'order_id' => $order->id,
            'payment_mode' => optional($order->paymentMode)->mode,
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

    private function extractCheckoutUrl(string $guiSnippet): ?string
    {
        if ($guiSnippet === '') {
            return null;
        }

        if (preg_match('/data-sco-sveacheckout-iframesrc=[\"\\\']([^\"\\\']+)[\"\\\']/i', $guiSnippet, $matches)) {
            return $matches[1] ?? null;
        }

        if (preg_match('/data-checkout-(?:url|uri)=[\"\\\']([^\"\\\']+)[\"\\\']/', $guiSnippet, $matches)) {
            return $matches[1] ?? null;
        }

        if (preg_match('/src=[\"\\\']([^\"\\\']+)[\"\\\']/', $guiSnippet, $matches)) {
            $candidate = $matches[1] ?? null;

            if ($candidate && str_contains($candidate, 'index.js')) {
                return null;
            }

            return $candidate;
        }

        return null;
    }

    private function resolveVariant(Course $course, string $variantId): array
    {
        if (! preg_match('/^(\d+)-(\d+)$/', $variantId, $matches)) {
            return ['error' => 'Invalid variant_id format.'];
        }

        $packageId = (int) $matches[1];
        $division = (int) $matches[2];

        $package = Package::find($packageId);

        if (! $package || (int) $package->course_id !== $course->id) {
            return ['error' => 'Package not available for this course.'];
        }

        if (! $this->planIsEnabled($package, $division)) {
            return ['error' => 'Payment plan not available for this package.'];
        }

        $paymentPlan = PaymentPlan::where('division', $division)->first();

        if (! $paymentPlan) {
            return ['error' => 'Payment plan not found.'];
        }

        $paymentMode = PaymentMode::where('mode', 'Faktura')->first()
            ?? PaymentMode::where('mode', 'Vipps')->first();

        if (! $paymentMode) {
            return ['error' => 'Payment mode not found.'];
        }

        return [
            'package' => $package,
            'payment_plan' => $paymentPlan,
            'payment_mode' => $paymentMode,
        ];
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
