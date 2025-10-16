<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Backend;
use App\Http\Controllers\Editor;
use App\Http\Controllers\Editor\CoachingTimeController;
use App\Http\Controllers\Frontend;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaypalController;
use Illuminate\Support\Facades\Route;

// Domains
if (config('app.app_site') == 'no') {
    $front = 'www.forfatterskolen.no';
    $admin = 'admin.forfatterskolen.no';
    $editor = 'editor.forfatterskolen.no';
} elseif (config('app.app_site') == 'localhost') {
    $front = 'forfatterskolen.local';
    $admin = 'admin.forfatterskolen.local';
    $editor = 'editor.forfatterskolen.local';
} elseif (config('app.app_site') == 'dev.no') {
    $front = 'dev.forfatterskolen.no';
    $admin = 'admin.dev.forfatterskolen.no';
    $editor = 'editor.dev.forfatterskolen.no';
}

// get/set the locale
$locale = App::getLocale();
App::setLocale($locale);

/**
 * Front End Routes
 */
Route::view('/easywrite', 'frontend-easywrite.index');
Route::domain($front)->group(function () {

    Route::middleware('logActivity')->group(function () {
        Route::get('/', [Frontend\HomeController::class, 'index'])->name('front.home'); // Homepage
        Route::get('/sample-about', [Frontend\HomeController::class, 'sampleAbout']); // Homepage
        Route::post('/fb-leads', [Frontend\HomeController::class, 'fbLeads']); // Homepage
        Route::post('/agree-gdpr', [Frontend\HomeController::class, 'agreeGdpr'])->name('front.agree-gdpr');
        Route::get('/testemail', [Frontend\HomeController::class, 'testEmail']);
        Route::get('/bisnode', [Frontend\HomeController::class, 'bisnode']);
        Route::get('/test-fiken', [Frontend\HomeController::class, 'testFiken']);
        Route::get('/test-excel', [Frontend\HomeController::class, 'testExcel']);
        Route::post('/gotowebinar', [Frontend\HomeController::class, 'gtWebinarSendEmail']);
        Route::post('/gotowebinar/course/{id}/register', [Frontend\HomeController::class, 'gtWebinarCourseRegister']);
        Route::get('/contact-us', [Frontend\HomeController::class, 'contact_us'])->name('front.contact-us'); // Contact Us
        Route::post('/contact-us', [Frontend\HomeController::class, 'contact_us']); // Contact Us
        Route::get('/gift-cards', [Frontend\HomeController::class, 'giftCards'])->name('front.gift-cards');
        Route::post('/set-gift-card', [Frontend\HomeController::class, 'setGiftCard']);
        Route::get('/faq', [Frontend\HomeController::class, 'faq'])->name('front.faq'); // FAQ
        Route::get('/support', [Frontend\HomeController::class, 'support'])->name('front.support'); // Support
        Route::get('/support/{id}/articles', [Frontend\HomeController::class, 'supportArticles'])->name('front.support-articles'); // Support Articles
        Route::get('/support/{id}/article/{article_id}', [Frontend\HomeController::class, 'supportArticle'])->name('front.support-article'); // Support Article
        Route::get('/free-webinar/{id}/', [Frontend\HomeController::class, 'freeWebinar'])->name('front.free-webinar'); // Support Article
        Route::post('/free-webinar/{id}/', [Frontend\HomeController::class, 'freeWebinar'])->name('front.free-webinar.submit'); // Support Article
        Route::get('/free-webinar/{id}/thank-you', [Frontend\HomeController::class, 'freeWebinarThanks'])->name('front.free-webinar-thanks'); // Support Article
        Route::get('/webinartakk', [Frontend\HomeController::class, 'webinarThanks'])->name('front.webinar-thanks'); // Support Article
        Route::get('/children', [Frontend\HomeController::class, 'children'])->name('front.children');
        Route::view('/subscribe-success', 'frontend.subscribe-success')->name('front.subscribe-success'); // Homepage
        Route::get('/shop-manuscript', [Frontend\ShopManuscriptController::class, 'index'])->name('front.shop-manuscript.index'); // Shop Manuscript Listing
        Route::get('/shop-manuscript/export-single-bought', [Frontend\ShopManuscriptController::class, 'exportSingleBought']); // Shop Manuscript Listing
        Route::get('/blog', [Frontend\HomeController::class, 'blog'])->name('front.blog'); // Blog Page
        Route::get('/blog/{id}', [Frontend\HomeController::class, 'readBlog'])->name('front.read-blog'); // Blog Page
        Route::get('/publishing', [Frontend\HomeController::class, 'publishing'])->name('front.publishing'); // Forlag page
        Route::get('/konkurranse', [Frontend\HomeController::class, 'competition'])->name('front.competition'); // Forlag page
        Route::get('/coaching-timer', [Frontend\HomeController::class, 'coachingTimer'])->name('front.coaching-timer'); // Coaching Timer Page
        Route::get('/coaching-timer/checkout/{plan}', [Frontend\HomeController::class, 'coachingTimerCheckout'])->name('front.coaching-timer-checkout'); // Coaching Timer Page
        Route::post('/coaching-timer/checkout/{plan}', [Frontend\HomeController::class, 'coachingTimerCheckout'])->name('front.coaching-timer-checkout.submit'); // Coaching Timer Page
        Route::post('/coaching-timer', [Frontend\HomeController::class, 'coachingTimer'])->name('front.coaching-timer.submit'); // Coaching Timer Page
        Route::post('coaching-timer/{plan}/place-order', [Frontend\HomeController::class, 'coachingTimerPlaceOrder'])->name('front.coaching-timer-place-order'); // Coaching Timer Page
        Route::get('/coaching-timer/export-single-bought', [Frontend\HomeController::class, 'exportSingleBoughtCoaching']);
        Route::get('/course/export-pay-later-with-active', [Frontend\HomeController::class, 'exportCoursePayLaterWithActive']);
        Route::get('/vipps-order-status/{orderId}', [Frontend\HomeController::class, 'checkVippsOrderStatus']);
        Route::get('/chat', [Frontend\ChatController::class, 'index']);
        Route::post('/chat', [Frontend\ChatController::class, 'sendMessage']);
        Route::post('/ask-ai', [Frontend\ChatController::class, 'askAI']);
        Route::post('/ask-deepseek', [Frontend\ChatController::class, 'askDeepSeek']);
        Route::get('/import-webinar-registrants', [Frontend\HomeController::class, 'importWebinarRegistrants']);
        Route::post('/import-webinar-registrants', [Frontend\HomeController::class, 'processImportWebinarRegistrants'])
            ->name('process-import-webinar-registrants');
        Route::get('/soknad2024', [Frontend\HomeController::class, 'application']);
        Route::post('/soknad2024', [Frontend\HomeController::class, 'application']);
        Route::get('/export/course-taken/{year}', [Frontend\HomeController::class, 'exportCourseTakenByYear']);
        Route::get('/export/course-taken/{year}/additional-check', 
            [Frontend\HomeController::class, 'exportCourseTakenByYearWithAdditionalCheck']);
        Route::get('/export/shop-manuscripts-taken/{year}', [Frontend\HomeController::class, 'exportShopManuscriptsTakenByYear']);
        Route::get('/gpt-count-words', [Frontend\HomeController::class, 'countWordsUsingGPT']);

        Route::get('/dropbox/redirect', [Frontend\DropboxController::class, 'redirectToDropbox'])->name('dropbox.redirect');
        Route::get('/dropbox/callback', [Frontend\DropboxController::class, 'handleDropboxCallback'])->name('dropbox.callback');
        Route::post('/dropbox/refresh-token', [Frontend\DropboxController::class, 'refreshDropboxAccessToken'])->name('dropbox.refresh_token');
        Route::get('/dropbox/upload', [Frontend\DropboxController::class, 'dropboxUpload']);
        Route::post('/dropbox/upload', [Frontend\DropboxController::class, 'dropboxPostUpload'])->name('dropbox.post-upload');
        Route::get('/dropbox/shared-link/{path}', [Frontend\DropboxController::class, 'createSharedLink'])
            ->where('path', '.*')
            ->name('dropbox.shared_link');
        Route::get('/dropbox/download/{path}', [Frontend\DropboxController::class, 'downloadFile'])
            ->where('path', '.*')
            ->name('dropbox.download_file');

        Route::get('/power-office', [Frontend\HomeController::class, 'powerOffice']);

        Route::prefix('coaching-time')->group(function () {
            Route::post('/calculate', [Frontend\HomeController::class, 'coachingTimeCalculate']);
            Route::post('/validate-form', [Frontend\HomeController::class, 'coachingTimeValidate']);
        });

        Route::get('/copy-editing', [Frontend\HomeController::class, 'copyEditing'])->name('front.copy-editing'); // Copy Editing Page
        Route::post('/copy-editing', [Frontend\HomeController::class, 'copyEditing'])->name('front.copy-editing.submit'); // Copy Editing Page
        Route::get('/other-services', [Frontend\HomeController::class, 'otherServices'])->name('front.other-services-page');
        Route::get('/other-services/checkout/{plan}/{has_data}', [Frontend\HomeController::class, 'otherServiceCheckout'])->name('front.other-service-checkout');
        Route::post('/other-services/checkout/{plan}/{has_data}', [Frontend\HomeController::class, 'otherServiceCheckout'])->name('front.other-service-checkout.submit');
        Route::post('/other-services/place_order', [Frontend\HomeController::class, 'otherServiceOrder'])->name('front.other-service-place_order');
        Route::get('/thank-you', [Frontend\HomeController::class, 'thankyou'])->name('front.simple.thankyou'); // Thank You
        Route::get('/correction', [Frontend\HomeController::class, 'correction'])->name('front.correction'); // Correction Page
        Route::post('/correction', [Frontend\HomeController::class, 'correction'])->name('front.correction.submit'); // Correction Page
        Route::get('/gratis-tekstvurdering', [Frontend\ShopManuscriptController::class, 'freeManuscriptShow'])->name('front.free-manuscript.index'); // Free Manuscript
        Route::get('/gratistekstvurdering', [Frontend\ShopManuscriptController::class, 'freeManuscriptShowOther']);
        Route::get('/gratis-tekstvurdering/success', [Frontend\ShopManuscriptController::class, 'freeManuscriptShowSuccess'])->name('front.free-manuscript.success'); // Free Manuscript
        Route::post('/gratis-tekstvurdering/send', [Frontend\ShopManuscriptController::class, 'freeManuscriptSend'])->name('front.free-manuscript.send'); // Free Manuscript Send
        Route::post('/gratistekstvurdering/send', [Frontend\ShopManuscriptController::class, 'freeManuscriptSendOther'])->name('front.free-manuscript.send-other'); // Free Manuscript Send
        Route::post('/free-manuscript/set-word-count', [Frontend\ShopManuscriptController::class, 'freeManuscriptWordCount'])->name('front.free-manuscript.set-wordcount');
        Route::get('/personal-trainer/apply', [Frontend\HomeController::class, 'personalTrainer'])->name('front.personal-trainer.apply');
        Route::post('/personal-trainer/send', [Frontend\HomeController::class, 'personalTrainerSend'])->name('front.personal-trainer.send');
        Route::get('/personal-trainer/thank-you', [Frontend\HomeController::class, 'personalTrainerThanks'])->name('front.personal-trainer.thank-you');

        Route::get('/innlevering', [Frontend\HomeController::class, 'skrive2020'])->name('front.skrive2020');
        Route::post('/innlevering/send', [Frontend\HomeController::class, 'innleveringCompetitionSend'])->name('front.innlevering.send');
        Route::get('/takk', [Frontend\HomeController::class, 'innleveringCompetitionThanks'])->name('front.innlevering.thank-you');

        Route::post('/', [Frontend\HomeController::class, 'homeOptIn'])->name('front.home.submit'); // Homepage

        Route::get('/opt-in/{slug?}', [Frontend\HomeController::class, 'optIn'])->name('front.opt-in'); // Opt-in page
        Route::post('/opt-in/{slug?}', [Frontend\HomeController::class, 'optIn'])->name('front.opt-in.submit'); // Opt-in page
        Route::get('/opt-in/{slug?}/download', [Frontend\HomeController::class, 'downloadOptIn'])->name('front.opt-in.download'); // Download Opt-in file
        Route::view('manual-invoice', 'frontend.manual-invoice'); // Download Opt-in file
        Route::post('manual-invoice', [Frontend\HomeController::class, 'saveManualInvoice']); // Download Opt-in file

        // opt in thank you pages
        Route::get('/opt-in/thanks/{slug?}', [Frontend\HomeController::class, 'optInThanks'])->name('front.opt-in.thanks');
        Route::get('/opt-in/ref/{slug?}', [Frontend\HomeController::class, 'optInReferral'])->name('front.opt-in.referral');

        /*Route::get('/opt-in/rektor-tips', 'HomeController@optInRektor')->name('front.opt-in.rektor-tips'); // Opt-in page
        Route::post('/opt-in/rektor-tips', 'HomeController@optInRektor')->name('front.opt-in.rektor-tips'); // Opt-in page*/

        Route::get('/opt-in-terms', [Frontend\HomeController::class, 'optInTerms'])->name('front.opt-in-terms'); // Opt-in page

        Route::get('/terms/{slug?}', [Frontend\HomeController::class, 'terms'])->name('front.terms'); // Terms page

        Route::get('/upgrade-manuscript/{id}/checkout', [Frontend\ShopManuscriptController::class, 'checkoutUpgradeManuscript'])->name('front.shop-manuscript.upgrade-manuscript-checkout'); // Checkout Shop Manuscript
        Route::get('/contract/{code}', [Frontend\HomeController::class, 'contract'])->name('front.contract-view');
        Route::get('/contract/{code}/download', [Frontend\HomeController::class, 'contractDownload'])->name('front.contract.download');
        Route::post('/contract/{code}/sign', [Frontend\HomeController::class, 'contractSign'])->name('front.contract.sign');
        Route::get('/generate-image', [Frontend\HomeController::class, 'generateImage']);

        Route::get('/forget-session-key/{key}', [Frontend\HomeController::class, 'forgetSessionKey']);

        Route::prefix('shop-manuscript')->group(function () {
            Route::get('/{id}/checkout', [Frontend\ShopManuscriptController::class, 'checkout'])->name('front.shop-manuscript.checkout'); // Checkout Shop Manuscript
            Route::post('/{id}/place_order', [Frontend\ShopManuscriptController::class, 'place_order'])->name('front.shop-manuscript.place_order'); // Checkout Shop Manuscript
            Route::get('/{id}/cancelled-order', [Frontend\ShopManuscriptController::class, 'orderCancelled'])->name('front.shop-manuscript.cancelled-order'); // Checkout
            Route::post('/{id}/checkout/validate-order', [Frontend\ShopManuscriptController::class, 'validateOrder'])->name('front.shop-manuscript.validate-order');
            Route::post('/{id}/checkout/validate-form', [Frontend\ShopManuscriptController::class, 'validateForm'])->name('front.shop-manuscript.validate-form');
            Route::post('/{id}/checkout/vipps', [Frontend\ShopManuscriptController::class, 'vippsCheckout'])->name('front.shop-manuscript.vipps');
            Route::get('/{id}/checkout/process-vipps', [Frontend\ShopManuscriptController::class, 'processVipps'])->name('front.shop-manuscript.checkout.process-vipps');
            Route::get('/{id}/thankyou', [Frontend\ShopManuscriptController::class, 'thankyou'])->name('front.shop-manuscript.thankyou');
        });

        Route::get('/shop-manuscript/payment/paypal/{invoice_id}', [Frontend\ShopManuscriptController::class, 'paypalPayment'])->name('front.shop-manuscript.paypal-payment'); // Paypal Payment
        Route::post('/upgrade-manuscript/{id}/place_upgrade', [Frontend\ShopManuscriptController::class, 'upgradeManuscript'])->name('front.shop-manuscript.upgrade-manuscript'); // Checkout Shop Manuscript

        Route::get('/email/confirmation/{token}', [Frontend\HomeController::class, 'emailConfirmation'])->name('front.email-confirmation');
        Route::get('/email/attachment/{token}', [Frontend\HomeController::class, 'emailAttachment'])->name('front.email-attachment');
        Route::get('/email/check-open/{token}', [Frontend\HomeController::class, 'emailTracking'])->name('front.email-track');

        Route::get('/henrik-langeland', [Frontend\HomeController::class, 'henrikPage'])->name('front.henrik'); // Upviral ref page
        Route::get('/skrive2020', [Frontend\HomeController::class, 'innleveringCompetition'])->name('front.innlevering.join');
        Route::get('/poems', [Frontend\HomeController::class, 'poems'])->name('front.poems'); // Poems page
        Route::get('/gro-dahle', [Frontend\HomeController::class, 'grodahlePage'])->name('front.gro-dahle');

        Route::get('/reprise', [Frontend\HomeController::class, 'replay'])
            ->name('front.reprise'); // Replay Page
        Route::get('/barn', [Frontend\HomeController::class, 'barn'])
            ->name('front.barn'); // Replay Page
        Route::get('/skrivdittliv', [Frontend\HomeController::class, 'skrivdittliv'])
            ->name('front.skrivdittliv');
        Route::get('/hererjeg', [Frontend\HomeController::class, 'hereIam'])
            ->name('front.here-i-am'); // Replay Page

        // Test Manuscript (Shop Manuscript)
        Route::post('/test_manuscript', [Frontend\ShopManuscriptController::class, 'test_manuscript'])->name('front.shop-manuscript.test_manuscript'); // Test count shop manuscript
        Route::post('/shop-manuscript/store-temp-upload', [Frontend\ShopManuscriptController::class, 'storeTempUploadedFile'])->name('front.shop-manuscript.store-temp-upload');
        Route::post('/documents/convert-to-docx', [Frontend\DocumentConversionController::class, 'convertToDocx'])->name('front.documents.convert-to-docx');

        // Pay IPN
        Route::post('/paypalipn', [Frontend\ShopController::class, 'paypalIPN'])->name('front.shop.paypalipn'); // Paypal IPN

        // book invitation
        Route::get('/book/invitation/{link_token}', [Frontend\PilotReaderBookSettingsController::class, 'openInvitationLink']);
        Route::post('/book/invite/send', [Frontend\PilotReaderBookSettingsController::class, 'unauthenticatedSendInvitation'])->name('book.invite.send');
        Route::post('/email/validate', [Frontend\PilotReaderBookSettingsController::class, 'unauthenticatedEmailValidation']);

        // private groups
        Route::get('/invitation/group/accept/{link_token}', [Frontend\PrivateGroupMembersController::class, 'openInvitationLink']);
        Route::post('/private-group/email/validate', [Frontend\PrivateGroupMembersController::class, 'unauthenticatedEmailValidation']);
        Route::post('/private-group/invite/send', [Frontend\PrivateGroupMembersController::class, 'unauthenticatedSendInvitation']);

        Route::get('/webinar-pakke-campaign', [Frontend\HomeController::class, 'webinarPakkeRef']); // Webinar-pakke campaign page
        Route::get('/test-campaign', [Frontend\HomeController::class, 'testCampaign']); // Upviral ref page

        Route::get('/goto-webinar/register/{webinar_key}/{email}', [Frontend\HomeController::class, 'gotoWebinarEmailRegistration'])
            ->name('front.goto-webinar.registration.email'); // GotoWebinar Registration through email

        Route::get('/vipps', [Frontend\VippsController::class, 'index']);
        Route::post('/vipps/payment', [Frontend\HomeController::class, 'paymentCallback'])->name('vipps.payment');
        Route::post('/vipps/payment/v2/payments/{orderId}', [Frontend\HomeController::class, 'paymentCallback']);
        Route::get('/vipps/fallback', [Frontend\HomeController::class, 'vippsFallback'])->name('vipps.fallback');
        Route::get('/vipps/payment/{orderId}/details', [Frontend\VippsController::class, 'getPaymentDetails']);

        Route::get('/file/{hash}', [Frontend\HomeController::class, 'checkFileFromDB']);

        Route::get('/bambora/accept', [Frontend\HomeController::class, 'bamboraAccept']);
        Route::get('/bambora/paymentComplete', [Frontend\HomeController::class, 'bamboraPaymentComplete']);
        Route::get('/has-paid-course', [Frontend\ShopController::class, 'hasPaidCourse']);
        Route::get('/current-user', [Frontend\LearnerController::class, 'currentUser']);
        Route::post('/file/count-characters', [Frontend\LearnerController::class, 'countFileCharacters']);
        // Course
        Route::prefix('course')->group(function () {
            Route::get('/', [Frontend\CourseController::class, 'index'])->name('front.course.index'); // Course Listing
            Route::get('/{id}', [Frontend\CourseController::class, 'show'])->name('front.course.show'); // Course Details
            Route::get('/{id}/checkout', [Frontend\ShopController::class, 'sveaCheckout'])->name('front.course.checkout'); // Checkout
            Route::get('/{id}/application', [Frontend\CourseController::class, 'application'])->name('front.course.application'); // Checkout
            Route::post('/{id}/application/process', [Frontend\CourseController::class, 'processApplication'])->name('front.course.process-application'); // Checkout
            Route::get('/{id}/application/thank-you', [Frontend\CourseController::class, 'applicationThankyou'])->name('front.course.application.thank-you'); // Checkout
            Route::get('/{id}/fs_checkout', [Frontend\ShopController::class, 'checkout'])->name('front.course.fs-checkout'); // Checkout
            Route::get('/{id}/cancelled-order', [Frontend\ShopController::class, 'orderCancelled'])->name('front.course.cancelled-order');
            Route::get('/{id}/checkout-svea', [Frontend\ShopController::class, 'sveaCheckout'])->name('front.course.svea-checkout'); // Checkout
            Route::post('/{id}/checkout/process-order', [Frontend\ShopController::class, 'processOrder'])->name('front.course.process_order'); // Place Order
            Route::get('/{id}/thank-you', [Frontend\CourseController::class, 'thankyou'])->name('front.course.thank-you'); // Checkout
            Route::post('/{id}/checkout/validate-form', [Frontend\ShopController::class, 'validateCheckoutForm'])->name('front.course.checkout.validate-form');
            Route::post('/{id}/checkout/vipps', [Frontend\ShopController::class, 'vippsCheckout'])->name('front.course.checkout.vipps');
            Route::get('/{id}/checkout/process-vipps', [Frontend\ShopController::class, 'processVipps'])->name('front.course.checkout.process-vipps');
            Route::get('/{id}/checkout-test', [Frontend\ShopController::class, 'checkoutTest'])->name('front.course.checkout-test'); // Checkout
            Route::post('/{id}/proceed-checkout', [Frontend\ShopController::class, 'proceedCheckout'])->name('front.course.proceed-checkout'); // Checkout
            Route::get('/{id}/discount/{coupon}', [Frontend\ShopController::class, 'applyDiscount'])->name('front.course.apply-discount'); // Checkout
            Route::post('/{id}/checkout/place_order', [Frontend\ShopController::class, 'place_order'])->name('front.course.place_order'); // Place Order
            Route::post('/{id}/checkout/place_order_test', [Frontend\ShopController::class, 'place_order_test'])->name('front.course.place_order_test'); // Place Order
            Route::get('/{id}/check_discount/', [Frontend\ShopController::class, 'checkDiscount'])->name('front.course.checkDiscount'); // Check Discount
            Route::get('/{id}/check_coupon_discount/{coupon}', [Frontend\ShopController::class, 'checkCouponDiscount'])->name('front.course.checkCouponDiscount'); // Check Coupon Discount
            Route::post('/{id}/get-free/', [Frontend\CourseController::class, 'getFreeCourse'])->name('front.course.getFreeCourse'); // Check Discount
            Route::get('/{id}/claim-reward', [Frontend\ShopController::class, 'claimReward'])->name('front.course.claim-reward'); // Claim Reward
            Route::post('/{id}/claim-reward', [Frontend\ShopController::class, 'claimReward'])->name('front.course.claim-reward.submit'); // Claim Reward
            Route::get('/share/{share_hash}/checkout', [Frontend\ShopController::class, 'shareCourseCheckout'])->name('front.course.share.checkout');
            Route::post('/share/{share_hash}/checkout', [Frontend\ShopController::class, 'shareCourseCheckout'])->name('front.course.share.checkout.submit');
        });

        Route::prefix('publishing-service')->group(function () {
            Route::get('/calculator', [Frontend\PublishingServiceController::class, 'serviceCalculator'])->name('front.service-calculator');
            Route::get('/thank-you', [Frontend\PublishingServiceController::class, 'thankyou'])->name('publishing-service.thank-you');
            Route::get('/{id}', [Frontend\PublishingServiceController::class, 'show']);
            Route::post('/checkout/validate-form', [Frontend\PublishingServiceController::class, 'validateForm']);
        });

        // Workshop
        Route::prefix('workshop')->group(function () {
            Route::get('/', [Frontend\WorkshopController::class, 'index'])->name('front.workshop.index'); // workshop Listing
            Route::get('/{id}', [Frontend\WorkshopController::class, 'show'])->name('front.workshop.show'); // workshop Details
            Route::get('/{id}/checkout', [Frontend\WorkshopController::class, 'checkout'])->name('front.workshop.checkout'); // Checkout
            Route::post('/{id}/checkout/place_order', [Frontend\WorkshopController::class, 'place_order'])->name('front.workshop.place_order'); // Place Order
        });

        Route::prefix('gift')->group(function () {
            Route::prefix('course')->group(function () {
                Route::get('/', [Frontend\GiftController::class, 'course'])->name('front.gift.course');
                Route::get('/{id}', [Frontend\GiftController::class, 'courseShow'])->name('front.gift.course.show');
                Route::get('/{id}/checkout', [Frontend\GiftController::class, 'courseCheckout'])->name('front.gift.course.checkout');
                Route::post('/{id}/checkout/validate-form', [Frontend\GiftController::class, 'validateCheckoutForm'])->name('front.gift.course.checkout.validate-form');
                Route::post('/{id}/checkout/process-order', [Frontend\GiftController::class, 'processCourseOrder'])->name('front.gift.course.checkout.process-order');
                Route::get('/{id}/thankyou', [Frontend\GiftController::class, 'thankyou'])->name('front.gift.course.thankyou');
            });

            Route::prefix('shop-manuscript')->group(function () {
                Route::get('/', [Frontend\GiftController::class, 'shopManuscript'])->name('front.gift.shop-manuscript');
                Route::get('/{id}/checkout', [Frontend\GiftController::class, 'shopManuscriptCheckout'])->name('front.gift.shop-manuscript.checkout'); // Checkout Shop Manuscript
                Route::post('/{id}/checkout/validate-form', [Frontend\GiftController::class, 'validateCheckoutForm']);
                Route::get('/{id}/thankyou', [Frontend\GiftController::class, 'thankyou']);
            });

            Route::get('/redeem', [Frontend\GiftController::class, 'showRedeem'])->name('front.gift.show-redeem');
            Route::post('/redeem', [Frontend\GiftController::class, 'redeemGift']);
        });

        Route::get('/thankyou', [Frontend\ShopController::class, 'thankyou'])->name('front.shop.thankyou'); // Thank You
        Route::get('/assignment/thankyou', [Frontend\HomeController::class, 'assignmentThankyou'])->name('front.assignment.thankyou'); // Thank You
        Route::get('/thank-you', [Frontend\HomeController::class, 'thankyou'])->name('front.thank-you'); // Thank You

        /*Route::post('/cart/add', 'ShopController@add_to_cart')->name('front.shop.add_to_cart'); // Add To Cart
        Route::post('/cart/remove', 'ShopController@remove_from_cart')->name('front.shop.remove_from_cart'); // Remove From Cart*/

        Route::get('/format_money/{numeric}', [Frontend\HomeController::class, 'formatMoney']);

        Route::get('/payment-plan-options/{id}', [Frontend\ShopController::class, 'getPaymentPlanOptions']);
        Route::get('/payment-modes', [Frontend\ShopController::class, 'getPaymentModeOptions']);

    });

    // Learner Dashboard
    Route::middleware('learner', 'logActivity')->prefix('account')->group(function () {
        Route::get('/dashboard', [Frontend\LearnerController::class, 'dashboard'])->name('learner.dashboard'); // Dashboard Page
        Route::get('/course', [Frontend\LearnerController::class, 'course'])->name('learner.course')->middleware('checkAutoRenewCourses'); // Courses Page
        Route::get('/course/{id}', [Frontend\LearnerController::class, 'courseShow'])->name('learner.course.show'); // Single Course Page
        Route::post('/course/{id}/renew-all', [Frontend\LearnerController::class, 'courseRenewAll'])->name('learner.course.renew-all'); // Single Course Page
        Route::post('/renew-learner-courses', [Frontend\LearnerController::class, 'renewLearnerCourses'])->name('learner.renew-all-courses'); // Renew all the course of the learner in upgrade page
        Route::post('/course-renew/', [Frontend\LearnerController::class, 'courseRenew'])->name('learner.course.renew'); // Single Course Page
        Route::get('/calendar', [Frontend\LearnerController::class, 'calendar'])->name('learner.calendar'); // Calendar Page
        Route::get('/document-converter', [Frontend\LearnerController::class, 'documentConverter'])->name('learner.document-converter');
        Route::post('/document-converter', [Frontend\LearnerController::class, 'convertDocument'])->name('learner.document-converter.convert');
        Route::get('/invoice', [Frontend\LearnerController::class, 'invoice'])->name('learner.invoice'); // Invoice Listing Page
        Route::get('/invoice/{id}', [Frontend\LearnerController::class, 'invoiceShow'])->name('learner.invoice.show'); // Invoice Single Page
        Route::post('/invoice/pay-later/{id}/generate', [Frontend\LearnerController::class, 'generatePayLaterInvoice'])->name('learner.invoice.pay-later.generate');
        Route::get('/change-portal/{portal}', [Frontend\LearnerController::class, 'changePortal'])->name('learner.change-portal'); // Invoice Single Page
        Route::get('/invoice/{fiken_invoice_id}/vipps-payment', [Frontend\LearnerController::class, 'invoiceVippsPayment'])->name('learner.invoice.vipps-payment'); // Invoice Single Page
        Route::get('/order/{id}/download-credited', [Frontend\LearnerController::class, 'downloadCreditedOrder'])->name('learner.order.download-credited');
        Route::get('/order/{id}/download', [Frontend\LearnerController::class, 'downloadOrder']);
        Route::post('/order/{id}/save-company', [Frontend\LearnerController::class, 'saveCompany']);
        Route::post('/redeem-gift', [Frontend\LearnerController::class, 'redeemGift'])->name('learner.redeem-gift');
        Route::post('learner/invoice/{id}/e-faktura', [Frontend\LearnerController::class, 'vippsEFaktura'])->name('learner.invoice.vipps-e-faktura');
        Route::post('learner/set-vipss-efaktura', [Frontend\LearnerController::class, 'setVippsEFaktura'])->name('learner.set-vipps-e-faktura');
        Route::get('/invoice/{id}/download/{type}', [Frontend\LearnerController::class, 'downloadInvoiceByType'])->name('learner.invoice.download-by-type');
        Route::get('/publishing', [Frontend\LearnerController::class, 'publishing'])->name('learner.publishing'); // Publishers House Page
        Route::get('/writing-groups', [Frontend\LearnerController::class, 'writingGroups'])->name('learner.writing-groups'); // Writing Groups Page
        Route::get('/writing-group/{id}', [Frontend\LearnerController::class, 'writingGroup'])->name('learner.writing-group'); // Writing Group Page
        Route::put('/writing-group/{id}', [Frontend\LearnerController::class, 'writingGroup'])->name('learner.update.writing-group'); // Writing Group Page
        Route::get('/competition', [Frontend\LearnerController::class, 'competition'])->name('learner.competition'); // Competitions Page
        Route::get('/private-message', [Frontend\LearnerController::class, 'privateMessage'])->name('learner.private-message'); // Private Message Page
        Route::get('/coaching-time', [Frontend\LearnerController::class, 'coachingTime'])->name('learner.coaching-time');
        Route::get('/coaching-time/available', [Frontend\LearnerController::class, 'availableCoachingTime'])->name('learner.coaching-time.available');
        Route::post('/coaching-time/request', [Frontend\LearnerController::class, 'requestCoachingTime'])->name('learner.coaching-time.request');
        Route::get('/time-register', [Frontend\LearnerController::class, 'timeRegister'])->name('learner.time-register');
        Route::get('/book-sale', [Frontend\LearnerController::class, 'bookSale'])->name('learner.book-sale');
        Route::get('/book-for-sale/{id}', [Frontend\LearnerController::class, 'bookForSale'])->name('learner.book-for-sale');
        Route::get('/book-sale/list-by-month/{year}', [Frontend\LearnerController::class, 'bookSaleByMonth']);
        Route::get('/book-sale/monthly-details/{year}/{month}',
            [Frontend\LearnerController::class, 'bookSaleMonthlyDetails']);
        Route::get('project/{project_id}/registration/{registration_id}/storage-cost/{year}/export', [Frontend\LearnerController::class, 'exportStorageCost'])
            ->name('learner.project.storage-cost.export');
        Route::post('/for-sale-books/save', [Frontend\LearnerController::class, 'saveForSaleBooks'])->name('learner.save-for-sale-books');
        Route::delete('/for-sale-books/{id}/delete', [Frontend\LearnerController::class, 'deleteForSaleBooks'])->name('learner.delete-for-sale-books');

        Route::post('/self-publishing-portal/request', [Frontend\LearnerController::class, 'requestSelfPublishingPortal'])
            ->name('learner.request-self-publishing-portal');
        Route::get('/project', [Frontend\LearnerController::class, 'project'])->name('learner.project');
        Route::post('/project', [Frontend\LearnerController::class, 'saveProject'])->name('learner.save-project');
        Route::post('/project/{id}/set-standard', [Frontend\LearnerController::class, 'setStandardProject'])->name('learner.project.set-standard');
        Route::post('/project/self-publishing/{id}/upload-manuscript', [Frontend\LearnerController::class, 'uploadSelfPublishingManuscript'])
            ->name('learner.project.self-publishing.upload-manuscript');
        Route::post('/project/other-service/{id}/upload-manuscript/{type}', [Frontend\LearnerController::class, 'uploadOtherServiceManuscript'])
            ->name('learner.project.other-service.upload-manuscript');
        Route::get('/self-publishing/feedback/{id}/download', [Frontend\SelfPublishingController::class, 'download'])
            ->name('learner.self-publishing.feedback.download');
        Route::get('/marketing', [Frontend\LearnerController::class, 'marketing'])->name('learner.marketing');
        Route::get('/marketing/download', [Frontend\LearnerController::class, 'marketingDownload'])->name('learner.marketing-download');
        Route::get('/progress-plan', [Frontend\ProgressPlanController::class, 'index'])->name('learner.progress-plan');
        Route::get('/progress-plan/{step}', [Frontend\ProgressPlanController::class, 'planStep'])->name('learner.progress-plan.step');
        Route::post('/progress-plan/manuscripts/upload', [Frontend\ProgressPlanController::class, 'uploadManuscript'])
            ->name('learner.progress-plan.manuscript.upload');
        Route::post('/progress-plan/other-service/{type}/upload', [Frontend\ProgressPlanController::class, 'uploadOtherServiceManuscript'])
            ->name('learner.project.progress-plan.other-service.upload-manuscript');
        Route::post('/progress-plan/{project_id}/e-book/save', [Frontend\ProgressPlanController::class, 'saveEbook'])
            ->name('learner.progress-plan.save-ebook');
        Route::post('/progress-plan/{id}/audio/save', [Frontend\ProgressPlanController::class, 'saveAudio'])->name('learner.progress-plan.save-audio');
        Route::post('/progress-plan/{id}/print/save', [Frontend\ProgressPlanController::class, 'savePrint'])->name('learner.progress-plan.save-print');
        Route::post('/progress-plan/type-setting/upload', [Frontend\ProgressPlanController::class, 'uploadTypeSetting'])
            ->name('learner.progress-plan.type-setting.upload');
        Route::get('/self-publishing/order', [Frontend\SelfPublishingController::class, 'selfPublishingOrder'])->name('learner.self-publishing.order');
        Route::post('/self-publishing/add-to-cart', [Frontend\SelfPublishingController::class, 'addToCart'])->name('learner.self-publishing.add-to-cart');
        Route::get('/self-publishing/order/checkout', [Frontend\SelfPublishingController::class, 'checkoutOrder'])->name('learner.self-publishing.checkout');
        Route::get('/self-publishing/order/process-checkout', [Frontend\SelfPublishingController::class, 'processCheckoutOrder'])
            ->name('learner.self-publishing.process-checkout');
        Route::post('/self-publishing/order/{id}/save-quote', [Frontend\SelfPublishingController::class, 'saveQuote'])->name('learner.self-publishing.save-quote');
        Route::post('/self-publishing/order/{id}/move-to-order', [Frontend\SelfPublishingController::class, 'moveToOrder'])->name('learner.self-publishing.move-to-order');
        Route::delete('/self-publishing/order/{id}/delete', [Frontend\SelfPublishingController::class, 'deleteOrder'])->name('learner.self-publishing.delete-order');

        // self publishing records not connected to project
        Route::get('/self-publishing/list', [Frontend\SelfPublishingController::class, 'listSelfPublishing'])->name('learner.self-publishing.list');
        Route::get('/self-publishing/copy-editing', [Frontend\SelfPublishingController::class, 'copyEditing'])->name('learner.self-publishing.copy-editing');
        Route::get('/self-publishing/correction', [Frontend\SelfPublishingController::class, 'correction'])->name('learner.self-publishing.correction');
        Route::get('/self-publishing/cover', [Frontend\SelfPublishingController::class, 'cover'])->name('learner.self-publishing.cover');
        Route::get('/self-publishing/cover/{id}', [Frontend\SelfPublishingController::class, 'coverDetails'])->name('learner.self-publishing.cover-show');
        Route::post('/self-publishing/cover/{project_id}/save', [Frontend\SelfPublishingController::class, 'saveCover'])
            ->name('learner.self-publishing.save-cover');
        Route::get('/self-publishing/page-format', [Frontend\SelfPublishingController::class, 'pageFormat'])->name('learner.self-publishing.page-format');
        Route::get('/self-publishing/page-format/{id}', [Frontend\SelfPublishingController::class, 'pageFormatDetails'])
            ->name('learner.self-publishing.page-format-show');
        Route::post('/self-publishing/page-format/{project_id}/save', [Frontend\SelfPublishingController::class, 'savePageFormat'])
            ->name('learner.self-publishing.save-page-format');
        Route::get('/self-publishing/publishing/order', [Frontend\SelfPublishingController::class, 'publishingOrder'])
            ->name('learner.self-publishing.publishing.order');
        Route::post('/self-publishing/publishing/order/validate', [Frontend\SelfPublishingController::class, 'validatePublishingOrder'])
            ->name('learner.self-publishing.publishing.order.validate');
        Route::post('/self-publishing/publishing/order/process', [Frontend\SelfPublishingController::class, 'processPublishingOrder'])
            ->name('learner.self-publishing.publishing.order.process');

        Route::prefix('project/{id}')->group(function () {
            Route::get('/', [Frontend\LearnerController::class, 'showProject'])->name('learner.project.show');
            Route::get('/service/{service_id}/order', [Frontend\LearnerController::class, 'orderService'])->name('learner.service.order');
            Route::get('/graphic-work', [Frontend\LearnerController::class, 'projectGraphicWork'])->name('learner.project.graphic-work');
            Route::get('/registration', [Frontend\LearnerController::class, 'projectRegistration'])->name('learner.project.registration');
            Route::get('/marketing', [Frontend\LearnerController::class, 'projectMarketing'])->name('learner.project.marketing');
            Route::get('/marketing-plan', [Frontend\LearnerController::class, 'projectMarketingPlan'])->name('learner.project.marketing-plan');
            Route::post('/save-answer', [Frontend\LearnerController::class, 'saveMarketingPlanQA'])->name('learner.project.save-marketing-qa');
            Route::get('/contract', [Frontend\LearnerController::class, 'projectContract'])->name('learner.project.contract');
            Route::get('/invoice', [Frontend\LearnerController::class, 'projectInvoice'])->name('learner.project.invoice');
            Route::get('/storage', [Frontend\LearnerController::class, 'projectStorage'])->name('learner.project.storage');
            Route::get('/storage/{registration_id}/details', [Frontend\LearnerController::class, 'projectStorageDetails'])
                ->name('learner.project.storage-details');
        });

        Route::get('/profile', [Frontend\LearnerController::class, 'profile'])->name('learner.profile'); // Profile Page
        Route::get('/terms', [Frontend\LearnerController::class, 'terms'])->name('learner.terms'); // Terms Page
        Route::get('/course/{course_id}/lesson/{id}', [Frontend\LearnerController::class, 'lesson'])->name('learner.course.lesson'); // Lesson Page
        Route::get('/course/{course_id}/lesson/{id}/download', [Frontend\LearnerController::class, 'downloadLesson'])->name('learner.course.download-lesson'); // Download Lesson Page
        Route::get('/manuscript/{id}', [Frontend\LearnerController::class, 'manuscriptShow'])->name('learner.manuscript.show'); // Manuscript Single Page
        Route::get('/shop-manuscript', [Frontend\LearnerController::class, 'shopManuscript'])->name('learner.shop-manuscript'); // Shop Manuscripts Page
        Route::get('/shop-manuscript/{id}', [Frontend\LearnerController::class, 'shopManuscriptShow'])->name('learner.shop-manuscript.show'); // Shop Manuscript Show Page
        Route::get('shop-manuscript/{id}/download-script/{type}', [Frontend\LearnerController::class, 'downloadManuscript'])->name('learner.shop-manuscript.download');
        Route::get('/shop-manuscript/{id}/feedback/{feedback_id}', [Frontend\LearnerController::class, 'downloadManuscriptFeedback'])->name('learner.shop-manuscript.download-feedback'); // Shop Manuscript Show Page
        Route::get('/workshop', [Frontend\LearnerController::class, 'workshop'])->name('learner.workshop'); // Workshops Page
        Route::post('/coaching-timer/{id}/approve_date', [Frontend\LearnerController::class, 'approveCoachingDate'])->name('learner.coaching-timer.approve_date');
        Route::post('/coaching-timer/{id}/suggest_date', [Frontend\LearnerController::class, 'suggestCoachingDate'])->name('learner.coaching-timer.suggest_date');
        Route::post('/coaching-timer/{id}/help_with', [Frontend\LearnerController::class, 'updateHelpWith'])->name('learner.coaching-timer.help_with');
        Route::post('/coaching-timer/{id}/set-status', [Frontend\LearnerController::class, 'setCoachingStatus'])->name('learner.coaching-timer.set-status');
        Route::post('/course-taken/coaching-timer/add', [Frontend\LearnerController::class, 'addCoachingSession'])->name('learner.course-taken.coaching-timer.add');
        Route::get('/webinar', [Frontend\LearnerController::class, 'webinar'])->name('learner.webinar'); // Webinars Page
        Route::post('/webinar', [Frontend\LearnerController::class, 'webinar'])->name('learner.webinar.submit'); // Webinars Page
        Route::get('/webinar/register/{webinar_key}/{webinar_id}', [Frontend\LearnerController::class, 'webinarRegister'])->name('learner.webinar.register'); // Webinars Page
        Route::get('/course-webinar', [Frontend\LearnerController::class, 'courseWebinar'])->name('learner.course-webinar'); // Course Webinars Page
        Route::post('/course-webinar', [Frontend\LearnerController::class, 'courseWebinar'])->name('learner.course-webinar.submit'); // Course Webinars Page
        Route::get('/assignment', [Frontend\LearnerController::class, 'assignment'])->name('learner.assignment'); // Assignments Page
        Route::post('assignment/{id}/replace_manuscript', [Frontend\LearnerController::class, 'replaceAssignmentManuscript'])->name('learner.assignment.replace_manuscript');
        Route::post('assignment/{id}/delete_manuscript', [Frontend\LearnerController::class, 'deleteAssignmentManuscript'])->name('learner.assignment.delete_manuscript');
        Route::post('assignment/{id}/replace_letter', [Frontend\LearnerController::class, 'replaceAssignmentLetter'])->name('learner.assignment.replace_letter');
        Route::get('/assignment/group/{id}', [Frontend\LearnerController::class, 'group_show'])->name('learner.assignment.group.show'); // Assignment show Page
        Route::get('/assignment/group/{id}/learner-details', [Frontend\LearnerController::class, 'groupLearnerDetails']);
        Route::get('/assignment/group/{id}/show-details', [Frontend\LearnerController::class, 'groupShowDetails']);
        Route::get('/assignment/manuscript/{id}', [Frontend\LearnerController::class, 'downloadAssignmentGroupManuscript'])->name('learner.assignment.manuscript.download'); // Assignment show Page
        Route::get('/assignment/feedback/{id}/download', [Frontend\LearnerController::class, 'downloadAssignmentGroupFeedback'])->name('learner.assignment.feedback.download'); // Download assignment feedback
        Route::get('/assignment/feedback-no-group/{id}/download', [Frontend\LearnerController::class, 'downloadAssignmentNoGroupFeedback'])->name('learner.assignment.no-group-feedback.download'); // Download assignment feedback
        Route::get('/assignment/group/{id}/download-all-feedback', [Frontend\LearnerController::class, 'downloadAssignmentGroupAllFeedback'])->name('learner.assignment.group.feedback.download-all'); // Download all assignment group feedback
        Route::get('/word-written', [Frontend\LearnerController::class, 'wordWritten'])->name('learner.word-written'); // Word Written Page
        Route::post('/word-written', [Frontend\LearnerController::class, 'wordWritten'])->name('learner.word-written.submit'); // Word Written Page
        Route::get('/word-written-goals', [Frontend\LearnerController::class, 'wordWrittenGoals'])->name('learner.word-written-goals'); // Word Written Goals Page
        Route::post('/word-written-goals', [Frontend\LearnerController::class, 'wordWrittenGoals'])->name('learner.word-written-goals.submit'); // Word Written Goals Page
        Route::put('/word-written-goals/{id}/update', [Frontend\LearnerController::class, 'wordWrittenGoalsUpdate'])->name('learner.word-written-goals-update'); // Word Written Goals Page
        Route::delete('/word-written-goals/{id}/delete', [Frontend\LearnerController::class, 'wordWrittenGoalsDelete'])->name('learner.word-written-goals-delete'); // Word Written Goals Page
        Route::get('/word-written-goal/{id}/statistic', [Frontend\LearnerController::class, 'goalStatistic'])->name('learner.goal-statistic');
        Route::get('/search', [Frontend\LearnerController::class, 'search'])->name('learner.account.search'); // Assignment show Page
        Route::get('/lesson/download-document/{id}', [Frontend\LearnerController::class, 'downloadLessonDocument'])->name('learner.lesson.download-lesson-document');
        Route::get('/upgrade', [Frontend\LearnerController::class, 'upgrade'])->name('learner.upgrade');
        Route::get('/upgrade/get-course/{course_taken_id}/package/{package_id}', [Frontend\LearnerController::class, 'getUpgradeCourse'])->name('learner.get-upgrade-course');
        Route::post('/upgrade/course/{id}', [Frontend\LearnerController::class, 'upgradeCourse'])->name('learner.upgrade-course');
        Route::post('/upgrade-course/{id}/validate-form', [Frontend\LearnerController::class, 'validateUpgradeCourseForm']);
        Route::get('/upgrade/get-manuscript/{id}', [Frontend\LearnerController::class, 'getUpgradeManuscript'])->name('learner.get-upgrade-manuscript');
        Route::post('/upgrade-manuscript/{id}/validate-form', [Frontend\LearnerController::class, 'validateUpgradeManuscriptForm']);
        Route::post('/upgrade/manuscript/{id}', [Frontend\LearnerController::class, 'upgradeManuscript'])->name('learner.upgrade-manuscript');
        Route::post('/upgrade/autoRenew', [Frontend\LearnerController::class, 'setAutoRenewCourses'])->name('learner.upgrade-auto-renew');
        Route::get('/upgrade/assignment/{id}', [Frontend\LearnerController::class, 'getUpgradeAssignment'])->name('learner.get-upgrade-assignment'); // Assignment Add on Page
        Route::post('/upgrade/assignment/{id}/validate-form', [Frontend\LearnerController::class, 'validateUpgradeAssignmentForm']);
        Route::post('/upgrade/assignment/{id}', [Frontend\LearnerController::class, 'upgradeAssignment'])->name('learner.upgrade-assignment'); // Assignment Add on Page
        Route::get('/survey/{id}', [Frontend\LearnerController::class, 'survey'])->name('learner.survey'); // Survey Page
        Route::post('/take-survey/{id}', [Frontend\LearnerController::class, 'takeSurvey'])->name('learner.take-survey'); // Survey Page
        Route::get('/notifications', [Frontend\LearnerController::class, 'notifications'])->name('learner.notifications'); // Survey Page
        Route::get('diploma/{id}/download', [Frontend\LearnerController::class, 'downloadDiploma'])->name('learner.download-diploma');
        Route::get('course-certificate/{id}/download', [Frontend\LearnerController::class, 'downloadCourseCertificate'])->name('learner.download-course-certificate');
        Route::get('/other-service/{id}/download/{type}', [Frontend\LearnerController::class, 'downloadOtherServiceDoc'])->name('learner.other-service.download-doc'); // Download assignment feedback
        Route::get('/other-service/download-feedback/{id}', [Frontend\LearnerController::class, 'downloadOtherServiceFeedback'])->name('learner.other-service.download-feedback');
        Route::get('/forum', [Frontend\LearnerController::class, 'forum'])->name('learner.forum');
        Route::post('/webinar-auto-register-update', [Frontend\LearnerController::class, 'autoRegisterCourseWebinar']);

        Route::post('/profile', [Frontend\LearnerController::class, 'profileUpdate'])->name('learner.profile.update'); // Profile Update
        Route::post('/profile/photo', [Frontend\LearnerController::class, 'profileUpdatePhoto'])->name('learner.profile.update-photo'); // Profile Update
        Route::post('/password/update', [Frontend\LearnerController::class, 'passwordUpdate'])->name('learner.password.update'); // Profile Update
        Route::post('/course/take', [Frontend\LearnerController::class, 'takeCourse'])->name('learner.course.take'); // Take Course
        Route::post('/course/{id}/uploadManuscript', [Frontend\LearnerController::class, 'uploadManuscript'])->name('learner.course.uploadManuscript'); // Upload manuscript to course
        Route::post('/shop-manuscript/{id}/comment', [Frontend\LearnerController::class, 'shopManuscriptPostComment'])->name('learner.shop-manuscript.post-comment'); // Shop Manuscript Show Page
        Route::post('/assignment/{id}/upload', [Frontend\LearnerController::class, 'assignmentManuscriptUpload'])->name('learner.assignment.add_manuscript'); // Upload assignment manuscript
        Route::post('/group/{group_id}/learner/{id}/submit_feedba', [Frontend\LearnerController::class, 'submit_feedback'])->name('learner.assignment.group.submit_feedback'); // Submit feedback manuscript
        Route::post('/feedback/{id}/replace_feedback', [Frontend\LearnerController::class, 'replaceFeedback'])->name('learner.assignment.group.replace_feedback'); // Submit feedback manuscript
        Route::post('/feedback/{id}/delete_feedback', [Frontend\LearnerController::class, 'deleteFeedback'])->name('learner.assignment.group.delete_feedback'); // Submit feedback manuscript
        Route::post('/shop-manuscript/{id}/upload', [Frontend\ShopManuscriptController::class, 'upload_manuscript'])->name('learner.shop-manuscript.upload'); // Upload shop manuscript
        Route::post('/shop-manuscript/{id}/upload-synopsis', [Frontend\ShopManuscriptController::class, 'upload_synopsis'])->name('learner.shop-manuscript.upload_synopsis'); // Upload shop manuscript
        Route::post('/shop-manuscript/{id}/update-uploaded-manuscript', [Frontend\ShopManuscriptController::class, 'updateUploadedManuscript'])->name('learner.shop-manuscript.update-uploaded-manuscript'); // update Uploade shop manuscript
        Route::post('/shop-manuscript/{id}/delete-uploaded-manuscript', [Frontend\ShopManuscriptController::class, 'deleteUploadedManuscript'])->name('learner.shop-manuscript.delete-uploaded-manuscript'); // update Uploade shop manuscript
        Route::get('/download/invoice/{id}/credit-note', [Frontend\LearnerController::class, 'downloadCreditNote'])->name('learner.download.credit-note');
        Route::get('/download/time-register-invoice/{id}', [Frontend\LearnerController::class, 'downloadTimeRegisterInvoice'])->name('learner.download.time-register-invoice');
        Route::get('/download/invoice/{url}', [Frontend\LearnerController::class, 'downloadInvoice'])->name('learner.download.invoice')
            ->where('url', '.*'); // to accept url as parameter

        // Pilot Reader
        Route::get('/book-author', [Frontend\PilotReaderAuthorController::class, 'bookAuthor'])->name('learner.book-author'); // Book Reader Author Page
        Route::get('/book-author/create', [Frontend\PilotReaderAuthorController::class, 'bookAuthorCreate'])->name('learner.book-author-create'); // Book Reader Author Create Page
        Route::post('/book-author/create', [Frontend\PilotReaderAuthorController::class, 'bookAuthorCreate'])->name('learner.book-author-create.submit'); // Book Reader Author Create Page
        Route::get('/book-author/book/{id}', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBook'])->name('learner.book-author-book-show'); // Book Reader Author Show Book Page
        Route::get('/book-author/book/{id}/invitation', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookInvitation'])->name('learner.book-author-book-invitation'); // Book Reader Author Show Invitation Page
        Route::post('/book-author/book/{id}/invitation', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookInvitationSend'])->name('learner.book-author-book-invitation-send'); // Book Reader Author Send Invitation Page
        Route::post('/book/invite/send', [Frontend\PilotReaderBookSettingsController::class, 'authenticatedSendInvitation'])->name('account.book.invite.send');
        Route::post('/book-author/book/settings/invite/link/get', [Frontend\PilotReaderBookSettingsController::class, 'getInvitationLink'])->name('learner.book-author.settings.get-invite-link');
        Route::get('/book-author/book/{id}/track-readers', [Frontend\PilotReaderAuthorController::class, 'bookAuthorTrackReaders'])->name('learner.book-author-book-track-readers'); // Book Reader Author Show Invitation Page
        Route::get('/book-author/book/{id}/feedback-list', [Frontend\PilotReaderAuthorController::class, 'bookAuthorFeedbackList'])->name('learner.book-author-book-feedback-list'); // Book Reader Author Show Invitation Page
        Route::get('/book-author/book/{id}/settings', [Frontend\PilotReaderBookSettingsController::class, 'bookSettings'])->name('learner.book-author-book-settings');
        Route::post('/book-author/book/settings/set', [Frontend\PilotReaderBookSettingsController::class, 'setBookSettings'])->name('learner.book-author-set-book-settings');
        Route::post('/book/settings/reading/status/set', [Frontend\PilotReaderBookSettingsController::class, 'setReadingStatus'])->name('learner.book-settings-reading-status-set');
        Route::get('/book-author/book/{id}/reader-feedback-list', [Frontend\PilotReaderAuthorController::class, 'bookAuthorReaderFeedbackList'])->name('learner.book-author-book-reader-feedback-list');
        Route::post('/book-author/book/{id}/validate-email', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookInvitationValidateEmail'])->name('learner.book-author-book-invitation-validate-email'); // Book Reader Author Send Invitation Page
        Route::get('/book-author/book/{id}/list-invitation/{status}', [Frontend\PilotReaderAuthorController::class, 'listInvitations'])->name('learner.book-author-book-list-invitation'); // Book Reader Author Send Invitation Page
        Route::post('/book-author/book/invitation/cancel', [Frontend\PilotReaderAuthorController::class, 'cancelInvitation'])->name('learner.book-cancel-invitation'); // Book Reader Author Send Invitation Page
        Route::post('/book/settings/reader/role/set', [Frontend\PilotReaderBookSettingsController::class, 'setReaderRole'])->name('learner.book.settings.set-reader-role');
        Route::post('/book-author/book/reader/restore-remove', [Frontend\PilotReaderAuthorController::class, 'restoreOrRemoveReader']);
        Route::get('/book/invitation/{_token}/{action}', [Frontend\PilotReaderAuthorController::class, 'bookInvitationAction'])->name('learner.book-invitation-action'); // Book Reader Author Show Invitation Page
        Route::get('/book/invitation/{id}/decline', [Frontend\PilotReaderAuthorController::class, 'bookInvitationDecline'])->name('learner.book-invitation-decline'); // Book Reader Author Show Invitation Page
        Route::put('/book-author/book/{id}/update', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookUpdate'])->name('learner.book-author-book-update'); // Book Reader Author Update Book Page
        Route::get('/book-author/book/{id}/chapter/new/{type}', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookCreateChapter'])->name('learner.book-author-book-create-chapter'); // Book Reader Author Book Chapter Create Page
        Route::post('/book-author/book/{id}/chapter/new/{type}', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookCreateChapter'])->name('learner.book-author-book-create-chapter.create'); // Book Reader Author Book Chapter Create Page
        Route::post('/book-author/book/{id}/sort-chapter', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookSortChapter'])->name('learner.book-author-book-sort-chapter'); // Update the chapter sort
        Route::post('/book/chapter/{id}/update-field', [Frontend\PilotReaderAuthorController::class, 'bookChapterUpdateField'])->name('learner.book-chapter-update-field'); // Update the chapter by field
        Route::get('/book-author/book/{book_id}/chapter/{chapter_id}', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookViewChapter'])->name('learner.book-author-book-view-chapter'); // Book Reader Author Book Chapter View Page
        Route::get('/book-author/book/{book_id}/chapter/{chapter_id}/edit', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookUpdateChapter'])->name('learner.book-author-book-update-chapter'); // Book Reader Author Book Chapter Update Page
        Route::put('/book-author/book/{book_id}/chapter/{chapter_id}/edit', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookUpdateChapter'])->name('learner.book-author-book-update-chapter.submit'); // Book Reader Author Book Chapter Update Page
        Route::delete('/book-author/book/{book_id}/chapter/{chapter_id}/delete', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookDeleteChapter'])->name('learner.book-author-book-delete-chapter'); // Book Reader Author Book Chapter Update Page
        Route::post('/book-author/book/destroy', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookDelete'])->name('learner.book-author-book-destroy'); // Book Reader Author Show Book Page
        Route::post('/chapter/feedback/create', [Frontend\PilotReaderAuthorController::class, 'authorChapterFeedbackCreate'])->name('learner.book-author-book-chapter-feedback-create'); // Book Reader Author Book Chapter Note Create
        Route::post('/chapter/feedback/update', [Frontend\PilotReaderAuthorController::class, 'authorChapterFeedbackUpdate'])->name('learner.book-author-book-chapter-feedback-update'); // Book Reader Author Book Chapter Note Create
        Route::post('/chapter/note/create', [Frontend\PilotReaderAuthorController::class, 'authorChapterNoteCreate'])->name('learner.book-author-book-chapter-note-create'); // Book Reader Author Book Chapter Note Create
        Route::post('/chapter/note/update', [Frontend\PilotReaderAuthorController::class, 'authorChapterNoteUpdate'])->name('learner.book-author-book-chapter-note-update'); // Book Reader Author Book Chapter Note Update
        Route::post('/chapter/draft/delete', [Frontend\PilotReaderAuthorController::class, 'authorChapterDeleteDraft'])->name('learner.book-author-book-chapter-draft-delete'); // Book Reader Author Book Chapter Note Update
        Route::get('/chapter/{id}/note/list', [Frontend\PilotReaderAuthorController::class, 'authorChapterNoteList'])->name('learner.book-author-book-chapter-note-list'); // Book Reader Author Import Book Page
        Route::get('/book-author/book/{id}/import', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookImport'])->name('learner.book-author-book-import'); // Book Reader Author Import Book Page
        Route::post('/book-author/book/{id}/import', [Frontend\PilotReaderAuthorController::class, 'bookAuthorBookImport'])->name('learner.book-author-book-import.submit'); // Book Reader Author Import Book Page
        Route::post('/book-author/chapter/bulk-import', [Frontend\PilotReaderAuthorController::class, 'saveBulkChapters'])->name('learner.bulk-import-chapter'); // Book Reader Author Import Book Page
        Route::post('/book-author/chapter/bookmark/set', [Frontend\PilotReaderAuthorController::class, 'setBookMark'])->name('learner.book.chapter.set-bookmark'); // Book Reader Author Import Book Page
        Route::get('/book-author/chapter/bookmark/get/{id}', [Frontend\PilotReaderAuthorController::class, 'getBookMark'])->name('learner.book.chapter.get-bookmark'); // Book Reader Author Import Book Page
        Route::get('/reader-directory', [Frontend\PilotReaderDirectoryController::class, 'index'])->name('learner.reader-directory.index');
        Route::get('/reader-directory/about', [Frontend\PilotReaderDirectoryController::class, 'about'])->name('learner.reader-directory.about');
        Route::get('/reader-directory/query/sent/list', [Frontend\PilotReaderDirectoryController::class, 'queryReaderSentList'])->name('learner.reader-directory.query-sent-list');
        Route::get('/reader-directory/query/received/list', [Frontend\PilotReaderDirectoryController::class, 'queryReaderReceivedList'])->name('learner.reader-directory.query-received-list');
        Route::post('/reader-directory/query/list', [Frontend\PilotReaderDirectoryController::class, 'listQueries'])->name('learner.reader-directory.query-reader-list');
        Route::post('/reader-directory/query/decision/submit', [Frontend\PilotReaderDirectoryController::class, 'saveQueryDecision'])->name('learner.reader-directory.query-decision-submit');
        Route::post('/reader-directory/list', [Frontend\PilotReaderDirectoryController::class, 'listReaderProfile'])->name('learner.reader-directory.list-profile');
        Route::post('/reader-directory/list/book', [Frontend\PilotReaderDirectoryController::class, 'listBook'])->name('learner.reader-directory.list-book');
        Route::post('/reader-directory/query/sent', [Frontend\PilotReaderDirectoryController::class, 'queryReader'])->name('learner.reader-directory.query-sent');
        Route::get('/pilot-reader/profile', [Frontend\PilotReaderAccountController::class, 'index'])->name('learner.pilot-reader.account.index'); // Book Reader Author Import Book Page
        Route::get('/pilot-reader/profile/preferences/view', [Frontend\PilotReaderAccountController::class, 'viewUserPreferences'])->name('learner.pilot-reader.account.preferences.view'); // Book Reader Author Import Book Page
        Route::post('/pilot-reader/profile/preferences/set', [Frontend\PilotReaderAccountController::class, 'setUserPreferences'])->name('learner.pilot-reader.account.preferences.set'); // Book Reader Author Import Book Page
        Route::get('/pilot-reader/profile/reader', [Frontend\PilotReaderAccountController::class, 'readerProfile'])->name('learner.pilot-reader.account.reader-profile'); // Book Reader Author Import Book Page
        Route::get('/pilot-reader/profile/reader/view', [Frontend\PilotReaderAccountController::class, 'viewReaderProfile'])->name('learner.pilot-reader.account.reader-profile-view'); // Book Reader Author Import Book Page
        Route::post('/pilot-reader/profile/reader/set', [Frontend\PilotReaderAccountController::class, 'setReaderProfile'])->name('learner.pilot-reader.account.reader-profile-set');
        Route::post('/notification/{id}/mark-as-read', [Frontend\LearnerController::class, 'markNotificationAsRead'])->name('learner.notification.mark-as-read');
        Route::post('/notification/{id}/delete', [Frontend\LearnerController::class, 'deleteNotification'])->name('learner.notification.delete');
        Route::get('/private-groups', [Frontend\PrivateGroupsController::class, 'index'])->name('learner.private-groups.index');
        Route::get('/private-groups/{id}', [Frontend\PrivateGroupsController::class, 'show'])->name('learner.private-groups.show');
        Route::get('/private-groups/{id}/get-data', [Frontend\PrivateGroupsController::class, 'getGroupData'])->name('learner.private-groups.get-data');
        Route::post('/private-groups/create', [Frontend\PrivateGroupsController::class, 'createGroup'])->name('learner.private-groups.create');
        Route::post('/private-groups/update', [Frontend\PrivateGroupsController::class, 'updateGroup'])->name('learner.private-groups.update');
        Route::get('/private-groups/{id}/discussions', [Frontend\PrivateGroupDiscussionsController::class, 'index'])->name('learner.private-groups.discussion');
        Route::get('/private-groups/{id}/discussion/{discussion_id}', [Frontend\PrivateGroupDiscussionsController::class, 'show'])->name('learner.private-groups.discussion.show');
        Route::get('/private-groups/discussions/list/{group_id}', [Frontend\PrivateGroupDiscussionsController::class, 'listDiscussion']);
        Route::post('/private-groups/discussions/create', [Frontend\PrivateGroupDiscussionsController::class, 'create']);
        Route::post('/private-groups/discussion/update', [Frontend\PrivateGroupDiscussionsController::class, 'update']);
        Route::get('/private-groups/discussion/replies/get/{id}', [Frontend\PrivateGroupDiscussionRepliesController::class, 'getDiscussionReplies']);
        Route::post('/private-groups/discussion/reply/create', [Frontend\PrivateGroupDiscussionRepliesController::class, 'createReply']);
        Route::post('/private-groups/discussion/reply/update', [Frontend\PrivateGroupDiscussionRepliesController::class, 'updateReply']);
        Route::get('/private-groups/{id}/books', [Frontend\PrivateGroupsController::class, 'books'])->name('learner.private-groups.books');
        Route::get('/private-groups/shared-book/list/{group_id}', [Frontend\PrivateGroupSharedBookController::class, 'listSharedBook']);
        Route::post('/private-groups/shared-book/share', [Frontend\PrivateGroupSharedBookController::class, 'shareBook']);
        Route::post('/private-groups/shared-book/update', [Frontend\PrivateGroupSharedBookController::class, 'updateSharedBook']);
        Route::post('/private-groups/shared-book/remove', [Frontend\PrivateGroupSharedBookController::class, 'destroySharedBook']);
        Route::get('/private-groups/shared-book/book/{book_id}', [Frontend\PrivateGroupSharedBookController::class, 'getBookDetail']);
        Route::post('/private-groups/shared-book/book/become-reader', [Frontend\PrivateGroupSharedBookController::class, 'becomeReader']);
        Route::get('/private-groups/{id}/preferences', [Frontend\PrivateGroupsController::class, 'preferences'])->name('learner.private-groups.preferences');
        Route::get('/private-groups/preferences/get/{id}', [Frontend\PrivateGroupsController::class, 'viewPreference'])->name('learner.private-groups.preferences-get');
        Route::post('/private-groups/preferences/set', [Frontend\PrivateGroupsController::class, 'setPreference'])->name('learner.private-groups.preferences-set');
        Route::get('/private-groups/{id}/members', [Frontend\PrivateGroupMembersController::class, 'index'])->name('learner.private-groups.members');
        Route::get('/private-groups/{id}/edit-group', [Frontend\PrivateGroupsController::class, 'editGroup'])->name('learner.private-groups.edit-group');
        Route::post('/private-groups/member/link/get', [Frontend\PrivateGroupMembersController::class, 'getInvitationLink'])->name('learner.private-groups.invitation-link.get');
        Route::get('/private-groups/invitation/{status}/{token}', [Frontend\PrivateGroupMembersController::class, 'confirmInvitation'])->name('learner.private-groups.invitation.action');
        Route::post('/private-group/invite/send', [Frontend\PrivateGroupMembersController::class, 'authenticatedSendInvitation']);
        Route::get('/private-groups/{id}/members/invitations/list/{status}', [Frontend\PrivateGroupMembersController::class, 'listInvitations']);
        Route::post('/private-groups/member/invitation/cancel', [Frontend\PrivateGroupMembersController::class, 'cancelInvitation']);
        Route::post('/private-groups/member/invitation/remove', [Frontend\PrivateGroupMembersController::class, 'removeMember']);

        // Profile Email

        Route::prefix('email')->group(function () {
            Route::get('list', [Frontend\LearnerController::class, 'listEmails']);
            Route::post('primary/set', [Frontend\LearnerController::class, 'setPrimaryEmail']);
            Route::post('destroy', [Frontend\LearnerController::class, 'removeSecondaryEmail']);
            Route::post('confirmation', [Frontend\LearnerController::class, 'sendEmailConfirmation']);
        });

    });

    Route::get('/api/pilotleser/login', [Frontend\LearnerController::class, 'pilotleserLogin']);

    // Authentication
    Route::prefix('auth')->middleware('guest')->group(function () {
        Route::get('login', [Auth\LoginController::class, 'showFrontend'])->name('auth.login.show');
        Route::get('login/self-publishing', [Auth\LoginController::class, 'showSelfPublishing'])->name('auth.login.self-publishing-show');

        Route::post('login', [Auth\LoginController::class, 'login'])->name('frontend.login.store');
        Route::post('login/self-publishing', [Auth\LoginController::class, 'selfPublishingLogin'])->name('frontend.login.self-publishing-store');
        Route::post('checkout/login', [Auth\LoginController::class, 'checkoutLogin'])->name('frontend.login.checkout.store');
        Route::post('register', [Auth\RegisterController::class, 'store'])->name('frontend.register.store');
        Route::post('passwordreset', [Auth\ResetPasswordController::class, 'store'])->name('frontend.passwordreset.store');
        Route::get('passwordreset/{token}', [Auth\ResetPasswordController::class, 'resetForm'])->name('frontend.passwordreset.form');
        Route::post('passwordreset/{token}/update', [Auth\ResetPasswordController::class, 'updatePassword'])->name('frontend.passwordreset.update');
        Route::post('password-change', [Auth\ResetPasswordController::class, 'changePassword'])->name('frontend.password-change');
        Route::get('verify-email/{token}', [Auth\RegisterController::class, 'verifyEmail'])->name('email.verify');

        // Route::get('login/email/{email_hash}', 'LoginController@emailLogin')->name('auth.login.email');
        // Route::get('login/email-normal/{email}', 'LoginController@emailLoginNormal')->name('auth.login.email-normal');

        /*Route::get('login/email-redirect/{email}/{redirect_link}', 'LoginController@emailLoginRedirect')
            ->name('auth.login.emailRedirect');*/

        // socialite route
        Route::get('login/facebook', [Auth\LoginController::class, 'redirectToFacebook'])->name('auth.login.facebook');
        Route::get('login/facebook/callback', [Auth\LoginController::class, 'handleFacebookCallback']);
        Route::get('login/google', [Auth\LoginController::class, 'redirectToGoogle'])->name('auth.login.google');
        Route::get('login/google/callback', [Auth\LoginController::class, 'handleGoogleCallback']);
    });

    // without checking middleware
    Route::prefix('auth')->group(function () {
        Route::get('login/email/{email_hash}', [Auth\LoginController::class, 'emailLogin'])->name('auth.login.email');
        Route::get('login/email-redirect/{email}/{redirect_link}', [Auth\LoginController::class, 'emailLoginRedirect'])
            ->name('auth.login.emailRedirect');
        Route::get('login/email-normal/{email}', [Auth\LoginController::class, 'emailLoginNormal'])->name('auth.login.email-normal');
        Route::get('/vipps-login/{state?}', [Auth\LoginController::class, 'vippsLogin'])->name('auth.login.vipps');
        Route::get('/vipps-login-redirect', [Auth\LoginController::class, 'vippsLoginRedirect']);
    });

    // PAYPAL ROUTES

    Route::get('/paypal/{order?}', [PaypalController::class, 'form'])
        ->name('app.home');

    Route::post('/checkout/payment/{order}/paypal', [PaypalController::class, 'checkout'])
        ->name('checkout.payment.paypal');

    Route::get('/paypal/checkout/{order}/{page?}/completed', [PaypalController::class, 'completed'])
        ->name('paypal.checkout.completed');

    Route::get('/paypal/checkout/{order}/cancelled', [PaypalController::class, 'cancelled'])
        ->name('paypal.checkout.cancelled');

    Route::post('/webhook/paypal/{order?}/{env?}', [PaypalController::class, 'webhook'])
        ->name('webhook.paypal.ipn');

});

/**
 * Admin Routes
 */
Route::domain($admin)->group(function () {

    Route::middleware('admin', 'logActivity')->group(function () {

        // Dashboard Page
        Route::get('/', [Backend\PageController::class, 'dashboard'])->name('backend.dashboard');
        Route::get('backend/{id}/download_manuscript', [Backend\PageController::class, 'downloadManuscript'])->name('backend.download_manuscript');
        Route::get('backend/{id}/download_shop_manuscript', [Backend\PageController::class, 'downloadShopManuscript'])->name('backend.download_shop_manuscript');
        Route::get('backend/{id}/download_assigned_manuscript', [Backend\PageController::class, 'downloadAssignedManuscript'])->name('backend.download_assigned_manuscript');
        Route::post('backend/change-password', [Backend\PageController::class, 'changePassword'])->name('backend.change-password');
        Route::get('/tests', [Backend\PageController::class, 'tests']);
        Route::get('head-editor/dashboard', [Backend\HeadEditorController::class, 'index'])->name('admin.head-editor-dashboard')->middleware('headEditor');
        Route::post('/update-expected-finish/{type}/{id}', [Backend\PageController::class, 'updateExpectedFinish'])->name('backend.update-expected-finish');
        Route::post('/self-publishing/feedback/{id}/approve', [Backend\HeadEditorController::class, 'approveSelfPublishingFeedback'])->name('head_editor.self-publishing-feedback.approve');
        Route::get('/svea-orders', [Backend\PageController::class, 'sveaOrders'])->name('admin.svea.orders');
        Route::post('/self-publishing-request/{id}/approve', [Backend\PageController::class, 'approveSelfPublishingRequest'])->name('admin.self-publishing-portal-request.approve');
        Route::delete('/self-publishing-request/{id}/delete', [Backend\PageController::class, 'deleteSelfPublishingRequest'])->name('admin.self-publishing-portal-request.destroy');
        Route::get('/learner-not-started-manu', [Backend\PageController::class, 'learnerNotStartedManu']);
        Route::get('/learner-avail-course/{year}', [Backend\PageController::class, 'learnerAvailedCourseYear']);
        Route::get('/learner-with-no-paid-records/export', [Backend\PageController::class, 'exportLearnersWithNoPaidRecords']);
        Route::get('/learner-with-no-paid-records/delete', [Backend\PageController::class, 'deleteLearnersWithNoPaidRecords']);
        Route::post('/send-email-to-queue', [Backend\PageController::class, 'sendEmailToQueue'])->name('admin.send-email-to-queue');
        Route::get('/learners/search', [Backend\PageController::class, 'searchLearners']);
        Route::get('/course/{id}/add-coaching-time-to-learners', [Backend\PageController::class, 'addCoachingTimeToCourseLearners']);
        Route::get('/worker-status', [Backend\PageController::class, 'workerStatus']);

        Route::resource('page_meta', Backend\PageMetaController::class, [
            'names' => [
                'index' => 'admin.page_meta.index',
                'store' => 'admin.page_meta.store',
                'update' => 'admin.page_meta.update',
                'destroy' => 'admin.page_meta.delete',
            ],
        ])->except('show', 'create', 'edit');

        // Learners Route
        Route::get('learner/list-notes', [Backend\LearnerController::class, 'listNotes'])->name('admin.learner.list_notes');
        Route::get('learner/generate-password', [Backend\LearnerController::class, 'generatePassword']);
        Route::post('learner/register', [Backend\LearnerController::class, 'registerLearner'])->name('admin.learner.register');
        Route::get('learner/export-vipps-efaktura', [Backend\LearnerController::class, 'exportLearnerWithVipps']);
        Route::get('learner/{id}/email-history', [Backend\LearnerController::class, 'learnerEmailHistory'])->name('admin.learner.email-history');
        Route::post('learner/{id}/set-disable-date', [Backend\LearnerController::class, 'setLearnerDisableDate'])->name('admin.learner.set_disable_date');
        Route::delete('/learner/{id}/remove-disable-date', [Backend\LearnerController::class, 'removeLearnerDisableDate'])->name('admin.learner.remove_disable_date');
        Route::resource('learner', Backend\LearnerController::class, [
            'names' => [
                'index' => 'admin.learner.index',
                'show' => 'admin.learner.show',
                'update' => 'admin.learner.update',
                'destroy' => 'admin.learner.delete',
            ],
        ]);
        Route::get('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}', [Backend\LearnerController::class, 'shopManuscriptTakenShow'])->name('shop_manuscript_taken');
        Route::post('learner/{id}/email', [Backend\LearnerController::class, 'sendEmail'])->name('admin.shop_manuscript_taken.email'); // Send email
        Route::get('shop-manuscript/{id}/download_synopsis', [Backend\LearnerController::class, 'downloadManuscriptSynopsis'])->name('admin.learner.download_synopsis');
        Route::post('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}/comment', [Backend\LearnerController::class, 'shopManuscriptTakenShowComment'])->name('shop_manuscript_taken_comment');
        Route::get('learner/{user_id}/assignment/{id}', [Backend\LearnerController::class, 'assignment'])->name('admin.learner.assignment');
        Route::post('learner/{user_id}/assignment/{id}/delete-add-one', [Backend\LearnerController::class, 'deleteAssignmentAddOn'])->name('admin.learner.assignment.delete-add-one');
        Route::post('learner/{user_id}/auto-renew', [Backend\LearnerController::class, 'setAutoRenewCourses'])->name('admin.learner.update-auto-renew');
        Route::post('learner/{user_id}/could-buy-course', [Backend\LearnerController::class, 'setCouldBuyCourse'])->name('admin.learner.update-could-buy-course');
        Route::post('learner/{user_id}/webinar-auto-register-update', [Backend\LearnerController::class, 'autoRegisterCourseWebinar'])->name('admin.learner.webinar-auto-register-update');
        Route::post('learner/{user_id}/update-is-publishing-learner', [Backend\LearnerController::class, 'isPublishingLearner']);
        Route::get('learner/{user_id}/course-certificate/{certificate_id}/download', [Backend\LearnerController::class, 'downloadCourseCertificate'])->name('admin.learner.download-course-certificate');
        Route::get('learner/order/{order_id}/editing-services', [Backend\LearnerController::class, 'selfPublishingOrders']);

        Route::post('shop-manuscript/{id}/update_document', [Backend\LearnerController::class, 'updateDocumentShopManuscriptTaken'])->name('shop_manuscript_taken.update_document');
        Route::post('shop-manuscript/{id}/synopsis', [Backend\LearnerController::class, 'saveSynopsis'])->name('shop_manuscript_taken.save_synopsis');

        Route::post('learner/activate_course_taken', [Backend\LearnerController::class, 'activate_course_taken'])->name('activate_course_taken');
        Route::post('learner/delete_course_taken', [Backend\LearnerController::class, 'delete_course_taken'])->name('delete_course_taken');
        Route::post('learner/activate_shop_manuscript_taken', [Backend\LearnerController::class, 'activate_shop_manuscript_taken'])->name('activate_shop_manuscript_taken');
        Route::post('learner/delete_shop_manuscript_taken', [Backend\LearnerController::class, 'delete_shop_manuscript_taken'])->name('delete_shop_manuscript_taken');
        Route::post('/learner/{id}/add_shop_manuscript', [Backend\LearnerController::class, 'addShopManuscript'])->name('admin.shop-manuscript.add_learner'); // Shop Manuscript add learner
        Route::post('/learner/{id}/update_workshop_count', [Backend\LearnerController::class, 'updateWorkshopCount'])->name('admin.learner.update_workshop_count'); // Update workshop count for learner
        Route::post('/workshop-taken/{id}/edit-notes', [Backend\LearnerController::class, 'updateWorkshopTakenNotes'])->name('admin.learner.workshop-taken.update-notes'); // Update workshop count for learner
        Route::post('/course_taken/{id}/update_started_at', [Backend\LearnerController::class, 'updateCourseTakenStartedAt'])->name('admin.course_taken.updated_started_at');
        Route::post('/course_taken/{id}/set_availability', [Backend\LearnerController::class, 'setCourseTakenAvailability'])->name('admin.course_taken.set_availability'); // Shop Manuscript add learner
        Route::post('/course_taken/{id}/set-disable-date', [Backend\LearnerController::class, 'setCourseTakenDisableDate'])->name('admin.course_taken.set_disable_date'); // Shop Manuscript add learner
        Route::delete('/course_taken/{id}/remove-disable-date', [Backend\LearnerController::class, 'removeCourseTakenDisableDate'])->name('admin.course_taken.remove_disable_date'); // Shop Manuscript add learner
        Route::post('/course_taken/{id}/send-regret-form', [Backend\LearnerController::class, 'sendRegretForm'])->name('admin.course_taken.send_regret_form');
        Route::post('/course_taken/{course_taken_id}/allow_lesson_access/{lesson_id}', [Backend\LearnerController::class, 'allow_lesson_access'])->name('admin.course_taken.allow_lesson_access'); // allow_lesson_access
        Route::post('/course_taken/{course_taken_id}/default_lesson_access/{lesson_id}', [Backend\LearnerController::class, 'default_lesson_access'])->name('admin.course_taken.default_lesson_access'); // default_lesson_access
        Route::post('/course_taken/{id}/set-expiry-reminder', [Backend\LearnerController::class, 'setCourseTakenExpiryReminder'])
            ->name('admin.course_taken.set_expiry_reminder');
        Route::post('learner/add_to_workshop', [Backend\LearnerController::class, 'addToWorkshop'])->name('learner.add_to_workshop');
        Route::post('learner/add_notes/{id}', [Backend\LearnerController::class, 'addNotes'])->name('learner.add_notes');
        Route::post('/is-manuscript-locked-status', [Backend\LearnerController::class, 'updateManuscriptLockedStatus'])->name('admin.learner.shop-manuscript-taken-locked-status'); // Manuscript lock status
        Route::get('learner/login_activity/{id}', [Backend\LearnerController::class, 'loginActivity'])->name('admin.learner.login_activity');
        Route::get('/word-written-goal/{id}/statistic', [Backend\LearnerController::class, 'goalStatistic'])->name('admin.learner.goal-statistic');
        Route::post('learner/{id}/add-other-service', [Backend\LearnerController::class, 'addOtherService'])->name('admin.learner.add-other-service');
        Route::post('other-service/{id}/assign-editor/{type}', [Backend\LearnerController::class, 'otherServiceAssignEditor'])->name('admin.other-service.assign-editor');
        Route::post('other-service/{id}/delete/{type}', [Backend\LearnerController::class, 'deleteOtherService'])->name('admin.other-service.delete');
        Route::post('coaching-timer/{id}/approve', [Backend\LearnerController::class, 'approveCoachingTimer'])->name('admin.coaching-timer.approve');
        Route::post('learner/{id}/add-coaching-timer', [Backend\LearnerController::class, 'addCoachingTimer'])->name('admin.learner.add-coaching-timer');
        Route::post('learner/{id}/add-diploma', [Backend\LearnerController::class, 'addDiploma'])->name('admin.learner.add-diploma');
        Route::post('diploma/{id}/edit', [Backend\LearnerController::class, 'editDiploma'])->name('admin.learner.edit-diploma');
        Route::delete('diploma/{id}/delete', [Backend\LearnerController::class, 'deleteDiploma'])->name('admin.learner.delete-diploma');
        Route::get('diploma/{id}/download', [Backend\LearnerController::class, 'downloadDiploma'])->name('admin.learner.download-diploma');

        Route::post('learner/invoice/{id}/update-due', [Backend\LearnerController::class, 'updateInvoiceDue'])->name('admin.learner.invoice.update-due');
        Route::delete('learner/invoice/{id}/delete', [Backend\LearnerController::class, 'deleteInvoice'])->name('admin.learner.invoice.delete');
        Route::post('learner/invoice/{id}/create-fiken-credit-note', [Backend\LearnerController::class, 'addFikenCreditNote'])
            ->name('admin.learner.invoice.create-fiken-credit-note');
        Route::post('learner/invoice/{id}/e-faktura', [Backend\LearnerController::class, 'vippsEFaktura'])->name('admin.learner.invoice.vipps-e-faktura');
        Route::post('learner/{user_id}/set-vipss-efaktura', [Backend\LearnerController::class, 'setVippsEFaktura'])->name('admin.learner.set-vipps-e-faktura');
        Route::post('learner/{user_id}/send-username-and-password', [Backend\LearnerController::class, 'sendUsernameAndPassword'])->name('admin.learner.send-username-and-password');
        Route::post('learner/{user_id}/restore-course/{former_course_id}', [Backend\LearnerController::class, 'restoreCourse'])->name('admin.learner.restore-course');
        Route::post('learner/svea/{order_id}/create-credit-note', [Backend\LearnerController::class, 'createSveaCreditNote'])->name('admin.learner.svea.create-credit-note');
        Route::post('learner/svea/{order_id}/deliver-order', [Backend\LearnerController::class, 'deliverSveaOrder'])->name('admin.learner.svea.deliver-order');
        Route::delete('learner/course/{course_taken_id}/delete', [Backend\LearnerController::class, 'deleteFromCourse'])->name('admin.learner.delete-from-course');
        Route::post('learner/{learner_id}/course/{course_taken_id}/renew', [Backend\LearnerController::class, 'renewCourse'])->name('admin.learner.renew-course');
        Route::post('learner/{learner_id}/send-email', [Backend\LearnerController::class, 'sendLearnerEmail'])->name('admin.learner.send-email');
        Route::post('learner/{learner_id}/add-email', [Backend\LearnerController::class, 'addSecondaryEmail'])->name('admin.learner.add-email');
        Route::post('learner/{email_id}/set-primary-email', [Backend\LearnerController::class, 'setPrimaryEmail'])->name('admin.learner.set-primary-email');
        Route::delete('learner/{email_id}/delete-secondary-email', [Backend\LearnerController::class, 'removeSecondaryEmail'])->name('admin.learner.remove-secondary-email');
        Route::post('learner/{learner_id}/save-for-sale-books', [Backend\LearnerController::class, 'saveForSaleBooks'])->name('admin.learner.save-for-sale-books');
        Route::delete('learner/{learner_id}/for-sale-books/{id}/delete', [Backend\LearnerController::class, 'deleteForSaleBooks'])->name('admin.learner.delete-for-sale-books');
        Route::post('learner/{learner_id}/save-book-sales', [Backend\LearnerController::class, 'saveBookSales'])->name('admin.learner.save-book-sales');
        Route::delete('learner/{learner_id}/book-sales/{id}/delete', [Backend\LearnerController::class, 'deleteBookSales'])->name('admin.learner.delete-book-sales');
        Route::post('learner/{learner_id}/webinar-registrant/{registrant_id}/send-email', [Backend\LearnerController::class, 'sendWebinarRegistrantEmail'])
            ->name('admin.learner.send-webinar-registrant-email');

        Route::post('learner/{learner_id}/private-message', [Backend\LearnerController::class, 'addPrivateMessage'])->name('admin.learner.add-private-message');
        Route::put('learner/{learner_id}/private-message/{id}', [Backend\LearnerController::class, 'updatePrivateMessage'])->name('admin.learner.update-private-message');
        Route::delete('learner/{learner_id}/private-message/{id}/delete', [Backend\LearnerController::class, 'deletePrivateMessage'])->name('admin.learner.delete-private-message');
        Route::post('learner/{learner_id}/set-preferred-editor', [Backend\LearnerController::class, 'setPreferredEditor'])->name('admin.learner.set-preferred-editor');
        Route::post('learner/{learner_id}/add-self-publishing', [Backend\LearnerController::class, 'addSelfPublishing'])->name('admin.learner.add-self-publishing');

        Route::post('task/{id}/finish', [Backend\TaskController::class, 'finishTask'])->name('admin.task.finish');
        Route::resource('task', Backend\TaskController::class, [
            'names' => [
                'index' => 'admin.task.index',
                'show' => 'admin.task.show',
                'create' => 'admin.task.create',
                'store' => 'admin.task.store',
                'edit' => 'admin.task.edit',
                'update' => 'admin.task.update',
                'destroy' => 'admin.task.destroy',
            ],
        ]);

        // Course Testimonials Route
        Route::resource('course/testimonial', Backend\CourseTestimonialController::class, [
            'names' => [
                'index' => 'admin.course-testimonial.index',
                'show' => 'admin.course-testimonial.show',
                'create' => 'admin.course-testimonial.create',
                'store' => 'admin.course-testimonial.store',
                'edit' => 'admin.course-testimonial.edit',
                'update' => 'admin.course-testimonial.update',
                'destroy' => 'admin.course-testimonial.destroy',
            ],
        ]);
        Route::post('course/testimonial/{id}/clone', [Backend\CourseTestimonialController::class, 'cloneRecord'])->name('admin.course-testimonial.clone');

        // Course Testimonials Route
        Route::resource('course/video/testimonial', Backend\CourseVideoTestimonialController::class, [
            'names' => [
                'create' => 'admin.course-video-testimonial.create',
                'store' => 'admin.course-video-testimonial.store',
                'edit' => 'admin.course-video-testimonial.edit',
                'update' => 'admin.course-video-testimonial.update',
                'destroy' => 'admin.course-video-testimonial.destroy',
            ],
        ]);

        // Courses Route
        Route::get('course/get-all-learners', [Backend\CourseController::class, 'getAllPaidLearners']); // get all learners that avail a paid course
        Route::get('course/webinars', [Backend\CourseController::class, 'allUpcomingWebinars'])->name('admin.course.all-upcoming-webinars');
        Route::get('course/export-no-certificate', [Backend\CourseController::class, 'exportCoursesWithNoCertificate']);
        Route::resource('course', Backend\CourseController::class, [
            'names' => [
                'index' => 'admin.course.index',
                'show' => 'admin.course.show',
                'create' => 'admin.course.create',
                'store' => 'admin.course.store',
                'edit' => 'admin.course.edit',
                'update' => 'admin.course.update',
                'destroy' => 'admin.course.destroy',
            ],
        ]);
        Route::post('course/{id}/update/email', [Backend\CourseController::class, 'update_email'])->name('admin.course.update.email');
        Route::post('course/{id}/welcome-email/send', [Backend\CourseController::class, 'sendWelcomeEmail'])->name('admin.course.welcome-email.send');
        Route::post('course/{id}/clone', [Backend\CourseController::class, 'clone_course'])->name('admin.course.clone');
        Route::post('course/{id}/add_similar_course', [Backend\CourseController::class, 'add_similar_course'])->name('admin.course.add_similar_course');
        Route::post('course/remove_similar_course/{similar_course_id}', [Backend\CourseController::class, 'remove_similar_course'])->name('admin.course.remove_similar_course');
        Route::post('/course/learner/add', [Backend\LearnerController::class, 'addLearner'])->name('learner.course.add.learner'); // Add Learner To Course
        Route::post('/course/learner/add-bulk', [Backend\LearnerController::class, 'addBulkLearners'])->name('learner.course.add-bulk.learner'); // Add Learner To Course
        Route::post('/course/learner/remove', [Backend\LearnerController::class, 'removeLearner'])->name('learner.course.remove.learner'); // Remove Learner From Course
        Route::post('/course-status', [Backend\CourseController::class, 'updateStatus'])->name('learner.course.status'); // Courses Page
        Route::post('/course-for-sale', [Backend\CourseController::class, 'updateForSaleStatus'])->name('learner.course.for-sale-status'); // Courses For Sale Status
        Route::post('/course-is-free', [Backend\CourseController::class, 'updateIsFreeStatus'])->name('learner.course.is-free-status'); // Courses For Sale Status
        Route::post('/course/{id}/send-email-to-learners', [Backend\CourseController::class, 'sendEmailToLearners'])->name('learner.course.send-email-to-learners'); // Add Learner To Course
        Route::post('/course/{id}/not-started-reminder', [Backend\CourseController::class, 'notStartedCourseReminder'])->name('learner.course.not-started-reminder');
        Route::post('/course/{id}/set-course-taken-end-date', [Backend\CourseController::class, 'setCourseTakenEndDate'])->name('learner.course.set-end-date');
        Route::get('/course/{id}/learner-list-excel/{type?}', [Backend\CourseController::class, 'learnerListExcel'])->name('learner.course.learner-list-excel'); // Add Learner To Course
        Route::get('/course/{id}/learner-active-list-excel', [Backend\CourseController::class, 'learnerActiveListExcel'])->name('learner.course.learner-active-list-excel'); // Add Learner To Course
        Route::get('/course/{id}/pay-later/export', [Backend\CourseController::class, 'exportPayLaterLearners'])->name('learner.course.pay-later'); // Add Learner To Course
        Route::post('/course/{course}/payment-plans/toggle', [Backend\CourseController::class, 'togglePaymentPlan'])->name('learner.course.payment-plans.toggle');
        Route::post('/course/{id}/expirationReminder', [Backend\CourseController::class, 'expirationReminder'])->name('admin.course.expiration-reminder');
        Route::post('/course/{id}/add-learners-to-webinars', [Backend\CourseController::class, 'addLearnersToWebinars'])->name('admin.course.add-learners-to-webinars');
        Route::post('/course/{id}/certificate-dates', [Backend\CourseController::class, 'updateCertificateDates'])->name('admin.course.update-certificate-dates');
        Route::get('course/resend-welcome-email/{package_id}/{user_id}/{course_taken_id}', [Backend\CourseController::class, 'resendWelcomeEmailToUser']);
        // Route::get('course/{id}/certificate', 'CourseController@certificate')->name('admin.course.certificate');
        Route::get('course/{id}/download-certificate', [Backend\CourseController::class, 'downloadCertificate'])->name('admin.course.download-certificate-template');
        Route::post('course/{id}/save-certificate-template', [Backend\CourseController::class, 'saveCertificateTemplate'])->name('admin.course.save-certificate-template');
        Route::post('course/{id}/add-coaching-time', [Backend\CourseController::class, 'addCoachingTime'])->name('admin.course.add-coaching-time');
        Route::get('/course/{id}/export-hidden-webinars', [Backend\CourseController::class, 'exportHiddenWebinars']);
        Route::get('/course/export-former-learners/bulk', [Backend\CourseController::class, 'exportFormerLearners']);
        Route::get('/course/export-current-learners/bulk', [Backend\CourseController::class, 'exportCurrentLearners']);
        Route::get('/course/application/{id}/details', [Backend\CourseController::class, 'applicationDetails']);
        Route::get('/course/application/{id}/download', [Backend\CourseController::class, 'applicationDownload'])->name('admin.course.application.download');
        Route::post('/course/application/{id}/approve', [Backend\CourseController::class, 'applicationApprove'])->name('admin.course.application.approve');
        Route::delete('/course/application/{id}/delete', [Backend\CourseController::class, 'applicationDelete'])->name('admin.course.application.delete');
        Route::post('/course/package/copy-learners', [Backend\CourseController::class, 'copyPackageLearners'])
            ->name('admin.course.package.copy-learners');
        Route::post('/course/package/import-learners', [Backend\CourseController::class, 'importPackageLearners'])
            ->name('admin.course.package.import-learners');
        Route::post('/course/package/copy-package-and-learners', [Backend\CourseController::class, 'copyPackageAndLearners'])
            ->name('admin.course.package.copy-package-and-learners');
        Route::post('course-taken/{id}/update-can-receive-email', [Backend\CourseController::class, 'canReceiveEmailUpdate']);
        Route::post('course-taken/{id}/update-in-facebook-group', [Backend\CourseController::class, 'inFacebookGroupUpdate']);
        Route::post('course-taken/{id}/exclude-in-registration', [Backend\CourseController::class, 'excludeInScheduledRegistration']);

        Route::get('/shareable-course/get-package/{course_id}', [Backend\ShareableCourseController::class, 'getCoursePackage']);
        Route::resource('shareable-course', Backend\ShareableCourseController::class, [
            'names' => [
                'index' => 'admin.shareable-course.index',
                'store' => 'admin.shareable-course.store',
                'update' => 'admin.shareable-course.update',
                'destroy' => 'admin.shareable-course.destroy',
            ],
        ])->except('crete', 'show', 'edit');

        // Email Out Route
        Route::post('/course/{course_id}/email-out/{email_out}/send-email', [Backend\EmailOutController::class, 'sendEmailToLearners'])
            ->name('admin.email-out.send-email');
        Route::resource('/course/{course_id}/email-out', Backend\EmailOutController::class, [
            'names' => [
                'create' => 'admin.email-out.create',
                'store' => 'admin.email-out.store',
                'edit' => 'admin.email-out.edit',
                'update' => 'admin.email-out.update',
                'destroy' => 'admin.email-out.destroy',
            ],
        ])->except('show');

        // Course Reward Coupon Route
        Route::resource('/course/{course_id}/reward-coupons', Backend\CourseRewardCouponController::class, [
            'names' => [
                'create' => 'admin.reward-coupons.create',
                'store' => 'admin.reward-coupons.store',
                'edit' => 'admin.reward-coupons.edit',
                'update' => 'admin.reward-coupons.update',
                'destroy' => 'admin.reward-coupons.destroy',
            ],
        ])->except('show');

        Route::get('/course/{course_id}/package/{package_id}/certificate', [Backend\CourseController::class, 'certificate'])
            ->name('admin.package.certificate');
        Route::get('/course/{course_id}/package/{package_id}/download-certificate', [Backend\CourseController::class, 'downloadPackageCertificate'])
            ->name('admin.package.download-certificate-template');
        Route::post('/course/{course_id}/package/{package_id}/save-certificate-template', [Backend\CourseController::class, 'savePackageCertificateTemplate'])
            ->name('admin.package.save-certificate-template');

        Route::post('/course/{course_id}/reward-coupons/multiple-store', [Backend\CourseRewardCouponController::class, 'multipleStore'])
            ->name('admin.reward-coupons.multiple-store');

        Route::get('/course/{course_id}/reward-coupons/export-to-text', [Backend\CourseRewardCouponController::class, 'exportToText'])
            ->name('admin.reward-coupons.export-to-text');

        Route::resource('course/{id}/discount', Backend\CourseDiscountController::class, [
            'names' => [
                'index' => 'admin.course-discount.index',
                'store' => 'admin.course-discount.store',
                'update' => 'admin.course-discount.update',
                'destroy' => 'admin.course-discount.destroy',
            ],
        ])->except('show', 'create', 'edit');

        // Free Courses Route
        Route::resource('free-course', Backend\FreeCourseController::class, [
            'names' => [
                'index' => 'admin.free-course.index',
                'store' => 'admin.free-course.store',
                'update' => 'admin.free-course.update',
                'destroy' => 'admin.free-course.destroy',
            ],
        ])->except('show', 'create', 'edit');

        Route::post('free-course/webinar', [Backend\FreeCourseController::class, 'storeWebinar'])->name('admin.free-webinar.store');
        Route::put('free-course/webinar/{id}/update', [Backend\FreeCourseController::class, 'updateWebinar'])->name('admin.free-webinar.update');
        Route::delete('free-course/webinar/{id}/delete', [Backend\FreeCourseController::class, 'deleteWebinar'])->name('admin.free-webinar.destroy');
        Route::post('free-course/webinar/{id}/presenter/store', [Backend\FreeCourseController::class, 'storeWebinarPresenter'])->name('admin.free-webinar.presenter.store');
        Route::put('free-course/webinar/{webinar_id}/presenter/{id}/update', [Backend\FreeCourseController::class, 'updateWebinarPresenter'])->name('admin.free-webinar.presenter.update');
        Route::delete('free-course/webinar/{webinar_id}/presenter/{id}/delete', [Backend\FreeCourseController::class, 'deleteWebinarPresenter'])->name('admin.free-webinar.presenter.delete');

        // Package Route
        Route::resource('course/{course_id}/package', Backend\PackageController::class, [
            'names' => [
                'store' => 'admin.course.package.store',
                'update' => 'admin.course.package.update',
                'destroy' => 'admin.course.package.destroy',
            ],
        ]);

        Route::post('course/{course_id}/package/{package_id}/include-coaching', [Backend\PackageController::class, 'includeCoaching'])
            ->name('admin.course.package.include-coaching');
        Route::get('course/package/generate-editor-package', [Backend\PackageController::class, 'generateEditorPackageForActiveCourses']);

        // Package Course  Route
        Route::resource('package_course', Backend\PackageCourseController::class, [
            'names' => [
                'store' => 'admin.package_course.store',
                'destroy' => 'admin.package_course.destroy',
            ],
        ])->except('show', 'index', 'edit', 'update', 'create');

        // Workshop Route
        Route::resource('workshop', Backend\WorkshopController::class, [
            'names' => [
                'index' => 'admin.workshop.index',
                'show' => 'admin.workshop.show',
                'create' => 'admin.workshop.create',
                'store' => 'admin.workshop.store',
                'edit' => 'admin.workshop.edit',
                'update' => 'admin.workshop.update',
                'destroy' => 'admin.workshop.destroy',
            ],
        ]);
        Route::post('workshop/{workshop_taken_id}/attendee/{attendee_id}', [Backend\WorkshopController::class, 'removeAttendee'])->name('admin.workshop.remove_attendee');
        Route::post('workshop/{id}/download_pdf', [Backend\WorkshopController::class, 'downloadAttendees'])->name('admin.workshop.download_pdf');
        Route::get('workshop/{id}/download_excel', [Backend\WorkshopController::class, 'downloadAttendeesExcel'])->name('admin.workshop.download_excel');
        Route::post('workshop/{id}/add-to-course', [Backend\WorkshopController::class, 'addLearnersToCourse'])->name('admin.workshop.add-learners-to-course');
        Route::post('workshop/{id}/send_email', [Backend\WorkshopController::class, 'sendEmailToAttendees'])->name('admin.workshop.send_email');
        Route::get('workshop/{id}/view_email_attendees', [Backend\WorkshopController::class, 'viewEmailLogAttendees'])->name('admin.workshop.send_email_log');
        Route::post('/workshop-status', [Backend\WorkshopController::class, 'updateStatus'])->name('admin.workshop.status'); // Courses Page
        Route::post('/workshop-for-sale', [Backend\WorkshopController::class, 'updateForSaleStatus'])->name('admin.workshop.for-sale-status'); // Courses For Sale Status
        Route::post('workshop/{id}/update/email', [Backend\WorkshopController::class, 'update_email'])->name('admin.workshop.update.email');

        // Workshop Presenter Route
        Route::resource('workshop/{workshop_id}/workshop-presenter', Backend\WorkshopPresenterController::class, [
            'names' => [
                'store' => 'admin.course.workshop-presenter.store',
                'update' => 'admin.course.workshop-presenter.update',
                'destroy' => 'admin.course.workshop-presenter.destroy',
            ],
        ])->except('index', 'show', 'create', 'edit');

        // Workshop Menu Route
        Route::resource('workshop/{workshop_id}/workshop-menu', Backend\WorkshopMenuController::class, [
            'names' => [
                'store' => 'admin.course.workshop-menu.store',
                'update' => 'admin.course.workshop-menu.update',
                'destroy' => 'admin.course.workshop-menu.destroy',
            ],
        ])->except('index', 'show', 'create', 'edit');

        // Lessons Route
        Route::resource('/course/{course_id}/lesson', Backend\LessonController::class, [
            'names' => [
                'create' => 'admin.lesson.create',
                'store' => 'admin.lesson.store',
                'edit' => 'admin.lesson.edit',
                'update' => 'admin.lesson.update',
                'destroy' => 'admin.lesson.destroy',
            ],
        ])->except('show');
        Route::post('/lesson/save_order', [Backend\LessonController::class, 'save_order'])->name('admin.lesson.save_order'); // Save lesson order
        Route::get('/lesson/download-document/{id}', [Backend\LessonController::class, 'downloadLessonDocument'])->name('admin.lesson.download-lesson-document');
        Route::delete('/lesson/delete-document/{id}', [Backend\LessonController::class, 'deleteLessonDocument'])->name('admin.lesson.delete-lesson-document');
        Route::delete('/lesson/delete-whole-file/{id}', [Backend\LessonController::class, 'deleteLessonFile'])->name('admin.lesson.delete-lesson-whole-file');
        Route::post('/lesson/{id}/add-content', [Backend\LessonController::class, 'addContent'])->name('admin.lesson.add_content'); // Save lesson order
        Route::get('/lesson/{id}/get-lesson-content', [Backend\LessonController::class, 'getLessonContent'])->name('admin.lesson.get_lesson_content'); // Save lesson order
        Route::post('/lesson-content/{id}/delete-lesson-content', [Backend\LessonController::class, 'deleteLessonContent'])->name('admin.lesson.delete_lesson_content'); // Save lesson order

        // Lessons Route
        Route::get('/admin/export_nearly_expired_courses', [Backend\AdminController::class, 'exportNearlyExpiredCourses'])->name('admin.admin.export_nearly_expired_courses');
        Route::post('/admin/{id}/page-access', [Backend\AdminController::class, 'pageAccess'])->name('admin.admin.page-access');
        Route::post('/admin-status', [Backend\AdminController::class, 'adminStatus'])->name('admin.admin.status');
        Route::post('/admin/type-change', [Backend\AdminController::class, 'adminTypeChange']);
        Route::get('/admin/clear/cache', [Backend\AdminController::class, 'clearCache'])->name('admin.clear.cache');
        Route::resource('/admin', Backend\AdminController::class, [
            'names' => [
                'index' => 'admin.admin.index',
                'show' => 'admin.admin.show',
                'store' => 'admin.admin.store',
                'update' => 'admin.admin.update',
                'destroy' => 'admin.admin.destroy',
            ],
        ])->except('create', 'edit');
        Route::post('/save-staff/{id?}', [Backend\AdminController::class, 'saveStaff'])->name('admin.staff.save');
        Route::delete('/delete-staff/{id?}', [Backend\AdminController::class, 'deleteStaff'])->name('admin.staff.delete');
        Route::get('/fiken-redirect', [Backend\AdminController::class, 'fikenRedirect'])->name('admin.fiken.redirect');

        Route::post('/contract/{id}/send-contract', [Backend\ContractController::class, 'sendContract'])
            ->name('admin.contract.send-contract');
        Route::post('/contract/template/save/{id?}', [Backend\ContractController::class, 'saveContractTemplate'])->name('admin.contract-template.save');
        Route::delete('/contract/template/delete/{id?}', [Backend\ContractController::class, 'deleteContractTemplate'])->name('admin.contract-template.delete');
        Route::post('/contract/{id}/sign', [Backend\ContractController::class, 'signContract'])->name('admin.contract.sign');
        Route::post('/contract/{id}/status', [Backend\ContractController::class, 'contractStatus'])->name('admin.contract.status');
        Route::get('/contract/{id}/download-pdf', [Backend\ContractController::class, 'downloadPDF'])->name('admin.contract.download-pdf');
        Route::resource('/contract', Backend\ContractController::class, [
            'names' => [
                'index' => 'admin.contract.index',
                'create' => 'admin.contract.create',
                'show' => 'admin.contract.show',
                'store' => 'admin.contract.store',
                'edit' => 'admin.contract.edit',
                'update' => 'admin.contract.update',
                'destroy' => 'admin.contract.destroy',
            ],
        ]);

        Route::resource('/email', Backend\EmailController::class, [
            'names' => [
                'index' => 'admin.email.index',
                'show' => 'admin.email.show',
                'store' => 'admin.email.store',
                'update' => 'admin.email.update',
                'destroy' => 'admin.email.destroy',
            ],
        ])->except('create', 'edit');

        Route::post('email/login', [Backend\EmailController::class, 'login'])->name('admin.email.login');
        Route::get('email/move/{id}', [Backend\EmailController::class, 'move'])->name('admin.email.move');
        Route::get('email/delete/{id}', [Backend\EmailController::class, 'delete'])->name('admin.email.delete');
        Route::post('email/forward/{id}', [Backend\EmailController::class, 'forward'])->name('admin.email.forward');
        Route::post('email/reply', [Backend\EmailController::class, 'reply'])->name('admin.email.reply');

        // Videos Route
        Route::resource('video', Backend\VideoController::class, [
            'names' => [
                'store' => 'admin.video.store',
                'update' => 'admin.video.update',
                'destroy' => 'admin.video.destroy',
            ],
        ]);

        // Webinar Route
        Route::resource('webinar', Backend\WebinarController::class, [
            'names' => [
                'store' => 'admin.webinar.store',
                'update' => 'admin.webinar.update',
                'destroy' => 'admin.webinar.destroy',
            ],
        ])->except('create', 'edit', 'show', 'index');
        Route::post('webinar/{id}/delete', [Backend\WebinarController::class, 'destroy'])->name('admin.webinar.delete');
        Route::put('webinar/{id}/make-replay', [Backend\WebinarController::class, 'makeReplay'])->name('admin.webinar.make-replay');
        Route::post('webinar/{id}/set-schedule', [Backend\WebinarController::class, 'setSchedule'])->name('admin.webinar.schedule');
        Route::post('webinar/{id}/update-field', [Backend\WebinarController::class, 'updateField'])->name('admin.webinar.update-field');
        Route::post('webinar/{id}/course/{course_id}/email-out', [Backend\WebinarController::class, 'webinarEmailOut'])->name('admin.webinar.email-out');
        Route::post('webinar/{id}/course/{course_id}/auto-register-learners', [Backend\WebinarController::class, 'autoRegisterLearnersToWebinar'])
            ->name('admin.webinar.auto-register-learners');
        Route::get('/webinar/{id}/registrant/list', [Backend\WebinarController::class, 'registrantList']);
        Route::delete('/webinar/registrant/{id}/delete', [Backend\WebinarController::class, 'removeRegistrant'])
            ->name('admin.webinar.remove-registrant');

        // Webinar Presenter Route
        Route::resource('webinar/{webinar_id}/presenter', Backend\WebinarPresenterController::class, [
            'names' => [
                'store' => 'admin.webinar.webinar-presenter.store',
                'update' => 'admin.webinar.webinar-presenter.update',
                'destroy' => 'admin.webinar.webinar-presenter.destroy',
            ],
        ])->except('index', 'show', 'create', 'edit');

        // Webinar Editor Route
        Route::post('storeWebinarEditor/{webinar_id}', [Backend\WebinarEditorController::class, 'store'])->name('admin.webinar.webinar-editor.store');
        Route::post('updateWebinarEditor/{id}', [Backend\WebinarEditorController::class, 'update'])->name('admin.webinar.webinar-editor.update');
        Route::delete('deleteWebinarEditor/{id}', [Backend\WebinarEditorController::class, 'deleteEditor'])->name('admin.webinar.webinar-editor.delete');

        // Assignments Route
        Route::resource('/assignment', Backend\AssignmentController::class, [
            'names' => [
                'index' => 'admin.assignment.index',
            ],
        ])->except('show', 'create', 'edit', 'store', 'update', 'destroy');

        Route::get('/course/{course_id}/assignment/{assignment_id}/list-manuscripts-without-editor',
            [Backend\AssignmentController::class, 'listManuscriptsWithoutEditor']);
        Route::post('/course/{course_id}/assignment/{assignment_id}/assign-editor-to-manuscripts',
            [Backend\AssignmentController::class, 'assignEditorToManuscripts'])->name('admin.assignment.assign-editor-to-manuscripts');
        Route::post('/assignment-manuscript/{id}/mark-finished', [Backend\AssignmentController::class, 'assignmentManuscriptFinished'])
        ->name('admin.assignment-manuscript.mark-finished');

        Route::resource('course/{course_id}/assignment', Backend\AssignmentController::class, [
            'names' => [
                'show' => 'admin.assignment.show',
                'store' => 'admin.assignment.store',
                'update' => 'admin.assignment.update',
                'destroy' => 'admin.assignment.destroy',
            ],
        ])->except('index', 'create', 'edit');

        Route::get('/power-office/self-publishing/{publishing_id}/add-to-po', [Backend\PowerOfficeController::class, 'addSelfPublshingToPowerOffice'])
            ->name('admin.power-office.self-publishing.add-to-po');
        Route::post('/power-office/self-publishing/{publishing_id}/add-to-po', [Backend\PowerOfficeController::class, 'addSelfPublshingToPowerOffice'])
            ->name('admin.power-office.self-publishing.add-to-po.submit');
        Route::get('/power-office/self-publishing/{publishing_id}/invoice/{invoice_id}/view',
            [Backend\PowerOfficeController::class, 'selfPublishingPowerOfficeInvoice'])
            ->name('admin.power-office.self-publishing.view-po-order');
        Route::get('/power-office/{id}/download', [Backend\PowerOfficeController::class, 'downloadInvoice'])
            ->name('admin.power-office.download');

        Route::post('/project/activity/save', [Backend\ProjectController::class, 'saveActivity']);
        Route::delete('/project/activity/{id}/delete', [Backend\ProjectController::class, 'deleteActivity']);
        Route::post('/project/{id}/notes/save', [Backend\ProjectController::class, 'saveNote']);
        Route::post('/project/{id}/learner/add', [Backend\ProjectController::class, 'addLearner']);
        Route::post('/project/{id}/whole-book/save', [Backend\ProjectController::class, 'saveWholeBook']);
        Route::post('/project/whole-book/{id}/update-status', [Backend\ProjectController::class, 'saveWholeBookStatus']);
        Route::delete('/project/whole-book/{id}/delete', [Backend\ProjectController::class, 'deleteWholeBook']);
        Route::get('/project/{id}/whole-book/{whole_book}/download', [Backend\ProjectController::class, 'downloadWholeBook']);
        Route::delete('/project/book-critique/{id}/delete', [Backend\ProjectController::class, 'deleteBookCritique']);
        Route::post('/project/book-critique/{id}/feedback', [Backend\ProjectController::class, 'saveBookCritiqueFeedback']);
        Route::post('/project/{id}/book/save', [Backend\ProjectController::class, 'saveBook']);
        Route::delete('/project/book/{id}/delete', [Backend\ProjectController::class, 'deleteBook']);
        Route::post('/project/{id}/book-pictures/save', [Backend\ProjectController::class, 'saveBookPicture'])->name('admin.project.save-picture');
        Route::delete('/project/book-pictures/{id}/delete', [Backend\ProjectController::class, 'deleteBookPicture'])->name('admin.project.delete-picture');
        Route::post('/project/{id}/book-formatting/save', [Backend\ProjectController::class, 'saveBookFormatting'])->name('admin.project.save-book-formatting');
        Route::post('/project/book-formatting/{id}/approve-feedback', [Backend\ProjectController::class, 'approveBookFormattingFeedback'])
            ->name('admin.project.book-formatting.approve-feedback');
        Route::delete('/project/book-formatting/{id}/delete', [Backend\ProjectController::class, 'deleteBookFormatting'])->name('admin.project.delete-book-formatting');
        Route::post('/project/{id}/add-other-service', [Backend\ProjectController::class, 'addOtherService'])->name('admin.project.add-other-service');
        Route::get('/project/{id}/graphic-work', [Backend\ProjectController::class, 'graphicWork'])->name('admin.project.graphic-work');
        Route::post('/project/{id}/graphic-work/save', [Backend\ProjectController::class, 'saveGraphicWork'])->name('admin.project.save-graphic-work');
        Route::delete('/project/{id}/graphic-work/{graphic_work_id}/delete', [Backend\ProjectController::class, 'deleteGraphicWork'])->name('admin.project.delete-graphic-work');
        Route::get('/project/{id}/cover/{cover_id}', [Backend\ProjectController::class, 'cover'])->name('admin.project.cover.show');
        Route::get('/project/{id}/book-format/{format_id}', [Backend\ProjectController::class, 'bookFormat'])->name('admin.project.book-format.show');
        Route::get('/project/{id}/registration', [Backend\ProjectController::class, 'registration'])->name('admin.project.registration');
        Route::post('/project/{id}/registration/save', [Backend\ProjectController::class, 'saveRegistration'])->name('admin.project.save-registration');
        Route::delete('/project/{id}/registration/{registration_id}/delete', [Backend\ProjectController::class, 'deleteRegistration'])->name('admin.project.delete-registration');
        Route::get('/project/{id}/marketing', [Backend\ProjectController::class, 'marketing'])->name('admin.project.marketing');
        Route::post('/project/{id}/marketing/save', [Backend\ProjectController::class, 'saveMarketing'])->name('admin.project.save-marketing');
        Route::delete('/project/{id}/marketing/{marketing_id}/delete', [Backend\ProjectController::class, 'deleteMarketing'])->name('admin.project.delete-marketing');
        Route::get('/project/{id}/marketing-plan', [Backend\ProjectController::class, 'marketingPlan'])->name('admin.project.marketing-plan');
        Route::get('/project/{id}/progress-plan', [Backend\ProjectController::class, 'progressPlan'])->name('admin.project.progress-plan');
        Route::post('/project/progress-plan/save', [Backend\ProjectController::class, 'progressPlanSave'])->name('admin.project.progress-plan.save');
        Route::get('/project/{id}/progress-plan/{step}', [Backend\ProjectController::class, 'progressPlanStep'])->name('admin.project.progress-plan-step');
        Route::get('/project/{id}/contract', [Backend\ProjectController::class, 'contract'])->name('admin.project.contract');
        Route::post('/project/{id}/contract', [Backend\ProjectController::class, 'storeContract'])->name('admin.project.contract-store');
        Route::post('/project/{id}/contract/upload', [Backend\ProjectController::class, 'uploadContract'])->name('admin.project.contract-upload');
        Route::post('/project/{id}/contract/{contract_id}/signed-upload', [Backend\ProjectController::class, 'uploadSignedContract'])
            ->name('admin.project.contract-signed-upload');
        Route::get('/project/{id}/contract/create', [Backend\ProjectController::class, 'createContract'])->name('admin.project.contract-create');
        Route::get('/project/{id}/contract/{contract_id}/edit', [Backend\ProjectController::class, 'editContract'])->name('admin.project.contract-edit');
        Route::put('/project/{id}/contract/{contract_id}/update', [Backend\ProjectController::class, 'updateContract'])->name('admin.project.contract-update');
        Route::get('/project/{id}/contract/{contract_id}', [Backend\ProjectController::class, 'showContract'])->name('admin.project.contract-show');
        Route::get('/project/{id}/invoice', [Backend\ProjectController::class, 'invoice'])->name('admin.project.invoice');
        Route::post('/project/{id}/invoice/save', [Backend\ProjectController::class, 'saveInvoice'])->name('admin.project.invoice.save');
        Route::delete('/project/{id}/invoice/{invoice_id}/delete', [Backend\ProjectController::class, 'deleteInvoice'])->name('admin.project.invoice.delete');
        Route::post('/project/{id}/manual-invoice/save', [Backend\ProjectController::class, 'saveManualInvoice'])->name('admin.project.manual-invoice.save');
        Route::delete('/project/{id}/manual-invoice/{invoice_id}/delete', [Backend\ProjectController::class, 'deleteManualInvoice'])->name('admin.project.manual-invoice.delete');
        Route::get('/project/{id}/storage', [Backend\ProjectController::class, 'storage'])->name('admin.project.storage');
        Route::post('/project/{id}/storage', [Backend\ProjectController::class, 'storage']);
        Route::get('/project/{id}/storage/{registration_id}/details', [Backend\ProjectController::class, 'storageDetails'])
            ->name('admin.project.storage-details');
        Route::post('/project/{id}/storage/save-book', [Backend\ProjectController::class, 'saveStorageBook'])->name('admin.project.storage.save-book');
        Route::delete('/project/{id}/storage/delete', [Backend\ProjectController::class, 'deleteStorageBook'])->name('admin.project.storage.delete-book');
        Route::post('project/{id}/storage/save-book-sales', [Backend\ProjectController::class, 'saveBookSales'])->name('admin.project.storage.save-book-sales');
        Route::post('project/{id}/storage/import-book-sales', [Backend\ProjectController::class, 'importBookSales'])
            ->name('admin.project.storage.import-book-sales');
        Route::delete('project/storage/book-sales/{book_id}/delete', [Backend\ProjectController::class, 'deleteBookSales'])->name('admin.project.storage.delete-book-sales');
        Route::post('/project/book/{id}/storage/save-details', [Backend\ProjectController::class, 'saveStorageBookDetails'])->name('admin.project.storage.save-details');
        Route::post('project/registration/{id}/paid-year', [Backend\ProjectController::class, 'saveRegistrationPaidDistribution']);
        Route::get('project/{project_id}/registration/{registration_id}/storage-cost/{year}/export', [Backend\ProjectController::class, 'exportStorageCost'])
            ->name('admin.project.storage-cost.export');
        Route::get('project/{project_id}/registration/{registration_id}/storage-cost/{year}/export-excel',
            [Backend\ProjectController::class, 'excelExportStorageCost'])
            ->name('admin.project.storage-cost.export-excel');
        Route::post('project/{project_id}/registration/{registration_id}/storage-cost/{year}/send-email',
            [Backend\ProjectController::class, 'storageCostSendEmail'])
            ->name('admin.project.storage-cost.send');
        Route::post('/project/book/{id}/storage/save-various', [Backend\ProjectController::class, 'saveStorageVarious'])->name('admin.project.storage.save-various');
        Route::post('/project/book/{id}/storage/save-distribution-cost', [Backend\ProjectController::class, 'saveDistributionCost'])
            ->name('admin.project.storage.save-distribution-cost');
        Route::get('/project/book/{id}/storage/sales-details', [Backend\ProjectController::class, 'storageSalesDetails']);
        Route::delete('/project/book/distribution-cost/{dist_id}/delete', [Backend\ProjectController::class, 'deleteDistributionCost'])
            ->name('admin.project.storage.delete-distribution-cost');
        Route::post('/project/book/{id}/storage/save-sales', [Backend\ProjectController::class, 'saveStorageSales'])->name('admin.project.storage.save-sales');
        Route::delete('/project/storage/{id}/delete-sales', [Backend\ProjectController::class, 'deleteStorageSales'])->name('admin.project.storage.delete-sales');
        Route::get('/project/{id}/e-book', [Backend\ProjectController::class, 'ebook'])->name('admin.project.ebook');
        Route::post('/project/{id}/e-book/save', [Backend\ProjectController::class, 'saveEbook'])->name('admin.project.save-ebook');
        Route::delete('/project/{id}/e-book/{ebook_id}/delete', [Backend\ProjectController::class, 'deleteEbook'])->name('admin.project.delete-ebook');
        Route::get('/project/{id}/audio', [Backend\ProjectController::class, 'audio'])->name('admin.project.audio');
        Route::post('/project/{id}/audio/save', [Backend\ProjectController::class, 'saveAudio'])->name('admin.project.save-audio');
        Route::delete('/project/{id}/audio/{audio_id}/delete', [Backend\ProjectController::class, 'deleteAudio'])->name('admin.project.delete-audio');
        Route::get('/project/{id}/print', [Backend\ProjectController::class, 'print'])->name('admin.project.print');
        Route::post('/project/{id}/print/save', [Backend\ProjectController::class, 'savePrint'])->name('admin.project.save-print');
        Route::get('/project/{id}/notes', [Backend\ProjectController::class, 'showNotes'])->name('admin.project.notes');
        Route::get('/project', [Backend\ProjectController::class, 'index'])->name('admin.project.index');
        Route::post('/project/save', [Backend\ProjectController::class, 'saveProject']);
        Route::get('/project/{id}', [Backend\ProjectController::class, 'show'])->name('admin.project.show');
        Route::delete('/project/{id}/delete', [Backend\ProjectController::class, 'deleteProject']);
        Route::get('/project/book/generate', [Backend\ProjectController::class, 'generateProjectBook']);
        Route::post('/project/quarterly-payout/store', [Backend\ProjectController::class, 'storePayout'])->name('admin.quarterly-payouts.store');

        Route::get('/storage-books', [Backend\StorageBookController::class, 'index'])->name('admin.storage-books.index');

        Route::post('generate_assignment_group/{id}', [Backend\AssignmentController::class, 'generateGroup'])->name('assignment.generate_assignment_group');
        Route::post('assignment/{id}/uploadManuscript', [Backend\AssignmentController::class, 'uploadManuscript'])->name('assignment.group.upload_manuscript');
        Route::post('assignment/{id}/add-on-for-learner', [Backend\AssignmentController::class, 'addOnForLearner'])->name('assignment.add-on-for-learner');
        Route::post('assignment/{id}/update-submission-date', [Backend\AssignmentController::class, 'updateSubmissionDate'])->name('assignment.update-submission-date');
        Route::post('assignment/{id}/update-available-date', [Backend\AssignmentController::class, 'updateAvailableDate'])->name('assignment.update-available-date');
        Route::post('assignment/{id}/update-max-words', [Backend\AssignmentController::class, 'updateMaxWords'])->name('assignment.update-max-words');
        Route::post('assignment_manuscript/{id}/delete', [Backend\AssignmentController::class, 'deleteManuscript'])->name('assignment.group.delete_manuscript');
        Route::post('assignment_manuscript/{id}/move', [Backend\AssignmentController::class, 'moveManuscript'])->name('assignment.group.move_manuscript');
        Route::post('assignment_manuscript/{id}/set_grade', [Backend\AssignmentController::class, 'setGrade'])->name('assignment.group.set_grade');
        Route::post('assignment_manuscript/{id}/replace_manuscript', [Backend\AssignmentController::class, 'replaceManuscript'])->name('assignment.group.replace_manuscript');
        Route::post('assignment_manuscript/lock-status', [Backend\AssignmentController::class, 'updateLockStatus'])->name('assignment.group.lock-status'); // Courses For Sale Status
        Route::post('assignment_manuscript/dashboard-status', [Backend\AssignmentController::class, 'updateDashboardStatus'])->name('assignment.group.dashboard-status'); // Courses For Sale Status
        Route::post('assignment_manuscript/{id}/update_manu_types', [Backend\AssignmentController::class, 'updateTypes'])->name('assignment.group.update_manu_types');
        Route::post('assignment_manuscript/{id}/assignEditor', [Backend\AssignmentController::class, 'assignManuscriptEditor'])->name('assignment.group.assign_manu_editor');
        Route::delete('assignment_manuscript/{id}/remove-editor', [Backend\AssignmentController::class, 'removeManuscriptEditor'])->name('assignment.group.remove_manu_editor');
        Route::post('assignment_manuscript/{id}/edit-dates', [Backend\AssignmentController::class, 'assignManuscriptEditDates'])->name('backend.assignment.edit-dates');
        Route::post('assignment_manuscript/{id}/download_editor_manuscript', [Backend\AssignmentController::class, 'downloadEditorManuscript'])->name('assignment.group.download_editor_manuscript');
        Route::post('assignment_manuscript/{id}/learner/{learner_id}/feedback', [Backend\AssignmentController::class, 'manuscriptFeedbackNoGroup'])->name('assignment.group.manuscript-feedback-no-group');
        Route::post('assignment_manuscript/{id}/send-email-to-user', [Backend\AssignmentController::class, 'emailManuscriptUser'])->name('assignment.send-email-to-manuscript-user');
        Route::post('assignment_manuscript/update-feedback/{id}', [Backend\AssignmentController::class, 'manuscriptFeedbackNoGroupUpdate'])->name('assignment.group.manuscript-feedback-no-group-update');
        Route::post('assignment_manuscript/update-availability/{id}', [Backend\AssignmentController::class, 'manuscriptFeedbackNoGroupUpdateAvailability'])->name('assignment.group.manuscript-feedback-no-group-update-availability');
        Route::post('assignment_manuscript/update-join-group/{id}', [Backend\AssignmentController::class, 'updateJoinGroup'])->name('assignment.update-join-group');
        Route::get('assignment/{id}/download', [Backend\AssignmentController::class, 'downloadManuscript'])->name('assignment.group.download_manuscript');
        Route::get('assignment-manuscript/{id}/download-letter', [Backend\AssignmentController::class, 'downloadManuscriptLetter'])->name('assignment.manuscript.download_letter');
        Route::get('assignment/{id}/downloadAll', [Backend\AssignmentController::class, 'downloadAllManuscript'])->name('assignment.group.download_all_manuscript');
        Route::get('assignment/{id}/exportEmailList', [Backend\AssignmentController::class, 'exportEmailList'])->name('assignment.group.export_email_list');
        Route::get('assignment/{id}/export-all-learners-include-add-on-learners', [Backend\AssignmentController::class, 'exportLearnersIncludeAddOnLearners'])
            ->name('assignment.export-all-learners-include-add-on-learners');
        Route::post('assignment/{id}/send-email-to-list', [Backend\AssignmentController::class, 'sendEmailToList'])->name('assignment.group.send-email-to-list');
        Route::get('assignment/{id}/generate-doc', [Backend\AssignmentController::class, 'generateDoc'])->name('assignment.group.generate-doc');
        Route::get('assignment/{id}/download-generate-doc', [Backend\AssignmentController::class, 'downloadGenerateDoc'])->name('assignment.group.download-generate-doc');
        Route::get('assignment/{id}/download-excel-sheet', [Backend\AssignmentController::class, 'downloadExcelSheet'])->name('assignment.group.download-excel-sheet');
        Route::post('assignment/{id}/assign-editor', [Backend\AssignmentController::class, 'assignEditor'])->name('assignment.assign_editor');
        Route::delete('assignment/{id}/remove-editor', [Backend\AssignmentController::class, 'removeEditor'])->name('assignment.remove_editor');
        Route::post('assignment/template/save/{id?}', [Backend\AssignmentController::class, 'saveAssignmentTemplate'])->name('assignment.template.save');
        Route::delete('assignment/template/delete/{id}', [Backend\AssignmentController::class, 'deleteAssignmentTemplate'])->name('assignment.template.delete');
        Route::post('assignment/learner-assignment/save/{id?}', [Backend\AssignmentController::class, 'learnerAssignment'])->name('assignment.learner-assignment.save');
        Route::post('assignment/{id}/disabled-learner-assignment/save', [Backend\AssignmentController::class, 'disabledLearnerAssignment'])
            ->name('assignment.disable-learner-assignment.save');
        Route::delete('assignment/learner-assignment/{id}/delete', [Backend\AssignmentController::class, 'deleteLearnerAssignment'])->name('assignment.learner-assignment.delete');
        Route::post('assignment/multiple-learner-assignment/save', [Backend\AssignmentController::class, 'multipleLearnerAssignment'])->name('assignment.multiple-learner-assignment.save');
        Route::get('/assignment/{assignment_id}/course/{course_id}/assignment-with-course-learners',
            [Backend\AssignmentController::class, 'assignmentWithCourseLearner']);
        Route::post('/assignment/{id}/disable-learner', [Backend\AssignmentController::class, 'disableLearner']);

        // Assignment Groups Route
        Route::resource('course/{course_id}/assignment/{assignment_id}/group', Backend\AssignmentGroupController::class, [
            'names' => [
                'show' => 'admin.assignment-group.show',
                'store' => 'admin.assignment-group.store',
                'update' => 'admin.assignment-group.update',
                'destroy' => 'admin.assignment-group.destroy',
            ],
        ])->except('index', 'create', 'edit');
        Route::post('course/{course_id}/assignment/{assignment_id}/group/{id}/add_learner', [Backend\AssignmentGroupController::class, 'add_learner'])->name('assignment.group.add_learner');
        Route::post('course/{course_id}/assignment/{assignment_id}/group/{group_id}/remove_learner/{id}', [Backend\AssignmentGroupController::class, 'remove_learner'])->name('assignment.group.remove_learner');
        Route::get('course/{course_id}/assignment/{assignment_id}/group/{group_id}/download_all', [Backend\AssignmentGroupController::class, 'downloadAll'])->name('assignment.group.download_all');
        Route::post('course/{course_id}/assignment/{assignment_id}/group/{group_id}/feedback-availability', [Backend\AssignmentGroupController::class, 'setGroupFeedbackAvailability'])->name('assignment.group.feedback-availability');
        Route::post('/group/{group_id}/learner/{id}/submit_feedback', [Backend\AssignmentGroupController::class, 'submit_feedback'])->name('admin.assignment.group.submit_feedback'); // Submit assignment feedback
        Route::post('/group/{group_id}/learner/{id}/submit_feedback_learner', [Backend\AssignmentGroupController::class, 'submit_feedback_learner'])->name('admin.assignment.group.submit_feedback_learner'); // Submit assignment feedback
        Route::post('/assignment-group/{group_id}/set-feedback-to-other-learners/{group_learner_id}',
            [Backend\AssignmentGroupController::class, 'setFeedbackToOtherLearner'])->name('admin.assignment.group.learner.set-feedback-to-other');
        Route::post('/feedback/{id}/remove_feedback', [Backend\AssignmentGroupController::class, 'remove_feedback'])->name('admin.assignment.group.remove_feedback'); // Remove assignment feedback
        Route::post('/feedback/{id}/update_feedback', [Backend\AssignmentGroupController::class, 'update_feedback'])->name('admin.assignment.group.update_feedback'); // Update assignment feedback
        Route::post('/feedback/{id}/update_feedback_admin', [Backend\AssignmentGroupController::class, 'update_feedback_admin'])->name('admin.assignment.group.update_feedback_admin'); // Update assignment feedback admin
        Route::post('/feedback/{id}/approve', [Backend\AssignmentGroupController::class, 'approve'])->name('admin.assignment.group.approve'); // Approve assignment feedback admin
        Route::post('/feedback/lock-status', [Backend\AssignmentGroupController::class, 'updateFeedbackLockStatus'])->name('learner.assignment.group.lock-status'); // Courses For Sale Status
        Route::get('/feedback/{id}/download', [Backend\AssignmentGroupController::class, 'downloadFeedback'])->name('assignment.feedback.download_manuscript');
        Route::get('/assignment-group/{group_id}/get-feedback-to-other-learners/{group_learner_id}', [Backend\AssignmentGroupController::class, 'getFeedbackToOtherLearner'])
            ->name('learner.assignment.group.get-feedback-to-other-learners');

        // Manuscripts Route
        Route::resource('/manuscript', Backend\ManuscriptController::class, [
            'names' => [
                'index' => 'admin.manuscript.index',
                'store' => 'admin.manuscript.store',
                'show' => 'admin.manuscript.show',
                'update' => 'admin.manuscript.update',
                'destroy' => 'admin.manuscript.destroy',
            ],
        ])->except('edit', 'create');
        Route::post('/manuscript/{id}', [Backend\ManuscriptController::class, 'addFeedback'])->name('admin.feedback.store'); // Store Feedback
        Route::post('/feedback/{id}/delete', [Backend\ManuscriptController::class, 'destroyFeedback'])->name('admin.feedback.destroy'); // Delete Feedback
        Route::post('/manuscript/{id}/email', [Backend\ManuscriptController::class, 'sendEmail'])->name('admin.manuscript.email'); // Send email
        Route::get('email-template', [Backend\EmailTemplateController::class, 'index'])->name('admin.email-template.index');
        Route::post('/email_template/add_email_template', [Backend\EmailTemplateController::class, 'addEmailTemplate'])->name('admin.manuscript.add_email_template'); // Store Email Template
        Route::put('/email_template/edit_email_template/{id}', [Backend\EmailTemplateController::class, 'editEmailTemplate'])->name('admin.manuscript.edit_email_template'); // Update Email Template
        Route::post('/email_template/courseEditAdd/{course_id}', [Backend\EmailTemplateController::class, 'courseEditAdd'])->name('admin.email_template.courseEditAdd'); // Update Email Template

        Route::prefix('sale')->group(function () {

            Route::get('/', [Backend\SaleController::class, 'index'])->name('admin.sales.index');
            Route::get('/load-tab-content', [Backend\SaleController::class, 'loadTabContent']);
            Route::post('/send-email/{id}/{parent}', [Backend\SaleController::class, 'sendEmail'])
                ->name('admin.sales.send-email');
            Route::get('/move-to-archive/{id}', [Backend\SaleController::class, 'moveToArchive'])->name('admin.sales.move-to-archive');
            Route::post('/is-invoice-sent', [Backend\SaleController::class, 'orderInvoiceSent']);
            Route::post('/is-order-withdrawn', [Backend\SaleController::class, 'isOrderWithdrawn']);
            Route::get('/add-to-po/{order_id}', [Backend\SaleController::class, 'addToPowerOffice'])->name('admin.sale.add-to-po');

        });

        Route::get('/email-history', [Backend\EmailHistoryController::class, 'index'])->name('admin.email-history.index');
        Route::resource('/replays', Backend\ReplayController::class, [
            'names' => [
                'index' => 'admin.replay.index',
                'store' => 'admin.replay.store',
                'update' => 'admin.replay.update',
                'destroy' => 'admin.replay.delete',
            ],
        ])->except('show');

        Route::get('/checkout-log', [Backend\CheckoutLogController::class, 'index'])->name('admin.checkout-log.index');

        Route::prefix('upcoming')->group(function () {
            Route::get('/', [Backend\UpcomingController::class, 'index'])->name('admin.upcoming.index');
            Route::post('/{id}/save', [Backend\UpcomingController::class, 'saveSection'])->name('admin.upcoming.save');
        });

        Route::resource('/publishing', Backend\PublishingController::class, [
            'names' => [
                'index' => 'admin.publishing.index',
                'create' => 'admin.publishing.create',
                'store' => 'admin.publishing.store',
                'edit' => 'admin.publishing.edit',
                'update' => 'admin.publishing.update',
                'destroy' => 'admin.publishing.destroy',
            ],
        ])->except('show');

        // FAQ Route
        Route::resource('/faq', Backend\FaqController::class, [
            'names' => [
                'index' => 'admin.faq.index',
                'store' => 'admin.faq.store',
                'update' => 'admin.faq.update',
                'destroy' => 'admin.faq.destroy',
            ],
        ])->only('index', 'store', 'update', 'destroy');

        Route::resource('/competition', Backend\CompetitionController::class, [
            'names' => [
                'index' => 'admin.competition.index',
                'store' => 'admin.competition.store',
                'update' => 'admin.competition.update',
                'destroy' => 'admin.competition.destroy',
            ],
        ])->only('index', 'store', 'update', 'destroy');

        Route::resource('/writing-group', Backend\WritingGroupController::class, [
            'names' => [
                'index' => 'admin.writing-group.index',
                'create' => 'admin.writing-group.create',
                'store' => 'admin.writing-group.store',
                'edit' => 'admin.writing-group.edit',
                'update' => 'admin.writing-group.update',
                'destroy' => 'admin.writing-group.destroy',
            ],
        ])->except('show');

        Route::resource('/solution', Backend\SolutionController::class, [
            'names' => [
                'index' => 'admin.solution.index',
                'create' => 'admin.solution.create',
                'store' => 'admin.solution.store',
                'edit' => 'admin.solution.edit',
                'update' => 'admin.solution.update',
                'destroy' => 'admin.solution.destroy',
            ],
        ])->except('show');

        Route::resource('/sos-children', Backend\SosChildrenController::class, [
            'names' => [
                'index' => 'admin.sos-children.index',
                'create' => 'admin.sos-children.create',
                'store' => 'admin.sos-children.store',
                'edit' => 'admin.sos-children.edit',
                'update' => 'admin.sos-children.update',
                'destroy' => 'admin.sos-children.destroy',
            ],
        ])->except('show');
        Route::get('/sos-children/edit-main-description', [Backend\SosChildrenController::class, 'getEditMainDescription'])
            ->name('admin.sos-children.get-main-description');
        Route::post('/sos-children/edit-main-description', [Backend\SosChildrenController::class, 'editMainDescription'])
            ->name('admin.sos-children.post-main-description');

        Route::put('/blog/status-update/{id}', [Backend\BlogController::class, 'statusUpdate'])
            ->name('admin.sos-children.main-description');
        Route::resource('/blog', Backend\BlogController::class, [
            'names' => [
                'index' => 'admin.blog.index',
                'create' => 'admin.blog.create',
                'store' => 'admin.blog.store',
                'edit' => 'admin.blog.edit',
                'update' => 'admin.blog.update',
                'destroy' => 'admin.blog.destroy',
            ],
        ])->except('show');

        Route::resource('/publisher-book', Backend\PublisherBookController::class, [
            'names' => [
                'index' => 'admin.publisher-book.index',
                'create' => 'admin.publisher-book.create',
                'store' => 'admin.publisher-book.store',
                'edit' => 'admin.publisher-book.edit',
                'update' => 'admin.publisher-book.update',
                'destroy' => 'admin.publisher-book.destroy',
            ],
        ])->except('show');

        Route::prefix('/publisher-book-library')->group(function () {
            Route::post('/{book_id}/store', [Backend\PublisherBookController::class, 'storeLibrary'])->name('publisher-book-library.store');
            Route::put('/{id}/update', [Backend\PublisherBookController::class, 'updateLibrary'])->name('publisher-book-library.update');
            Route::delete('/{id}/delete', [Backend\PublisherBookController::class, 'deleteLibrary'])->name('publisher-book-library.delete');
        });

        Route::resource('/opt-in', Backend\OptInController::class, [
            'names' => [
                'index' => 'admin.opt-in.index',
                'create' => 'admin.opt-in.create',
                'store' => 'admin.opt-in.store',
                'edit' => 'admin.opt-in.edit',
                'update' => 'admin.opt-in.update',
                'destroy' => 'admin.opt-in.destroy',
            ],
        ])->except('show');

        Route::resource('/poem', Backend\PoemController::class, [
            'names' => [
                'index' => 'admin.poem.index',
                'create' => 'admin.poem.create',
                'store' => 'admin.poem.store',
                'edit' => 'admin.poem.edit',
                'update' => 'admin.poem.update',
                'destroy' => 'admin.poem.destroy',
            ],
        ])->except('show');

        Route::resource('/solution/{solution_id}/article', Backend\SolutionArticleController::class, [
            'names' => [
                'index' => 'admin.solution-article.index',
                'create' => 'admin.solution-article.create',
                'store' => 'admin.solution-article.store',
                'edit' => 'admin.solution-article.edit',
                'update' => 'admin.solution-article.update',
                'destroy' => 'admin.solution-article.destroy',
            ],
        ])->except('show');

        Route::get('/free-manuscript', [Backend\FreeManuscriptController::class, 'index'])->name('admin.free-manuscript.index');
        Route::post('/free-manuscript/{id}/delete', [Backend\FreeManuscriptController::class, 'deleteFreeManuscript'])->name('admin.free-manuscript.delete');
        Route::post('/free-manuscript/{id}/edit-content', [Backend\FreeManuscriptController::class, 'editContent'])->name('admin.free-manuscript.edit-content');
        Route::post('/free-manuscript/{id}/assign_editor', [Backend\FreeManuscriptController::class, 'assignEditor'])->name('admin.free-manuscript.assign_editor');
        Route::post('/free-manuscript/{id}/send_feedback', [Backend\FreeManuscriptController::class, 'sendFeedback'])->name('admin.free-manuscript.send_feedback');
        Route::get('/free-manuscript/{id}/feedback-history', [Backend\FreeManuscriptController::class, 'feedbackHistory'])->name('admin.free-manuscript.feedback-history');
        Route::get('/free-manuscript/{id}/download', [Backend\FreeManuscriptController::class, 'downloadContent'])->name('admin.free-manuscript.download');
        Route::post('/free-manuscript/{id}/resend-feedback', [Backend\FreeManuscriptController::class, 'resendFeedback'])->name('admin.free-manuscript.resend-feedback');
        Route::post('/free-manuscript/{id}/approve_feedback', [Backend\FreeManuscriptController::class, 'approveFeedback'])->name('head_editor.free-manuscript.feedback_approve');

        Route::resource('/other-service', Backend\OtherServiceController::class, [
            'names' => [
                'index' => 'admin.other-service.index',
                'create' => 'admin.other-service.create',
                'store' => 'admin.other-service.store',
                'edit' => 'admin.other-service.edit',
                'update' => 'admin.other-service.update',
                'destroy' => 'admin.other-service.destroy',
            ],
        ])->except('show');

        Route::post('/other-service/{id}/coaching-timer/approve_date', [Backend\OtherServiceController::class, 'approveDate'])->name('admin.other-service.coaching-timer.approve_date');
        Route::post('/other-service/{id}/coaching-timer/set-approve-date', [Backend\OtherServiceController::class, 'setCoachingApproveDate'])
            ->name('admin.other-service.coaching-timer.set-coaching-approve-date');
        Route::post('/other-service/{id}/coaching-timer/suggest_date', [Backend\OtherServiceController::class, 'suggestDate'])->name('admin.other-service.coaching-timer.suggestDate');
        Route::post('/other-service/set-approved-date', [Backend\OtherServiceController::class, 'setApprovedDate'])->name('admin.other-service.coaching-timer.set-approved-date');
        Route::post('/other-service/{id}/coaching-timer/set_replay', [Backend\OtherServiceController::class, 'setReplay'])->name('admin.other-service.coaching-timer.set_replay');
        Route::post('/other-service/{id}/coaching-timer/mark_as_finished', [Backend\OtherServiceController::class, 'markAsFinished'])->name('admin.other-service.coaching-timer.mark_as_finished');
        Route::post('/other-service/{id}/update-status/{type}', [Backend\OtherServiceController::class, 'updateStatus'])->name('admin.other-service.update-status');
        Route::post('/other-service/{id}/lock-status/{type}', [Backend\OtherServiceController::class, 'updateLocked'])->name('admin.other-service.update-locked');
        Route::post('/other-service/{id}/update-expected-finish/{type}', [Backend\OtherServiceController::class, 'updateExpectedFinish'])->name('admin.other-service.update-expected-finish');
        Route::get('/other-service/{id}/download/{type}', [Backend\OtherServiceController::class, 'downloadOtherServiceDoc'])->name('admin.other-service.download-doc'); // Download assignment feedback
        Route::post('/other-service/{id}/add-feedback/{type}', [Backend\OtherServiceController::class, 'addFeedback'])->name('admin.other-service.add-feedback');
        Route::get('/other-service/{id}/download-feedback/{type}', [Backend\OtherServiceController::class, 'downloadFeedback'])->name('admin.other-service.download-feedback');
        Route::delete('/other-service/{id}/coaching-timer/delete', [Backend\OtherServiceController::class, 'deleteCoaching'])->name('admin.other-service.coaching-timer.delete');

        // Shop Manuscripts Route
        Route::resource('/shop-manuscript', Backend\ShopManuscriptController::class, [
            'names' => [
                'index' => 'admin.shop-manuscript.index',
                'store' => 'admin.shop-manuscript.store',
                'update' => 'admin.shop-manuscript.update',
                'destroy' => 'admin.shop-manuscript.destroy',
            ],
        ])->except('edit', 'create', 'show');
        Route::post('/shop-manuscript-taken/{id}/assign_editor', [Backend\ShopManuscriptController::class, 'updateTaken'])->name('admin.shop-manuscript-taken.update_taken'); // Assign editor
        Route::post('/shop-manuscript-taken/{id}/add-feedback', [Backend\ShopManuscriptController::class, 'addFeedback'])->name('admin.shop-manuscript-taken-feedback.store'); // Store Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{id}/delete', [Backend\ShopManuscriptController::class, 'destroyFeedback'])->name('admin.shop-manuscript-taken-feedback.delete'); // Remove Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{id}/update-genre', [Backend\ShopManuscriptController::class, 'updateGenre'])->name('admin.shop-manuscript-taken.update-genre'); // Remove Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{id}/update-coaching-time-later', [Backend\ShopManuscriptController::class, 'updateCoachingTimeLater'])
            ->name('admin.shop-manuscript-taken.update-coaching-time-later');
        Route::post('/shop-manuscript-taken/{id}/update-description', [Backend\ShopManuscriptController::class, 'updateDescription'])->name('admin.shop-manuscript-taken.update-description'); // Remove Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{feedback_id}/approve-feedback', [Backend\ShopManuscriptController::class, 'approveFeedback'])->name('admin.shop-manuscript-taken.approve-feedback');

        Route::get('/test', [Backend\ShopManuscriptController::class, 'testEmail']);

        // Invoices Route
        Route::post('/invoice/create-new', [Backend\InvoiceController::class, 'addInvoice'])->name('admin.invoice.new');
        Route::resource('/invoice', Backend\InvoiceController::class, [
            'names' => [
                'index' => 'admin.invoice.index',
                'show' => 'admin.invoice.show',
                'store' => 'admin.invoice.store',
                'update' => 'admin.invoice.update',
                'destroy' => 'admin.invoice.destroy',
            ],
        ])->except('create', 'edit');
        Route::post('/invoice/{id}', [Backend\InvoiceController::class, 'addTransaction'])->name('admin.transaction.store'); // Store Transaction
        Route::post('/invoice/{invoice_id}/transaction/{transaction_id}', [Backend\InvoiceController::class, 'updateTransaction'])->name('admin.transaction.update'); // Update Transaction
        Route::post('/invoice/{invoice_id}/transaction/{transaction_id}/delete', [Backend\InvoiceController::class, 'destroyTransaction'])->name('admin.transaction.destroy'); // Delete Transaction
        Route::get('/invoice/{id}/download-fiken', [Backend\InvoiceController::class, 'downloadFikenPdf'])->name('admin.invoice.download-fiken-pdf'); // Store Transaction

        Route::get('/yearly_calendar', [Backend\AdminController::class, 'yearlyCalendar'])->name('admin.yearly-calendar.index');

        // Package shop manuscripts route
        Route::resource('package_shop_manuscript/{id}', Backend\PackageShopManuscriptController::class, [
            'names' => [
                'store' => 'admin.package_shop_manuscript.store',
            ],
        ])->except('index', 'show', 'edit', 'create', 'update', 'destroy');
        Route::post('package_shop_manuscript/{id}/remove', [Backend\PackageShopManuscriptController::class, 'delete'])->name('admin.package_shop_manuscript.destroy');

        // Package workshops route
        Route::post('package_workshop/{id}/approve', [Backend\PackageWorkshopController::class, 'approve'])->name('admin.package_workshop.approve');
        Route::post('package_workshop/{id}/disapprove', [Backend\PackageWorkshopController::class, 'disapprove'])->name('admin.package_workshop.disapprove');

        // Editors route
        Route::resource('/editor', Backend\EditorController::class, [
            'names' => [
                'index' => 'admin.editor.index',
                'create' => 'admin.editor.create',
                'store' => 'admin.editor.store',
                'edit' => 'admin.editor.edit',
                'update' => 'admin.editor.update',
                'destroy' => 'admin.editor.destroy',
            ],
        ])->except('show');

        Route::view('cron-log', 'backend.support.cron-log')->name('admin.cron-log.index');

        // Goto-webinar route
        Route::resource('/goto-webinar', Backend\GotoWebinarController::class, [
            'names' => [
                'index' => 'admin.goto-webinar.index',
                'create' => 'admin.goto-webinar.create',
                'store' => 'admin.goto-webinar.store',
                'edit' => 'admin.goto-webinar.edit',
                'update' => 'admin.goto-webinar.update',
                'destroy' => 'admin.goto-webinar.destroy',
            ],
        ])->except('show');

        // testimonial routes
        Route::resource('/testimonial', Backend\TestimonialController::class, [
            'names' => [
                'index' => 'admin.testimonial.index',
                'create' => 'admin.testimonial.create',
                'store' => 'admin.testimonial.store',
                'edit' => 'admin.testimonial.edit',
                'update' => 'admin.testimonial.update',
                'destroy' => 'admin.testimonial.destroy',
            ],
        ])->except('show');

        // testimonial routes
        Route::resource('/file', Backend\FilesController::class, [
            'names' => [
                'index' => 'admin.file.index',
                'store' => 'admin.file.store',
                'update' => 'admin.file.update',
                'destroy' => 'admin.file.destroy',
            ],
        ])->except('show', 'edit', 'create');

        Route::get('/personal-trainer/export', [Backend\PersonalTrainerController::class, 'export']);
        Route::resource('/personal-trainer', Backend\PersonalTrainerController::class, [
            'names' => [
                'index' => 'admin.personal-trainer.index',
                'show' => 'admin.personal-trainer.show',
                'create' => 'admin.personal-trainer.create',
                'store' => 'admin.personal-trainer.store',
                'destroy' => 'admin.personal-trainer.destroy',
            ],
        ])->except('edit');

        Route::get('/single-competition', [Backend\PageController::class, 'singleCompetition'])
            ->name('admin.single-competition.index');
        Route::get('/single-competition/{id}', [Backend\PageController::class, 'singleCompetitionShow'])
            ->name('admin.single-competition.show');
        Route::post('/single-competition', [Backend\PageController::class, 'singleCompetitionStore'])
            ->name('admin.single-competition.store');
        Route::put('/single-competition/{id}', [Backend\PageController::class, 'singleCompetitionUpdate'])
            ->name('admin.single-competition.update');
        Route::delete('/single-competition/{id}', [Backend\PageController::class, 'singleCompetitionDelete'])
            ->name('admin.single-competition.delete');
        Route::delete('/single-competition/{id}/manuscript', [Backend\PageController::class, 'singleCompetitionDeleteManuscript'])
            ->name('admin.single-competition.delete-manuscript');

        // Calendar Notes
        Route::resource('/calendar-note', Backend\CalendarNoteController::class, [
            'names' => [
                'index' => 'admin.calendar-note.index',
                'create' => 'admin.calendar-note.create',
                'store' => 'admin.calendar-note.store',
                'edit' => 'admin.calendar-note.edit',
                'update' => 'admin.calendar-note.update',
                'destroy' => 'admin.calendar-note.destroy',
            ],
        ])->except('show');

        // Calendar Page
        Route::get('/calendar', [Backend\PageController::class, 'calendar'])->name('backend.calendar');
        Route::get('/pilot-reader', [Backend\PageController::class, 'pilotReader'])->name('backend.pilot-reader');

        // Finish Assignment
        Route::post('/assignment/{id}/finish', [Backend\PageController::class, 'finishAssignment'])->name('backend.assignment.finish');

        // Settings
        Route::post('/settings/update/welcome_email', [Backend\SettingsController::class, 'updateEmail'])->name('admin.settings.update.welcome_email'); // Store Feedback
        Route::post('/settings/update/terms', [Backend\SettingsController::class, 'updateTerms'])->name('admin.settings.update.terms'); // Store Terms
        Route::post('/settings/update/other-terms', [Backend\SettingsController::class, 'updateOtherTerms'])->name('admin.settings.update.other-terms');
        Route::post('/settings/update/opt-in-terms', [Backend\SettingsController::class, 'updateOptInTerms'])->name('admin.settings.update.opt-in-terms'); // Store Terms
        Route::post('/settings/update/opt-in-description', [Backend\SettingsController::class, 'updateOptInDescription'])->name('admin.settings.update.opt-in-description'); // Store Terms
        Route::post('/settings/update/opt-in-rektor-description', [Backend\SettingsController::class, 'updateOptInRektorDescription'])->name('admin.settings.update.opt-in-rektor-description'); // Store Terms
        Route::post('/settings/update/gt_confirmation_email', [Backend\SettingsController::class, 'gtConfirmationEmail'])->name('admin.settings.update.gt_confirmation_email'); // Store Feedback
        Route::post('/settings/update/webinar_email_template', [Backend\SettingsController::class, 'webinarEmailTemplate'])->name('admin.settings.update.webinar_email_template');
        Route::post('/settings/update/gt_reminder_email_template', [Backend\SettingsController::class, 'gtReminderEmail'])->name('admin.settings.update.gt_reminder_email_template');
        Route::post('/settings/update/course_not_started_reminder', [Backend\SettingsController::class, 'courseNotStartedReminder'])->name('admin.settings.update.course_not_started_reminder');
        Route::post('/settings/update/head-editor', [Backend\SettingsController::class, 'headEditor'])->name('admin.settings.update.head-editor');
        Route::post('/settings/update/editors-note', [Backend\SettingsController::class, 'updateEditorsNote'])->name('admin.settings.update.editors-note'); // Store Terms
        Route::post('/settings/create/{name}', [Backend\SettingsController::class, 'create'])->name('admin.settings.create');
        Route::post('/settings/update-record', [Backend\SettingsController::class, 'updateRecord'])->name('admin.settings.update-record');
        Route::get('/news', [Backend\SettingsController::class, 'news'])->name('admin.news.index');
        Route::post('/news/save', [Backend\SettingsController::class, 'saveNews'])->name('admin.news.save');

        Route::resource('/genre', Backend\GenreController::class, [
            'names' => [
                'index' => 'admin.genre.index',
                'show' => 'admin.genre.show',
                'store' => 'admin.genre.store',
                'update' => 'admin.genre.update',
                'destroy' => 'admin.genre.destroy',
            ],
        ])->except('create', 'edit');

        Route::delete('/self-publishing/remove-learner/{id}', [Backend\SelfPublishingController::class, 'removeLearnerFromPublishing'])
            ->name('admin.learner.remove-self-publishing');
        Route::get('/self-publishing/{id}/learners', [Backend\SelfPublishingController::class, 'learners'])->name('admin.self-publishing.learners');
        Route::post('/self-publishing/{id}/add-learners', [Backend\SelfPublishingController::class, 'addLearners'])->name('admin.self-publishing.add-learners');
        Route::get('/self-publishing/{id}/download-manuscript', [Backend\SelfPublishingController::class, 'selfPublishingDownloadManuscript'])
            ->name('admin.self-publishing.download-manuscript');
        Route::post('/self-publishing/{id}/add-feedback', [Backend\SelfPublishingController::class, 'addFeedback'])->name('admin.self-publishing.add-feedback');
        Route::get('/self-publishing/feedback/{feedback_id}/download', [Backend\SelfPublishingController::class, 'downloadFeedback'])->name('admin.self-publishing.download-feedback');
        Route::delete('/self-publishing/delete-learner/{learner_id}', [Backend\SelfPublishingController::class, 'deleteLearner'])->name('admin.self-publishing.delete-learner');
        Route::get('/self-publishing/orders', [Backend\SelfPublishingController::class, 'orders'])->name('admin.self-publishing.orders');
        Route::post('/self-publishing/{id}/update-status', [Backend\SelfPublishingController::class, 'updateStatus'])
            ->name('admin.self-publishing.update-status');
        Route::resource('/self-publishing', Backend\SelfPublishingController::class, [
            'names' => [
                'index' => 'admin.self-publishing.index',
                'show' => 'admin.self-publishing.show',
                'store' => 'admin.self-publishing.store',
                'update' => 'admin.self-publishing.update',
                'destroy' => 'admin.self-publishing.destroy',
            ],
        ])->except('create', 'edit');

        Route::get('/book-publisher/calculator', [Backend\BookPublisherController::class, 'calculator'])->name('admin.book-publisher.calculator');

        Route::resource('/marketing-plan', Backend\MarketingPlanController::class, [
            'names' => [
                'index' => 'admin.marketing-plan.index',
                'show' => 'admin.marketing-plan.show',
                'store' => 'admin.marketing-plan.store',
                'update' => 'admin.marketing-plan.update',
                'destroy' => 'admin.marketing-plan.destroy',
            ],
        ])->except('create', 'edit');

        Route::post('/book-for-sale/{book_for_sale_id}/save-inventory', [Backend\BookForSaleController::class, 'saveInventory'])
            ->name('admin.book-for-sale.save-inventory');
        Route::post('/book-for-sale/{book_for_sale_id}/save-sales', [Backend\BookForSaleController::class, 'saveSales'])
            ->name('admin.book-for-sale.save-sales');
        Route::get('/book-for-sale/{book_for_sale_id}/details', [Backend\BookForSaleController::class, 'saleDetails']);
        Route::get('/book-for-sale/{book_for_sale_id}/details', [Backend\BookForSaleController::class, 'saleDetails']);
        Route::delete('/book-for-sale/sales-report/{sale_id}/delete', [Backend\BookForSaleController::class, 'deleteSales']);
        Route::post('/book-for-sale/{book_for_sale_id}/save-distribution-cost', [Backend\BookForSaleController::class, 'saveDistributionCost'])
            ->name('admin.book-for-sale.save-distribution-cost');
        Route::delete('/book-for-sale/distribution-cost/{dist_id}/delete', [Backend\BookForSaleController::class, 'deleteDistributionCost'])
            ->name('admin.book-for-sale.delete-distribution-cost');

        Route::resource('/book-for-sale', Backend\BookForSaleController::class, [
            'names' => [
                'index' => 'admin.book-for-sale.index',
                'show' => 'admin.book-for-sale.show',
            ],
        ])->except('create', 'edit');

        Route::get('/application', [Backend\PageController::class, 'application'])->name('admin.application');

        Route::prefix('queue-jobs')->group(function () {
            Route::get('/', [Backend\QueueJobController::class, 'index'])->name('admin.queue-jobs');
            Route::post('/jobs/run', [Backend\QueueJobController::class, 'runJobs'])->name('admin.queue-jobs.jobs.run');
            Route::post('/failed-jobs/retry-all', [Backend\QueueJobController::class, 'retryAll'])->name('admin.queue-jobs.failed-jobs.retry-all');
            Route::post('/failed-jobs/{id}/retry', [Backend\QueueJobController::class, 'retry'])->name('admin.queue-jobs.failed-jobs.retry');
            Route::delete('/failed-jobs/{id}/delete', [Backend\QueueJobController::class, 'deleteFailedJob'])
                ->name('admin.queue-jobs.failed-jobs.destroy');
        });

        Route::post('/task/save', [Backend\ProjectController::class, 'saveTask'])->name('admin.project-task.save');
        Route::put('/project/task/{id}/update', [Backend\ProjectController::class, 'updateTask'])->name('admin.project-task.update');
        Route::post('/project/task/{id}/finish', [Backend\ProjectController::class, 'finishTask'])->name('admin.project-task.finish');
        Route::delete('/project/task/{id}/delete', [Backend\ProjectController::class, 'deleteTask'])->name('admin.project-task.delete');
        Route::post('/time-register/save', [Backend\TimeRegisterController::class, 'save'])->name('admin.time-register.save');
        Route::delete('/time-register/{id}/delete', [Backend\TimeRegisterController::class, 'destroy'])->name('admin.time-register.delete');
        Route::get('/time-register/{id}/time-used-list', [Backend\TimeRegisterController::class, 'timeUsedList']);
        Route::post('/time-register/{id}/save-time-used', [Backend\TimeRegisterController::class, 'saveTimeUsed']);
        Route::delete('/time-register/time-used/{id}/delete', [Backend\TimeRegisterController::class, 'deleteTimeUsed']);

        Route::get('/services', [Backend\PublishingPackageController::class, 'services'])->name('admin.service.index');
        Route::get('/all-services', [Backend\PublishingPackageController::class, 'getAllServices']);
        Route::post('/service/{id}/update-field', [Backend\PublishingPackageController::class, 'updateServiceField']);
        Route::post('/save-service', [Backend\PublishingPackageController::class, 'saveService']);

        Route::get('/assemble-book-packages/all-options', [Backend\AssembleBookController::class, 'getOptions']);
        Route::post('/assemble-book-packages/save-cover-or-color', [Backend\AssembleBookController::class, 'saveCoverOrColor']);
        Route::post('/assemble-book-packages/save-count-or-help', [Backend\AssembleBookController::class, 'saveCountOrHelp']);
        Route::view('/assemble-book-packages', 'backend.assemble-books.list');

        // Advisories
        Route::put('/advisory/{id}', [Backend\AdvisoryController::class, 'update'])->name('admin.advisory.update');

        Route::resource('/pulse', Backend\PulseController::class, [
            'names' => [
                'index' => 'admin.pulse.index',
                'show' => 'admin.pulse.show',
                'store' => 'admin.pulse.store',
                'update' => 'admin.pulse.update',
                'destroy' => 'admin.pulse.destroy',
            ],
        ])->except('create', 'edit');
        Route::post('/pulse/{id}/update-pulse-title', [Backend\PulseController::class, 'updatePulseTitle'])->name('admin.pulse.update-pulse-title'); // Assign User
        Route::post('/pulse/remove-subscriber', [Backend\PulseController::class, 'removeSubscriber'])->name('admin.pulse.remove-subscriber'); // Assign User

        Route::resource('/board', Backend\BoardController::class, [
            'names' => [
                'index' => 'admin.board.index',
                'show' => 'admin.board.show',
                'store' => 'admin.board.store',
                'update' => 'admin.board.update',
                'destroy' => 'admin.board.destroy',
            ],
        ])->except('create', 'edit');
        Route::post('/board/{id}/assign-user', [Backend\BoardController::class, 'assignUser'])->name('admin.board.assign-user'); // Assign User
        Route::post('/board/{id}/add-pulse', [Backend\BoardController::class, 'addPulse'])->name('admin.board.add-pulse'); // Assign User
        Route::post('/board/{id}/update-group-title', [Backend\BoardController::class, 'updateGroupTitle'])->name('update-group-title'); // Assign User
        Route::post('/board/{id}/update-pulse-status', [Backend\BoardController::class, 'setStatus'])->name('admin.board.update-pulse-status'); // Update pulse status
        Route::post('/board/{id}/update-timeline', [Backend\BoardController::class, 'setTimeline'])->name('admin.board.update-timeline'); // Update timeline

        Route::put('/survey/{id}/update-date', [Backend\SurveyController::class, 'updateDate'])->name('admin.survey.update-date');
        Route::resource('/survey', Backend\SurveyController::class, [
            'names' => [
                'index' => 'admin.survey.index',
                'show' => 'admin.survey.show',
                'edit' => 'admin.survey.edit',
                'store' => 'admin.survey.store',
                'update' => 'admin.survey.update',
                'destroy' => 'admin.survey.destroy',
            ],
        ])->except('create');

        Route::get('/survey/{id}/download-answers', [Backend\SurveyController::class, 'downloadAnswers'])->name('admin.survey.download-answers');
        Route::get('/survey/{id}/answers', [Backend\SurveyController::class, 'answers'])->name('admin.survey.answers');

        Route::resource('/survey/{survey_id}/question', Backend\SurveyQuestionController::class, [
            'names' => [
                'index' => 'admin.survey.question.index',
                'show' => 'admin.survey.question.show',
                'edit' => 'admin.survey.question.edit',
                'store' => 'admin.survey.question.store',
                'update' => 'admin.survey.question.update',
                'destroy' => 'admin.survey.question.destroy',
            ],
        ])->except('create');

        Route::get('translations', [Backend\PageController::class, 'translations']);

        Route::get('translations/view', [Backend\PageController::class, 'translations']);

        Route::prefix('zoom')->group(function () {
            Route::get('/', [Backend\ZoomController::class, 'index']);
            Route::get('webinar/{user_id}', [Backend\ZoomController::class, 'webinars'])->name('admin.zoom.webinars');
            Route::get('webinar/{user_id}/create', [Backend\ZoomController::class, 'createWebinar'])->name('admin.zoom.webinar.create');
            Route::post('webinar/{user_id}/store', [Backend\ZoomController::class, 'storeWebinar'])->name('admin.zoom.webinar.store');
            Route::get('webinar/{webinar_id}/edit', [Backend\ZoomController::class, 'editWebinar'])->name('admin.zoom.webinar.edit');
            Route::put('webinar/{webinar_id}/update', [Backend\ZoomController::class, 'updateWebinar'])->name('admin.zoom.webinar.update');
            Route::delete('webinar/{webinar_id}/delete', [Backend\ZoomController::class, 'deleteWebinar'])->name('admin.zoom.webinar.delete');
            Route::post('webinar/{webinar_id}/panelist', [Backend\ZoomController::class, 'storePanelist'])->name('admin.zoom.webinar.panelist.store');
            Route::delete('webinar/{webinar_id}/panelist/{panelist_id}', [Backend\ZoomController::class, 'deletePanelist'])->name('admin.zoom.webinar.panelist.delete');
        });

        // head editor route
        Route::post('personal_assignment/{id}/approve_feedback/{learner_id}', [Backend\AssignmentController::class, 'approveFeedbackNoGroup'])->name('head_editor.personal_assignment.feedbac_approve');
        Route::post('course_assignment/{id}/approve_feedback/{learner_id}/feedback/{feedback_id}', [Backend\AssignmentGroupController::class, 'approveFeedbackCourse'])->name('head_editor.course_assignment.feedback_approve');
        Route::post('shop-manuscript-taken/{id}/approve-feedback/{learner_id}/feedback/{feedback_id}', [Backend\ShopManuscriptController::class, 'approveFeedback'])->name('head_editor.shop-manuscript-taken-feedback.approve');
        Route::post('other-service/{id}/approve-feedback/{type}', [Backend\OtherServiceController::class, 'approveFeedback'])->name('head_editor.other-service.approve-feedback');
        Route::post('other-service/{id}/mark-as-finished/coaching-time', [Backend\OtherServiceController::class, 'coachingTimeMarkFinished'])->name('head_editor.other-service.mark-finished');

        // editor assignment
        Route::post('editor_assignment_price/save', [Backend\EditorAssignmentPriceController::class, 'save'])->name('editor_assignment_price.save');
        Route::post('editor_assignment_price/{id}/delete', [Backend\EditorAssignmentPriceController::class, 'delete'])->name('editor_assignment_price.delete');
        Route::get('editor_total_worked/{id}', [Backend\EditorController::class, 'total'])->name('admin.total_editor_worked');
        Route::post('saveGenrePrefences/{from_admin}', [Backend\EditorController::class, 'saveGenrePrefences'])->name('admin.save-genre-prefences');
        Route::post('deleteGenrePreferences/{id}', [Backend\EditorController::class, 'deleteGenrePreferences'])->name('admin.delete-genre-preferences');
        Route::post('hideShowEditor/{editor_id}/{hide}', [Backend\EditorController::class, 'hideShowEditor'])->name('admin.hide-show-editor');

        Route::get('showEditorHidden/{editor_id}', [Backend\EditorController::class, 'showEditorHidden'])->name('admin.show-editor-hidden');
        Route::get('deleteEditorHidden/{id}', [Backend\EditorController::class, 'deleteEditorHidden'])->name('admin.delete-editor-hidden');
        Route::post('setHowManyManuscriptYouCanTake/{id}', [Backend\EditorController::class, 'setHowManyManuscriptYouCanTake'])->name('admin.setHowManyManuscriptYouCanTake');
        Route::post('sendRequestToEditor/{id}', [Backend\LearnerController::class, 'sendRequestToEditor'])->name('admin.send-request-to-editor');
        Route::post('headEditorToEditor/{editor_id}/{type}/{title}/{learner}', [Backend\HeadEditorController::class, 'sendEmail'])->name('admin.head-editor-to-editor');

        Route::post('/tinymce-upload', [Backend\TinymceController::class, 'store']);
        Route::get('/tinymce/images', [Backend\TinymceController::class, 'images'])->name('admin.tinymce.images');
    });

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::view('password/reset', 'backend.auth.forgot-password')->name('admin.password-reset');
        Route::post('password/email', [Auth\ResetPasswordController::class, 'adminStore'])->name('admin.password.email');
        Route::get('passwordreset/{token}', [Auth\ResetPasswordController::class, 'adminResetForm'])->name('admin.passwordreset.form');
        Route::post('passwordreset/{token}/update', [Auth\ResetPasswordController::class, 'adminUpdatePassword'])->name('admin.passwordreset.update');
        Route::post('login', [Auth\LoginController::class, 'adminLogin'])->name('admin.login.store');
    });

    Route::get('/backup', [Backend\PageController::class, 'backup'])->name('backup');
    Route::get('/check-nearly-expired-course', [Backend\PageController::class, 'checkNearlyExpiredCourses']);
    Route::get('/user-activity', [Backend\PageController::class, 'userActivity']);
    Route::get('/user-activity/{id}', [Backend\PageController::class, 'userActivityDetails']);

    Route::get('/dropbox/shared-link/{path}', [Frontend\DropboxController::class, 'createSharedLink'])
        ->where('path', '.*')
        ->name('admin.dropbox.shared_link');
    Route::get('/dropbox/download/{path}', [Frontend\DropboxController::class, 'downloadFile'])
        ->where('path', '.*')
        ->name('admin.dropbox.download_file');
});

/**
 * Editor Routes
 */
Route::domain($editor)->group(function () {
    Route::middleware('editor', 'logActivity')->group(function () {

        Route::get('/', [Editor\PageController::class, 'dashboard'])->name('editor.dashboard');
        Route::get('/upcoming-assignments', [Editor\PageController::class, 'upcomingAssignments'])->name('editor.upcoming-assignment');
        Route::get('assignmentArchive', [Editor\PageController::class, 'assignmentArchive'])->name('editor.assignment-archive');
        Route::get('manuscriptYouCanTake', [Editor\ManuscriptEditorCanTakeController::class, 'index'])->name('editor.manuscript-you-can-take');
        Route::post('manuscriptYouCanTake/save', [Editor\ManuscriptEditorCanTakeController::class, 'save'])->name('editor.manuscript-you-can-take-save');
        Route::post('manuscriptYouCanTake/{id}/delete', [Editor\ManuscriptEditorCanTakeController::class, 'delete'])->name('editor.manuscript-you-can-take.delete');
        Route::get('/yearly-calendar', [Editor\PageController::class, 'yearlyCalendar'])->name('editor.yearly-calendar.index');
        Route::get('/editors-note', [Editor\PageController::class, 'editorsNote'])->name('editor.editors-note');
        Route::get('/assigned-webinar', [Editor\AssignedWebinarController::class, 'show'])->name('editor.assigned-webinar');
        Route::post('/self-publishing/{id}/feedback', [Editor\PageController::class, 'selfPublishingFeedback'])->name('editor.self-publishing.feedback');
        Route::get('/self-publishing/{id}/download-manuscript', [Editor\PageController::class, 'selfPublishingDownloadManuscript'])->name('editor.self-publishing.download-manuscript');
        Route::post('/assignment-manuscript/{id}/mark-finished', [Editor\PageController::class, 'assignmentManuscriptFinished'])->name('editor.assignment-manuscript.mark-finished');
        Route::get('/project/{id}', [Editor\PageController::class, 'projectDetails'])->name('editor.project.show');
        Route::post('/project/{id}/update-editor-hours', [Editor\PageController::class, 'projectEditorHours'])->name('editor.project.update-editor-hours');
    });

    Route::middleware('editor')->group(function () {

        Route::post('backend/change-password', [Backend\PageController::class, 'changePassword'])->name('editor.change-password');
        Route::post('assignment_manuscript/{id}/learner/{learner_id}/feedback', [Backend\AssignmentController::class, 'manuscriptFeedbackNoGroup'])->name('editor.assignment.group.manuscript-feedback-no-group');
        Route::post('/shop-manuscript-taken/{id}/add-feedback', [Backend\ShopManuscriptController::class, 'addFeedback'])->name('editor.admin.shop-manuscript-taken-feedback.store');
        Route::get('backend/{id}/download_shop_manuscript', [Backend\PageController::class, 'downloadShopManuscript'])->name('editor.backend.download_shop_manuscript');
        Route::get('backend/{id}/download_assigned_manuscript', [Backend\PageController::class, 'downloadAssignedManuscript'])->name('editor.backend.download_assigned_manuscript');
        Route::post('/group/{group_id}/learner/{id}/submit_feedback', [Backend\AssignmentGroupController::class, 'submit_feedback'])->name('editor.assignment.group.submit_feedback'); // Submit assignment feedback
        Route::post('/other-service/{id}/update-status/{type}', [Backend\OtherServiceController::class, 'updateStatus'])->name('editor.other-service.update-status');
        Route::post('/other-service/{id}/add-feedback/{type}', [Backend\OtherServiceController::class, 'addFeedback'])->name('editor.other-service.add-feedback');
        Route::get('/other-service/{id}/download/{type}', [Backend\OtherServiceController::class, 'downloadOtherServiceDoc'])->name('editor.other-service.download-doc'); // Download assignment feedback
        Route::post('/other-service/{id}/coaching-timer/set_replay', [Backend\OtherServiceController::class, 'editorSetReplay'])->name('editor.other-service.coaching-timer.set_replay');
        Route::get('settings', [Backend\EditorController::class, 'settings'])->name('editor.settings');
        Route::post('saveGenrePrefences/{from_admin}', [Backend\EditorController::class, 'saveGenrePrefences'])->name('editor.save-genre-prefences');
        Route::post('deleteGenrePreferences/{id}', [Backend\EditorController::class, 'deleteGenrePreferences'])->name('editor.delete-genre-preferences');
        Route::post('saveAssignmentManuscriptEditorCanTake/{id}/{assignment_manu_id}', [Backend\EditorController::class, 'saveAssignmentManuscriptEditorCanTake'])->name('editor.saveAssignmentManuscriptEditorCanTake');
        Route::get('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}', [Backend\LearnerController::class, 'shopManuscriptTakenShowEditorPreview'])->name('editor.shop_manuscript_taken');
        Route::get('acceptShopManuscriptRequest/{shop_manuscript_taken_id}/{accept}/{request_id}', [Backend\ShopManuscriptController::class, 'editorAcceptRequest'])->name('editor.acceptShopManuscriptRequest');
        Route::post('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}/comment', [Backend\LearnerController::class, 'shopManuscriptTakenShowComment'])->name('editor.shop_manuscript_taken_comment');
        Route::post('/update-expected-finish/{type}/{id}', [Backend\PageController::class, 'updateExpectedFinish'])->name('editor.personal-assignment.update-expected-finish');
        Route::get('assignment-manuscript/{id}/download-letter', [Backend\AssignmentController::class, 'downloadManuscriptLetter'])->name('editor.assignment.manuscript.download_letter');
        Route::post('/free-manuscript/{id}/edit-content', [Backend\FreeManuscriptController::class, 'editContent'])->name('editor.free-manuscript.edit-content');
        Route::post('/free-manuscript/{id}/send_feedback', [Backend\FreeManuscriptController::class, 'sendFeedback'])->name('editor.free-manuscript.send_feedback');
        Route::get('/free-manuscript/{id}/download', [Backend\FreeManuscriptController::class, 'downloadContent'])->name('editor.free-manuscript.download');
        Route::post('/time-register/save', [Backend\TimeRegisterController::class, 'save'])->name('editor.time-register.save');
        // Route::delete('/time-register/{id}/delete', [Backend\TimeRegisterController::class, 'destroy'])->name('admin.time-register.delete');
        Route::get('/time-register/{id}/time-used-list', [Backend\TimeRegisterController::class, 'timeUsedList']);
        Route::post('/time-register/{id}/save-time-used', [Backend\TimeRegisterController::class, 'saveTimeUsed']);
        Route::delete('/time-register/time-used/{id}/delete', [Backend\TimeRegisterController::class, 'deleteTimeUsed']);

        Route::prefix('/coaching-time')->name('editor.coaching-time.')->group(function () {
            Route::controller(CoachingTimeController::class)->group(function() {
                Route::get('/', 'index')->name('index');
                Route::get('/calendar', 'calendar')->name('calendar');

                Route::prefix('/time-slots')->name('time-slots.')->group(function () {
                    Route::get('fetch', 'fetchTimeSlot')->name('fetch');
                    Route::post('/',  'storeTimeSlot')->name('store');
                    Route::delete('{id}', 'destroyTimeSlot')->name('destroy');
                });
                Route::post('/request/{id}/accept', 'acceptRequest')->name('request.accept');
                Route::post('/request/{id}/decline', 'declineRequest')->name('request.decline');
            });
        });
    });

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::view('password/reset', 'editor.auth.forgot-password')->name('editor.password-reset');
        Route::post('password/email', [Auth\ResetPasswordController::class, 'editorStore'])->name('editor.password.email');
        Route::get('passwordreset/{token}', [Auth\ResetPasswordController::class, 'editorResetForm'])->name('editor.passwordreset.form');
        Route::post('passwordreset/{token}/update', [Auth\ResetPasswordController::class, 'editorUpdatePassword'])->name('editor.passwordreset.update');
        Route::get('login/editor-email/{email_hash}', [Auth\LoginController::class, 'editorEmailLogin'])->name('editor.login.email');
        Route::post('login', [Auth\LoginController::class, 'editorLogin'])->name('editor.login.store');
    });

    Route::get('/dropbox/shared-link/{path}', [Frontend\DropboxController::class, 'createSharedLink'])
        ->where('path', '.*')
        ->name('editor.dropbox.shared_link');
    Route::get('/dropbox/download/{path}', [Frontend\DropboxController::class, 'downloadFile'])
        ->where('path', '.*')
        ->name('editor.dropbox.download_file');
});

// File Manager routes
/*Route::group(['middleware' => 'auth'], function () {
    Route::get('/laravel-filemanager', '\Unisharp\Laravelfilemanager\controllers\LfmController@show');
    Route::post('/laravel-filemanager/upload', '\Unisharp\Laravelfilemanager\controllers\UploadController@upload');
    // list all lfm routes here...

    // uncommented the $middleware to fix error on php7.3
    $middleware = array_merge(\Config::get('lfm.middlewares'), [
        '\Unisharp\Laravelfilemanager\middlewares\MultiUser',
        '\Unisharp\Laravelfilemanager\middlewares\CreateDefaultFolder',
    ]);
    $prefix = \Config::get('lfm.url_prefix', \Config::get('lfm.prefix', 'laravel-filemanager'));
    $as = 'unisharp.lfm.';
    $namespace = '\Unisharp\Laravelfilemanager\controllers';

// make sure authenticated
    Route::group(compact('middleware', 'prefix', 'as', 'namespace'), function () {

        // Show LFM
        Route::get('/', [
            'uses' => 'LfmController@show',
            'as' => 'show',
        ]);

        // Show integration error messages
        Route::get('/errors', [
            'uses' => 'LfmController@getErrors',
            'as' => 'getErrors',
        ]);

        // upload
        Route::any('/upload', [
            'uses' => 'UploadController@upload',
            'as' => 'upload',
        ]);

        // list images & files
        Route::get('/jsonitems', [
            'uses' => 'ItemsController@getItems',
            'as' => 'getItems',
        ]);

        // folders
        Route::get('/newfolder', [
            'uses' => 'FolderController@getAddfolder',
            'as' => 'getAddfolder',
        ]);
        Route::get('/deletefolder', [
            'uses' => 'FolderController@getDeletefolder',
            'as' => 'getDeletefolder',
        ]);
        Route::get('/folders', [
            'uses' => 'FolderController@getFolders',
            'as' => 'getFolders',
        ]);

        // crop
        Route::get('/crop', [
            'uses' => 'CropController@getCrop',
            'as' => 'getCrop',
        ]);
        Route::get('/cropimage', [
            'uses' => 'CropController@getCropimage',
            'as' => 'getCropimage',
        ]);
        Route::get('/cropnewimage', [
            'uses' => 'CropController@getNewCropimage',
            'as' => 'getCropimage',
        ]);

        // rename
        Route::get('/rename', [
            'uses' => 'RenameController@getRename',
            'as' => 'getRename',
        ]);

        // scale/resize
        Route::get('/resize', [
            'uses' => 'ResizeController@getResize',
            'as' => 'getResize',
        ]);
        Route::get('/doresize', [
            'uses' => 'ResizeController@performResize',
            'as' => 'performResize',
        ]);

        // download
        Route::get('/download', [
            'uses' => 'DownloadController@getDownload',
            'as' => 'getDownload',
        ]);

        // delete
        Route::get('/delete', [
            'uses' => 'DeleteController@getDelete',
            'as' => 'getDelete',
        ]);

        // Route::get('/demo', 'DemoController@index');
    });

    Route::group(compact('prefix', 'as', 'namespace'), function () {
        // Get file when base_directory isn't public
        $images_url = '/' . \Config::get('lfm.images_folder_name') . '/{base_path}/{image_name}';
        $files_url = '/' . \Config::get('lfm.files_folder_name') . '/{base_path}/{file_name}';
        Route::get($images_url, 'RedirectController@getImage')
            ->where('image_name', '.*');
        Route::get($files_url, 'RedirectController@getFile')
            ->where('file_name', '.*');
    });
});*/

Route::get('/check-nearly-expired-course', [HomeController::class, 'checkNearlyExpiredCourse']);

/**
 * Authentication Routes
 */
Route::prefix('auth')->group(function () {
    Route::get('logout', [Auth\LoginController::class, 'logout'])->name('auth.logout-get');
    Route::post('logout', [Auth\LoginController::class, 'logout'])->name('auth.logout');
    Route::post('password', [Auth\PasswordController::class, 'updatePassword']);
});

// Localization - use for vue
Route::get('/js/lang.js', function () {
    $strings = Cache::rememberForever('lang.js', function () {
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
})->name('assets.lang');
