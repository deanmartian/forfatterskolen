<?php

// Domains
if(config('app.app_site') == 'no'){
    $front = 'www.forfatterskolen.no';
    $admin = 'admin.forfatterskolen.no';
    $editor = 'editor.forfatterskolen.no';
}elseif(config('app.app_site') == 'localhost'){
    $front = 'forfatterskolen.local';
    $admin = 'admin.forfatterskolen.local';
    $editor = 'editor.forfatterskolen.local';
}elseif(config('app.app_site') == 'dev.no'){
    $front = 'dev.forfatterskolen.no';
    $admin = 'admin.dev.forfatterskolen.no';
    $editor = 'editor.dev.forfatterskolen.no';
}

// get/set the locale
$locale = App::getLocale();
App::setLocale($locale);

/**
 * Front End Routes
 *
 *
 */
Route::view('/easywrite', 'frontend-easywrite.index');
Route::group([
    'domain' => $front,
], function(){


    Route::group([
        'namespace' => 'Frontend',
        'middleware' => 'logActivity'
    ], function () {
        Route::get('/', 'HomeController@index')->name('front.home'); // Homepage
        Route::post('/fb-leads', 'HomeController@fbLeads'); // Homepage
        Route::post('/agree-gdpr', 'HomeController@agreeGdpr')->name('front.agree-gdpr');
        Route::get('/testemail', 'HomeController@testEmail');
        Route::get('/bisnode', 'HomeController@bisnode');
        Route::get('/test-fiken', 'HomeController@testFiken');
        Route::get('/test-excel', 'HomeController@testExcel');
        Route::post('/gotowebinar', 'HomeController@gtWebinarSendEmail');
        Route::post('/gotowebinar/course/{id}/register', 'HomeController@gtWebinarCourseRegister');
        Route::get('/contact-us', 'HomeController@contact_us')->name('front.contact-us'); // Contact Us
        Route::post('/contact-us', 'HomeController@contact_us'); // Contact Us
        Route::get('/gift-cards', 'HomeController@giftCards')->name('front.gift-cards');
        Route::post('/set-gift-card', 'HomeController@setGiftCard');
        Route::get('/faq', 'HomeController@faq')->name('front.faq'); // FAQ
        Route::get('/support', 'HomeController@support')->name('front.support'); // Support
        Route::get('/support/{id}/articles', 'HomeController@supportArticles')->name('front.support-articles'); // Support Articles
        Route::get('/support/{id}/article/{article_id}', 'HomeController@supportArticle')->name('front.support-article'); // Support Article
        Route::get('/free-webinar/{id}/', 'HomeController@freeWebinar')->name('front.free-webinar'); // Support Article
        Route::post('/free-webinar/{id}/', 'HomeController@freeWebinar')->name('front.free-webinar'); // Support Article
        Route::get('/free-webinar/{id}/thank-you', 'HomeController@freeWebinarThanks')->name('front.free-webinar-thanks'); // Support Article
        Route::get('/webinartakk', 'HomeController@webinarThanks')->name('front.webinar-thanks'); // Support Article
        Route::get('/children','HomeController@children')->name('front.children');
        Route::view('/subscribe-success', 'frontend.subscribe-success')->name('front.subscribe-success'); // Homepage
        Route::get('/shop-manuscript', 'ShopManuscriptController@index')->name('front.shop-manuscript.index'); // Shop Manuscript Listing
        Route::get('/shop-manuscript/export-single-bought', 'ShopManuscriptController@exportSingleBought'); // Shop Manuscript Listing
        Route::get('/blog', 'HomeController@blog')->name('front.blog'); // Blog Page
        Route::get('/blog/{id}', 'HomeController@readBlog')->name('front.read-blog'); // Blog Page
        Route::get('/publishing', 'HomeController@publishing')->name('front.publishing'); // Forlag page
        Route::get('/konkurranse', 'HomeController@competition')->name('front.competition'); // Forlag page
        Route::get('/coaching-timer', 'HomeController@coachingTimer')->name('front.coaching-timer'); // Coaching Timer Page
        Route::get('/coaching-timer/checkout/{plan}', 'HomeController@coachingTimerCheckout')->name('front.coaching-timer-checkout'); // Coaching Timer Page
        Route::post('/coaching-timer/checkout/{plan}', 'HomeController@coachingTimerCheckout')->name('front.coaching-timer-checkout'); // Coaching Timer Page
        Route::post('/coaching-timer', 'HomeController@coachingTimer')->name('front.coaching-timer'); // Coaching Timer Page
        Route::post('coaching-timer/{plan}/place-order', 'HomeController@coachingTimerPlaceOrder')->name('front.coaching-timer-place-order'); // Coaching Timer Page
        Route::get('/coaching-timer/export-single-bought', 'HomeController@exportSingleBoughtCoaching');
        Route::get('/course/export-pay-later-with-active', 'HomeController@exportCoursePayLaterWithActive');
        Route::get('/vipps-order-status/{orderId}', 'HomeController@checkVippsOrderStatus');
        Route::get('/chat', 'ChatController@index');
        Route::post('/chat', 'ChatController@sendMessage');
        Route::get('/import-webinar-registrants', 'HomeController@importWebinarRegistrants');
        Route::post('/import-webinar-registrants', 'HomeController@processImportWebinarRegistrants')
            ->name('process-import-webinar-registrants');
        Route::get('/soknad2024', 'HomeController@application');
        Route::post('/soknad2024', 'HomeController@application');
        Route::get('/export/course-taken/{year}', 'HomeController@exportCourseTakenByYear');
        Route::get('/export/shop-manuscripts-taken/{year}', 'HomeController@exportShopManuscriptsTakenByYear');

        Route::get('/dropbox/redirect', 'DropboxController@redirectToDropbox')->name('dropbox.redirect');
        Route::get('/dropbox/callback', 'DropboxController@handleDropboxCallback')->name('dropbox.callback');
        Route::post('/dropbox/refresh-token', 'DropboxController@refreshDropboxAccessToken')->name('dropbox.refresh_token');
        Route::get('/dropbox/upload', 'DropboxController@dropboxUpload');
        Route::post('/dropbox/upload', 'DropboxController@dropboxPostUpload')->name('dropbox.post-upload');
        Route::get('/dropbox/shared-link/{path}', 'DropboxController@createSharedLink')
            ->where('path', '.*')
            ->name('dropbox.shared_link');
        Route::get('/dropbox/download/{path}', 'DropboxController@downloadFile')
            ->where('path', '.*')
            ->name('dropbox.download_file');


        Route::get('/power-office', 'HomeController@powerOffice');

        Route::group([
            'prefix' => 'coaching-time'
        ], function() {
            Route::post('/calculate', 'HomeController@coachingTimeCalculate');
            Route::post('/validate-form', 'HomeController@coachingTimeValidate');
        });

        Route::get('/copy-editing', 'HomeController@copyEditing')->name('front.copy-editing'); // Copy Editing Page
        Route::post('/copy-editing', 'HomeController@copyEditing')->name('front.copy-editing'); // Copy Editing Page
        Route::get('/other-services', 'HomeController@otherServices')->name('front.other-services-page');
        Route::get('/other-services/checkout/{plan}/{has_data}', 'HomeController@otherServiceCheckout')->name('front.other-service-checkout');
        Route::post('/other-services/checkout/{plan}/{has_data}', 'HomeController@otherServiceCheckout')->name('front.other-service-checkout');
        Route::post('/other-services/place_order', 'HomeController@otherServiceOrder')->name('front.other-service-place_order');
        Route::get('/thank-you', 'HomeController@thankyou')->name('front.simple.thankyou'); // Thank You
        Route::get('/correction', 'HomeController@correction')->name('front.correction'); // Correction Page
        Route::post('/correction', 'HomeController@correction')->name('front.correction'); // Correction Page
        Route::get('/gratis-tekstvurdering', 'ShopManuscriptController@freeManuscriptShow')->name('front.free-manuscript.index'); // Free Manuscript
        Route::get('/gratistekstvurdering', 'ShopManuscriptController@freeManuscriptShowOther');
        Route::get('/gratis-tekstvurdering/success', 'ShopManuscriptController@freeManuscriptShowSuccess')->name('front.free-manuscript.success'); // Free Manuscript
        Route::post('/gratis-tekstvurdering/send', 'ShopManuscriptController@freeManuscriptSend')->name('front.free-manuscript.send'); // Free Manuscript Send
        Route::post('/gratistekstvurdering/send', 'ShopManuscriptController@freeManuscriptSendOther')->name('front.free-manuscript.send-other'); // Free Manuscript Send
        Route::post('/free-manuscript/set-word-count', 'ShopManuscriptController@freeManuscriptWordCount')->name('front.free-manuscript.set-wordcount');
        Route::get('/personal-trainer/apply', 'HomeController@personalTrainer')->name('front.personal-trainer.apply');
        Route::post('/personal-trainer/send', 'HomeController@personalTrainerSend')->name('front.personal-trainer.send');
        Route::get('/personal-trainer/thank-you', 'HomeController@personalTrainerThanks')->name('front.personal-trainer.thank-you');

        Route::get('/innlevering', 'HomeController@skrive2020')->name('front.skrive2020');
        Route::post('/innlevering/send', 'HomeController@innleveringCompetitionSend')->name('front.innlevering.send');
        Route::get('/takk', 'HomeController@innleveringCompetitionThanks')->name('front.innlevering.thank-you');

        Route::post('/', 'HomeController@homeOptIn')->name('front.home'); // Homepage

        Route::get('/opt-in/{slug?}', 'HomeController@optIn')->name('front.opt-in'); // Opt-in page
        Route::post('/opt-in/{slug?}', 'HomeController@optIn')->name('front.opt-in'); // Opt-in page
        Route::get('/opt-in/{slug?}/download', 'HomeController@downloadOptIn')->name('front.opt-in.download'); // Download Opt-in file

        // opt in thank you pages
        Route::get('/opt-in/thanks/{slug?}', 'HomeController@optInThanks')->name('front.opt-in.thanks');
        Route::get('/opt-in/ref/{slug?}', 'HomeController@optInReferral')->name('front.opt-in.referral');

        /*Route::get('/opt-in/rektor-tips', 'HomeController@optInRektor')->name('front.opt-in.rektor-tips'); // Opt-in page
        Route::post('/opt-in/rektor-tips', 'HomeController@optInRektor')->name('front.opt-in.rektor-tips'); // Opt-in page*/

        Route::get('/opt-in-terms', 'HomeController@optInTerms')->name('front.opt-in-terms'); // Opt-in page

        Route::get('/terms/{slug?}', 'HomeController@terms')->name('front.terms'); // Terms page

        Route::get('/upgrade-manuscript/{id}/checkout', 'ShopManuscriptController@checkoutUpgradeManuscript')->name('front.shop-manuscript.upgrade-manuscript-checkout'); // Checkout Shop Manuscript
        Route::get('/contract/{code}', 'HomeController@contract')->name('front.contract-view');
        Route::get('/contract/{code}/download', 'HomeController@contractDownload')->name('front.contract.download');
        Route::post('/contract/{code}/sign', 'HomeController@contractSign')->name('front.contract.sign');
        Route::get('/generate-image', 'HomeController@generateImage');


        Route::group([
            'prefix' => 'shop-manuscript'
        ], function() {
            Route::get('/{id}/checkout', 'ShopManuscriptController@checkout')->name('front.shop-manuscript.checkout'); // Checkout Shop Manuscript
            Route::post('/{id}/place_order', 'ShopManuscriptController@place_order')->name('front.shop-manuscript.place_order'); // Checkout Shop Manuscript
            Route::get('/{id}/cancelled-order', 'ShopManuscriptController@orderCancelled')->name('front.shop-manuscript.cancelled-order'); // Checkout
            Route::post('/{id}/checkout/validate-order', 'ShopManuscriptController@validateOrder')->name('front.shop-manuscript.validate-order');
            Route::post('/{id}/checkout/validate-form', 'ShopManuscriptController@validateForm')->name('front.shop-manuscript.validate-form');
            Route::post('/{id}/checkout/vipps', 'ShopManuscriptController@vippsCheckout')->name('front.shop-manuscript.vipps');
            Route::get('/{id}/checkout/process-vipps', 'ShopManuscriptController@processVipps')->name('front.shop-manuscript.checkout.process-vipps');
            Route::get('/{id}/thankyou', 'ShopManuscriptController@thankyou')->name('front.shop-manuscript.thankyou');
        });

        Route::get('/shop-manuscript/payment/paypal/{invoice_id}', 'ShopManuscriptController@paypalPayment')->name('front.shop-manuscript.paypal-payment'); // Paypal Payment
        Route::post('/upgrade-manuscript/{id}/place_upgrade', 'ShopManuscriptController@upgradeManuscript')->name('front.shop-manuscript.upgrade-manuscript'); // Checkout Shop Manuscript

        Route::get('/email/confirmation/{token}', 'HomeController@emailConfirmation')->name('front.email-confirmation');
        Route::get('/email/attachment/{token}', 'HomeController@emailAttachment')->name('front.email-attachment');
        Route::get('/email/check-open/{token}', 'HomeController@emailTracking')->name('front.email-track');

        Route::get('/henrik-langeland', 'HomeController@henrikPage')->name('front.henrik'); // Upviral ref page
        Route::get('/skrive2020', 'HomeController@innleveringCompetition')->name('front.innlevering.join');
        Route::get('/poems', 'HomeController@poems')->name('front.poems'); // Poems page
        Route::get('/gro-dahle', 'HomeController@grodahlePage')->name('front.gro-dahle');

        Route::get('/reprise', 'HomeController@replay')
            ->name('front.reprise'); // Replay Page
        Route::get('/barn', 'HomeController@barn')
            ->name('front.barn'); // Replay Page
        Route::get('/skrivdittliv', 'HomeController@skrivdittliv')
            ->name('front.skrivdittliv');
        Route::get('/hererjeg', 'HomeController@hereIam')
            ->name('front.here-i-am'); // Replay Page

        // Test Manuscript (Shop Manuscript)
        Route::post('/test_manuscript', 'ShopManuscriptController@test_manuscript')->name('front.shop-manuscript.test_manuscript'); // Test count shop manuscript

        // Pay IPN
        Route::post('/paypalipn', 'ShopController@paypalIPN')->name('front.shop.paypalipn'); // Paypal IPN

        // book invitation
        Route::get('/book/invitation/{link_token}','PilotReaderBookSettingsController@openInvitationLink');
        Route::post('/book/invite/send','PilotReaderBookSettingsController@unauthenticatedSendInvitation')->name('book.invite.send');
        Route::post('/email/validate', 'PilotReaderBookSettingsController@unauthenticatedEmailValidation');

        // private groups
        Route::get('/invitation/group/accept/{link_token}', 'PrivateGroupMembersController@openInvitationLink');
        Route::post('/private-group/email/validate', 'PrivateGroupMembersController@unauthenticatedEmailValidation');
        Route::post('/private-group/invite/send', 'PrivateGroupMembersController@unauthenticatedSendInvitation');

        Route::get('/webinar-pakke-campaign', 'HomeController@webinarPakkeRef'); // Webinar-pakke campaign page
        Route::get('/test-campaign', 'HomeController@testCampaign'); // Upviral ref page

        Route::get('/goto-webinar/register/{webinar_key}/{email}', 'HomeController@gotoWebinarEmailRegistration')
            ->name('front.goto-webinar.registration.email'); // GotoWebinar Registration through email

        Route::get('/vipps', 'VippsController@index');
        Route::post('/vipps/payment', 'HomeController@paymentCallback')->name('vipps.payment');
        Route::post('/vipps/payment/v2/payments/{orderId}', 'HomeController@paymentCallback');
        Route::get('/vipps/fallback', 'HomeController@vippsFallback')->name('vipps.fallback');
        Route::get('/vipps/payment/{orderId}/details', 'VippsController@getPaymentDetails');

        Route::get('/file/{hash}', 'HomeController@checkFileFromDB');

        Route::get('/bambora/accept', 'HomeController@bamboraAccept');
        Route::get('/bambora/paymentComplete', 'HomeController@bamboraPaymentComplete');
        Route::get('/has-paid-course', 'ShopController@hasPaidCourse');
        Route::get('/current-user', 'LearnerController@currentUser');
        Route::post('/file/count-characters', 'LearnerController@countFileCharacters');
        // Course
        Route::group([
            'prefix' => 'course'
        ], function(){
            Route::get('/', 'CourseController@index')->name('front.course.index'); // Course Listing
            Route::get('/{id}', 'CourseController@show')->name('front.course.show'); // Course Details
            Route::get('/{id}/checkout', 'ShopController@sveaCheckout')->name('front.course.checkout'); // Checkout
            Route::get('/{id}/application', 'CourseController@application')->name('front.course.application'); // Checkout
            Route::post('/{id}/application/process', 'CourseController@processApplication')->name('front.course.process-application'); // Checkout
            Route::get('/{id}/application/thank-you', 'CourseController@applicationThankyou')->name('front.course.application.thank-you'); // Checkout
            Route::get('/{id}/fs_checkout', 'ShopController@checkout')->name('front.course.fs-checkout'); // Checkout
            Route::get('/{id}/cancelled-order', 'ShopController@orderCancelled')->name('front.course.cancelled-order');
            Route::get('/{id}/checkout-svea', 'ShopController@sveaCheckout')->name('front.course.svea-checkout'); // Checkout
            Route::post('/{id}/checkout/process-order', 'ShopController@processOrder')->name('front.course.process_order'); // Place Order
            Route::get('/{id}/thank-you', 'CourseController@thankyou')->name('front.course.thank-you'); // Checkout
            Route::post('/{id}/checkout/validate-form', 'ShopController@validateCheckoutForm')->name('front.course.checkout.validate-form');
            Route::post('/{id}/checkout/vipps', 'ShopController@vippsCheckout')->name('front.course.checkout.vipps');
            Route::get('/{id}/checkout/process-vipps', 'ShopController@processVipps')->name('front.course.checkout.process-vipps');
            Route::get('/{id}/checkout-test', 'ShopController@checkoutTest')->name('front.course.checkout-test'); // Checkout
            Route::post('/{id}/proceed-checkout', 'ShopController@proceedCheckout')->name('front.course.proceed-checkout'); // Checkout
            Route::get('/{id}/discount/{coupon}', 'ShopController@applyDiscount')->name('front.course.apply-discount'); // Checkout
            Route::post('/{id}/checkout/place_order', 'ShopController@place_order')->name('front.course.place_order'); // Place Order
            Route::post('/{id}/checkout/place_order_test', 'ShopController@place_order_test')->name('front.course.place_order_test'); // Place Order
            Route::get('/{id}/check_discount/', 'ShopController@checkDiscount')->name('front.course.checkDiscount'); // Check Discount
            Route::get('/{id}/check_coupon_discount/{coupon}', 'ShopController@checkCouponDiscount')->name('front.course.checkCouponDiscount'); // Check Coupon Discount
            Route::post('/{id}/get-free/', 'CourseController@getFreeCourse')->name('front.course.getFreeCourse'); // Check Discount
            Route::get('/{id}/claim-reward', 'ShopController@claimReward')->name('front.course.claim-reward'); // Claim Reward
            Route::post('/{id}/claim-reward', 'ShopController@claimReward')->name('front.course.claim-reward'); // Claim Reward
            Route::get('/share/{share_hash}/checkout', 'ShopController@shareCourseCheckout')->name('front.course.share.checkout');
            Route::post('/share/{share_hash}/checkout', 'ShopController@shareCourseCheckout')->name('front.course.share.checkout');
        });

        Route::group([
            'prefix' => 'publishing-service'
        ], function(){
            Route::get('/calculator', 'PublishingServiceController@serviceCalculator')->name('front.service-calculator');
            Route::get('/thank-you', 'PublishingServiceController@thankyou')->name('publishing-service.thank-you');
            Route::get('/{id}', 'PublishingServiceController@show');
            Route::post('/checkout/validate-form', 'PublishingServiceController@validateForm');
        });


        // Workshop
        Route::group([
            'prefix' => 'workshop'
        ], function(){
            Route::get('/', 'WorkshopController@index')->name('front.workshop.index'); // workshop Listing
            Route::get('/{id}', 'WorkshopController@show')->name('front.workshop.show'); // workshop Details
            Route::get('/{id}/checkout', 'WorkshopController@checkout')->name('front.workshop.checkout'); // Checkout
            Route::post('/{id}/checkout/place_order', 'WorkshopController@place_order')->name('front.workshop.place_order'); // Place Order
        });

        Route::group([
            'prefix' => 'gift'
        ], function(){
            Route::group([
                'prefix' => 'course'
            ], function(){
                Route::get('/', 'GiftController@course')->name('front.gift.course');
                Route::get('/{id}', 'GiftController@courseShow')->name('front.gift.course.show');
                Route::get('/{id}/checkout', 'GiftController@courseCheckout')->name('front.gift.course.checkout');
                Route::post('/{id}/checkout/validate-form', 'GiftController@validateCheckoutForm')->name('front.gift.course.checkout.validate-form');
                Route::post('/{id}/checkout/process-order', 'GiftController@processCourseOrder')->name('front.gift.course.checkout.process-order');
                Route::get('/{id}/thankyou', 'GiftController@thankyou')->name('front.gift.course.thankyou');
            });

            Route::group([
                'prefix' => 'shop-manuscript'
            ], function(){
                Route::get('/', 'GiftController@shopManuscript')->name('front.gift.shop-manuscript');
                Route::get('/{id}/checkout', 'GiftController@shopManuscriptCheckout')->name('front.gift.shop-manuscript.checkout'); // Checkout Shop Manuscript
                Route::post('/{id}/checkout/validate-form', 'GiftController@validateCheckoutForm');
                Route::get('/{id}/thankyou', 'GiftController@thankyou');
            });

            Route::get('/redeem', 'GiftController@showRedeem')->name('front.gift.show-redeem');
            Route::post('/redeem', 'GiftController@redeemGift');
        });

        Route::get('/thankyou', 'ShopController@thankyou')->name('front.shop.thankyou'); // Thank You
        Route::get('/assignment/thankyou', 'HomeController@assignmentThankyou')->name('front.assignment.thankyou'); // Thank You
        Route::get('/thank-you', 'HomeController@thankyou')->name('front.thank-you'); // Thank You


        /*Route::post('/cart/add', 'ShopController@add_to_cart')->name('front.shop.add_to_cart'); // Add To Cart
        Route::post('/cart/remove', 'ShopController@remove_from_cart')->name('front.shop.remove_from_cart'); // Remove From Cart*/


        Route::get('/format_money/{numeric}', 'HomeController@formatMoney');

        Route::get('/payment-plan-options/{id}', 'ShopController@getPaymentPlanOptions');
        Route::get('/payment-modes', 'ShopController@getPaymentModeOptions');

    });


    // Learner Dashboard
    Route::group([
        'middleware' => ['learner', 'logActivity'],
        'namespace' => 'Frontend',
        'prefix' => 'account',
    ], function(){
        Route::get('/dashboard', 'LearnerController@dashboard')->name('learner.dashboard'); // Dashboard Page
        Route::get('/course', 'LearnerController@course')->name('learner.course')->middleware('checkAutoRenewCourses'); // Courses Page
        Route::get('/course/{id}', 'LearnerController@courseShow')->name('learner.course.show'); // Single Course Page
        Route::post('/course/{id}/renew-all', 'LearnerController@courseRenewAll')->name('learner.course.renew-all'); // Single Course Page
        Route::post('/renew-learner-courses', 'LearnerController@renewLearnerCourses')->name('learner.renew-all-courses'); // Renew all the course of the learner in upgrade page
        Route::post('/course-renew/', 'LearnerController@courseRenew')->name('learner.course.renew'); // Single Course Page
        Route::get('/calendar', 'LearnerController@calendar')->name('learner.calendar'); // Calendar Page
        Route::get('/invoice', 'LearnerController@invoice')->name('learner.invoice'); // Invoice Listing Page
        Route::get('/invoice/{id}', 'LearnerController@invoiceShow')->name('learner.invoice.show'); // Invoice Single Page
        Route::get('/change-portal/{portal}', 'LearnerController@changePortal')->name('learner.change-portal'); // Invoice Single Page
        Route::get('/invoice/{fiken_invoice_id}/vipps-payment', 'LearnerController@invoiceVippsPayment')->name('learner.invoice.vipps-payment'); // Invoice Single Page
        Route::get('/order/{id}/download-credited', 'LearnerController@downloadCreditedOrder')->name('learner.order.download-credited');
        Route::get('/order/{id}/download', 'LearnerController@downloadOrder');
        Route::post('/order/{id}/save-company', 'LearnerController@saveCompany');
        Route::post('/redeem-gift', 'LearnerController@redeemGift')->name('learner.redeem-gift');
        Route::post('learner/invoice/{id}/e-faktura', 'LearnerController@vippsEFaktura')->name('learner.invoice.vipps-e-faktura');
        Route::post('learner/set-vipss-efaktura', 'LearnerController@setVippsEFaktura')->name('learner.set-vipps-e-faktura');
        Route::get('/invoice/{id}/download/{type}', 'LearnerController@downloadInvoiceByType')->name('learner.invoice.download-by-type');
        Route::get('/publishing', 'LearnerController@publishing')->name('learner.publishing'); // Publishers House Page
        Route::get('/writing-groups', 'LearnerController@writingGroups')->name('learner.writing-groups'); // Writing Groups Page
        Route::get('/writing-group/{id}', 'LearnerController@writingGroup')->name('learner.writing-group'); // Writing Group Page
        Route::put('/writing-group/{id}', 'LearnerController@writingGroup')->name('learner.update.writing-group'); // Writing Group Page
        Route::get('/competition', 'LearnerController@competition')->name('learner.competition'); // Competitions Page
        Route::get('/private-message', 'LearnerController@privateMessage')->name('learner.private-message'); // Private Message Page
        Route::get('/time-register', 'LearnerController@timeRegister')->name('learner.time-register');
        Route::get('/book-sale', 'LearnerController@bookSale')->name('learner.book-sale');
        Route::get('/book-for-sale/{id}', 'LearnerController@bookForSale')->name('learner.book-for-sale');
        Route::get('/book-sale/list-by-month', 'LearnerController@bookSaleByMonth');
        Route::post('/for-sale-books/save', 'LearnerController@saveForSaleBooks')->name('learner.save-for-sale-books');
        Route::delete('/for-sale-books/{id}/delete', 'LearnerController@deleteForSaleBooks')->name('learner.delete-for-sale-books');

        Route::post('/self-publishing-portal/request', 'LearnerController@requestSelfPublishingPortal')
            ->name('learner.request-self-publishing-portal');
        Route::get('/project', 'LearnerController@project')->name('learner.project');
        Route::post('/project', 'LearnerController@saveProject')->name('learner.save-project');
        Route::post('/project/self-publishing/{id}/upload-manuscript', 'LearnerController@uploadSelfPublishingManuscript')
            ->name('learner.project.self-publishing.upload-manuscript');
        Route::post('/project/other-service/{id}/upload-manuscript/{type}', 'LearnerController@uploadOtherServiceManuscript')
            ->name('learner.project.other-service.upload-manuscript');
        Route::get('/self-publishing/feedback/{id}/download', 'SelfPublishingController@download')
        ->name('learner.self-publishing.feedback.download');
        Route::get('/self-publishing/order', 'SelfPublishingController@selfPublishingOrder')->name('learner.self-publishing.order');
        Route::post('/self-publishing/add-to-cart', 'SelfPublishingController@addToCart')->name('learner.self-publishing.add-to-cart');
        Route::get('/self-publishing/order/checkout', 'SelfPublishingController@checkoutOrder')->name('learner.self-publishing.checkout');
        Route::get('/self-publishing/order/process-checkout', 'SelfPublishingController@processCheckoutOrder')
        ->name('learner.self-publishing.process-checkout');
        Route::post('/self-publishing/order/{id}/save-quote', 'SelfPublishingController@saveQuote')->name('learner.self-publishing.save-quote');
        Route::post('/self-publishing/order/{id}/move-to-order', 'SelfPublishingController@moveToOrder')->name('learner.self-publishing.move-to-order');
        Route::delete('/self-publishing/order/{id}/delete', 'SelfPublishingController@deleteOrder')->name('learner.self-publishing.delete-order');

        // self publishing records not connected to project
        Route::get('/self-publishing/list', 'SelfPublishingController@listSelfPublishing')->name('learner.self-publishing.list');
        Route::get('/self-publishing/copy-editing', 'SelfPublishingController@copyEditing')->name('learner.self-publishing.copy-editing');
        Route::get('/self-publishing/correction', 'SelfPublishingController@correction')->name('learner.self-publishing.correction');

        Route::group([
            'prefix' => 'project/{id}'
        ], function() {
            Route::get('/', 'LearnerController@showProject')->name('learner.project.show');
            Route::get('/service/{service_id}/order', 'LearnerController@orderService')->name('learner.service.order');
            Route::get('/graphic-work', 'LearnerController@projectGraphicWork')->name('learner.project.graphic-work');
            Route::get('/registration', 'LearnerController@projectRegistration')->name('learner.project.registration');
            Route::get('/marketing', 'LearnerController@projectMarketing')->name('learner.project.marketing');
            Route::get('/marketing-plan', 'LearnerController@projectMarketingPlan')->name('learner.project.marketing-plan');
            Route::post('/save-answer', 'LearnerController@saveMarketingPlanQA')->name('learner.project.save-marketing-qa');
            Route::get('/contract', 'LearnerController@projectContract')->name('learner.project.contract');
            Route::get('/invoice', 'LearnerController@projectInvoice')->name('learner.project.invoice');
            Route::get('/storage', 'LearnerController@projectStorage')->name('learner.project.storage');
            Route::get('/storage/{registration_id}/details', 'LearnerController@projectStorageDetails')
            ->name('learner.project.storage-details');
        });

        Route::get('/profile', 'LearnerController@profile')->name('learner.profile'); // Profile Page
        Route::get('/terms', 'LearnerController@terms')->name('learner.terms'); // Terms Page
        Route::get('/course/{course_id}/lesson/{id}', 'LearnerController@lesson')->name('learner.course.lesson'); // Lesson Page
        Route::get('/course/{course_id}/lesson/{id}/download', 'LearnerController@downloadLesson')->name('learner.course.download-lesson'); // Download Lesson Page
        Route::get('/manuscript/{id}', 'LearnerController@manuscriptShow')->name('learner.manuscript.show'); // Manuscript Single Page
        Route::get('/shop-manuscript', 'LearnerController@shopManuscript')->name('learner.shop-manuscript'); // Shop Manuscripts Page
        Route::get('/shop-manuscript/{id}', 'LearnerController@shopManuscriptShow')->name('learner.shop-manuscript.show'); // Shop Manuscript Show Page
        Route::get('shop-manuscript/{id}/download-script/{type}', 'LearnerController@downloadManuscript')->name('learner.shop-manuscript.download');
        Route::get('/shop-manuscript/{id}/feedback/{feedback_id}', 'LearnerController@downloadManuscriptFeedback')->name('learner.shop-manuscript.download-feedback'); // Shop Manuscript Show Page
        Route::get('/workshop', 'LearnerController@workshop')->name('learner.workshop'); // Workshops Page
        Route::post('/coaching-timer/{id}/approve_date', 'LearnerController@approveCoachingDate')->name('learner.coaching-timer.approve_date');
        Route::post('/coaching-timer/{id}/suggest_date', 'LearnerController@suggestCoachingDate')->name('learner.coaching-timer.suggest_date');
        Route::post('/coaching-timer/{id}/help_with', 'LearnerController@updateHelpWith')->name('learner.coaching-timer.help_with');
        Route::post('/coaching-timer/{id}/set-status', 'LearnerController@setCoachingStatus')->name('learner.coaching-timer.set-status');
        Route::post('/course-taken/coaching-timer/add', 'LearnerController@addCoachingSession')->name('learner.course-taken.coaching-timer.add');
        Route::get('/webinar', 'LearnerController@webinar')->name('learner.webinar'); // Webinars Page
        Route::post('/webinar', 'LearnerController@webinar')->name('learner.webinar'); // Webinars Page
        Route::get('/webinar/register/{webinar_key}/{webinar_id}', 'LearnerController@webinarRegister')->name('learner.webinar.register'); // Webinars Page
        Route::get('/course-webinar', 'LearnerController@courseWebinar')->name('learner.course-webinar'); // Course Webinars Page
        Route::post('/course-webinar', 'LearnerController@courseWebinar')->name('learner.course-webinar'); // Course Webinars Page
        Route::get('/assignment', 'LearnerController@assignment')->name('learner.assignment'); // Assignments Page
        Route::post('assignment/{id}/replace_manuscript', 'LearnerController@replaceAssignmentManuscript')->name('learner.assignment.replace_manuscript');
        Route::post('assignment/{id}/delete_manuscript', 'LearnerController@deleteAssignmentManuscript')->name('learner.assignment.delete_manuscript');
        Route::post('assignment/{id}/replace_letter', 'LearnerController@replaceAssignmentLetter')->name('learner.assignment.replace_letter');
        Route::get('/assignment/group/{id}', 'LearnerController@group_show')->name('learner.assignment.group.show'); // Assignment show Page
        Route::get('/assignment/group/{id}/learner-details', 'LearnerController@groupLearnerDetails');
        Route::get('/assignment/group/{id}/show-details', 'LearnerController@groupShowDetails');
        Route::get('/assignment/manuscript/{id}', 'LearnerController@downloadAssignmentGroupManuscript')->name('learner.assignment.manuscript.download'); // Assignment show Page
        Route::get('/assignment/feedback/{id}/download', 'LearnerController@downloadAssignmentGroupFeedback')->name('learner.assignment.feedback.download'); // Download assignment feedback
        Route::get('/assignment/feedback-no-group/{id}/download', 'LearnerController@downloadAssignmentNoGroupFeedback')->name('learner.assignment.no-group-feedback.download'); // Download assignment feedback
        Route::get('/assignment/group/{id}/download-all-feedback', 'LearnerController@downloadAssignmentGroupAllFeedback')->name('learner.assignment.group.feedback.download-all'); // Download all assignment group feedback
        Route::get('/word-written', 'LearnerController@wordWritten')->name('learner.word-written'); // Word Written Page
        Route::post('/word-written', 'LearnerController@wordWritten')->name('learner.word-written'); // Word Written Page
        Route::get('/word-written-goals', 'LearnerController@wordWrittenGoals')->name('learner.word-written-goals'); // Word Written Goals Page
        Route::post('/word-written-goals', 'LearnerController@wordWrittenGoals')->name('learner.word-written-goals'); // Word Written Goals Page
        Route::put('/word-written-goals/{id}/update', 'LearnerController@wordWrittenGoalsUpdate')->name('learner.word-written-goals-update'); // Word Written Goals Page
        Route::delete('/word-written-goals/{id}/delete', 'LearnerController@wordWrittenGoalsDelete')->name('learner.word-written-goals-delete'); // Word Written Goals Page
        Route::get('/word-written-goal/{id}/statistic', 'LearnerController@goalStatistic')->name('learner.goal-statistic');
        Route::get('/search', 'LearnerController@search')->name('learner.account.search'); // Assignment show Page
        Route::get('/lesson/download-document/{id}', 'LearnerController@downloadLessonDocument')->name('learner.lesson.download-lesson-document');
        Route::get('/upgrade', 'LearnerController@upgrade')->name('learner.upgrade');
        Route::get('/upgrade/get-course/{course_taken_id}/package/{package_id}', 'LearnerController@getUpgradeCourse')->name('learner.get-upgrade-course');
        Route::post('/upgrade/course/{id}', 'LearnerController@upgradeCourse')->name('learner.upgrade-course');
        Route::post('/upgrade-course/{id}/validate-form', 'LearnerController@validateUpgradeCourseForm');
        Route::get('/upgrade/get-manuscript/{id}', 'LearnerController@getUpgradeManuscript')->name('learner.get-upgrade-manuscript');
        Route::post('/upgrade-manuscript/{id}/validate-form', 'LearnerController@validateUpgradeManuscriptForm');
        Route::post('/upgrade/manuscript/{id}', 'LearnerController@upgradeManuscript')->name('learner.upgrade-manuscript');
        Route::post('/upgrade/autoRenew', 'LearnerController@setAutoRenewCourses')->name('learner.upgrade-auto-renew');
        Route::get('/upgrade/assignment/{id}', 'LearnerController@getUpgradeAssignment')->name('learner.get-upgrade-assignment'); // Assignment Add on Page
        Route::post('/upgrade/assignment/{id}/validate-form', 'LearnerController@validateUpgradeAssignmentForm');
        Route::post('/upgrade/assignment/{id}', 'LearnerController@upgradeAssignment')->name('learner.upgrade-assignment'); // Assignment Add on Page
        Route::get('/survey/{id}', 'LearnerController@survey')->name('learner.survey'); // Survey Page
        Route::post('/take-survey/{id}', 'LearnerController@takeSurvey')->name('learner.take-survey'); // Survey Page
        Route::get('/notifications', 'LearnerController@notifications')->name('learner.notifications'); // Survey Page
        Route::get('diploma/{id}/download', 'LearnerController@downloadDiploma')->name('learner.download-diploma');
        Route::get('course-certificate/{id}/download', 'LearnerController@downloadCourseCertificate')->name('learner.download-course-certificate');
        Route::get('/other-service/{id}/download/{type}', 'LearnerController@downloadOtherServiceDoc')->name('learner.other-service.download-doc'); // Download assignment feedback
        Route::get('/other-service/download-feedback/{id}', 'LearnerController@downloadOtherServiceFeedback')->name('learner.other-service.download-feedback');
        Route::get('/forum', 'LearnerController@forum')->name('learner.forum');
        Route::post('/webinar-auto-register-update', 'LearnerController@autoRegisterCourseWebinar');


        Route::post('/profile', 'LearnerController@profileUpdate')->name('learner.profile.update'); // Profile Update
        Route::post('/profile/photo', 'LearnerController@profileUpdatePhoto')->name('learner.profile.update-photo'); // Profile Update
        Route::post('/password/update', 'LearnerController@passwordUpdate')->name('learner.password.update'); // Profile Update
        Route::post('/course/take', 'LearnerController@takeCourse')->name('learner.course.take'); // Take Course
        Route::post('/course/{id}/uploadManuscript', 'LearnerController@uploadManuscript')->name('learner.course.uploadManuscript'); // Upload manuscript to course
        Route::post('/shop-manuscript/{id}/comment', 'LearnerController@shopManuscriptPostComment')->name('learner.shop-manuscript.post-comment'); // Shop Manuscript Show Page
        Route::post('/assignment/{id}/upload', 'LearnerController@assignmentManuscriptUpload')->name('learner.assignment.add_manuscript'); // Upload assignment manuscript
        Route::post('/group/{group_id}/learner/{id}/submit_feedba', 'LearnerController@submit_feedback')->name('learner.assignment.group.submit_feedback'); // Submit feedback manuscript
        Route::post('/feedback/{id}/replace_feedback', 'LearnerController@replaceFeedback')->name('learner.assignment.group.replace_feedback'); // Submit feedback manuscript
        Route::post('/feedback/{id}/delete_feedback', 'LearnerController@deleteFeedback')->name('learner.assignment.group.delete_feedback'); // Submit feedback manuscript
        Route::post('/shop-manuscript/{id}/upload', 'ShopManuscriptController@upload_manuscript')->name('learner.shop-manuscript.upload'); // Upload shop manuscript
        Route::post('/shop-manuscript/{id}/upload-synopsis', 'ShopManuscriptController@upload_synopsis')->name('learner.shop-manuscript.upload_synopsis'); // Upload shop manuscript
        Route::post('/shop-manuscript/{id}/update-uploaded-manuscript', 'ShopManuscriptController@updateUploadedManuscript')->name('learner.shop-manuscript.update-uploaded-manuscript'); // update Uploade shop manuscript
        Route::post('/shop-manuscript/{id}/delete-uploaded-manuscript', 'ShopManuscriptController@deleteUploadedManuscript')->name('learner.shop-manuscript.delete-uploaded-manuscript'); // update Uploade shop manuscript
        Route::get('/download/invoice/{id}/credit-note', 'LearnerController@downloadCreditNote')->name('learner.download.credit-note');
        Route::get('/download/time-register-invoice/{id}', 'LearnerController@downloadTimeRegisterInvoice')->name('learner.download.time-register-invoice');
        Route::get('/download/invoice/{url}', 'LearnerController@downloadInvoice')->name('learner.download.invoice')
            ->where('url', '.*'); // to accept url as parameter

        // Pilot Reader
        Route::get('/book-author', 'PilotReaderAuthorController@bookAuthor')->name('learner.book-author'); // Book Reader Author Page
        Route::get('/book-author/create', 'PilotReaderAuthorController@bookAuthorCreate')->name('learner.book-author-create'); // Book Reader Author Create Page
        Route::post('/book-author/create', 'PilotReaderAuthorController@bookAuthorCreate')->name('learner.book-author-create'); // Book Reader Author Create Page
        Route::get('/book-author/book/{id}', 'PilotReaderAuthorController@bookAuthorBook')->name('learner.book-author-book-show'); // Book Reader Author Show Book Page
        Route::get('/book-author/book/{id}/invitation', 'PilotReaderAuthorController@bookAuthorBookInvitation')->name('learner.book-author-book-invitation'); // Book Reader Author Show Invitation Page
        Route::post('/book-author/book/{id}/invitation', 'PilotReaderAuthorController@bookAuthorBookInvitationSend')->name('learner.book-author-book-invitation-send'); // Book Reader Author Send Invitation Page
        Route::post('/book/invite/send','PilotReaderBookSettingsController@authenticatedSendInvitation')->name('account.book.invite.send');
        Route::post('/book-author/book/settings/invite/link/get', 'PilotReaderBookSettingsController@getInvitationLink')->name('learner.book-author.settings.get-invite-link');
        Route::get('/book-author/book/{id}/track-readers', 'PilotReaderAuthorController@bookAuthorTrackReaders')->name('learner.book-author-book-track-readers'); // Book Reader Author Show Invitation Page
        Route::get('/book-author/book/{id}/feedback-list', 'PilotReaderAuthorController@bookAuthorFeedbackList')->name('learner.book-author-book-feedback-list'); // Book Reader Author Show Invitation Page
        Route::get('/book-author/book/{id}/settings', 'PilotReaderBookSettingsController@bookSettings')->name('learner.book-author-book-settings');
        Route::post('/book-author/book/settings/set', 'PilotReaderBookSettingsController@setBookSettings')->name('learner.book-author-set-book-settings');
        Route::post('/book/settings/reading/status/set', 'PilotReaderBookSettingsController@setReadingStatus')->name('learner.book-settings-reading-status-set');
        Route::get('/book-author/book/{id}/reader-feedback-list', 'PilotReaderAuthorController@bookAuthorReaderFeedbackList')->name('learner.book-author-book-reader-feedback-list');
        Route::post('/book-author/book/{id}/validate-email', 'PilotReaderAuthorController@bookAuthorBookInvitationValidateEmail')->name('learner.book-author-book-invitation-validate-email'); // Book Reader Author Send Invitation Page
        Route::get('/book-author/book/{id}/list-invitation/{status}', 'PilotReaderAuthorController@listInvitations')->name('learner.book-author-book-list-invitation'); // Book Reader Author Send Invitation Page
        Route::post('/book-author/book/invitation/cancel', 'PilotReaderAuthorController@cancelInvitation')->name('learner.book-cancel-invitation'); // Book Reader Author Send Invitation Page
        Route::post('/book/settings/reader/role/set', 'PilotReaderBookSettingsController@setReaderRole')->name('learner.book.settings.set-reader-role');
        Route::post('/book-author/book/reader/restore-remove', 'PilotReaderAuthorController@restoreOrRemoveReader');
        Route::get('/book/invitation/{_token}/{action}', 'PilotReaderAuthorController@bookInvitationAction')->name('learner.book-invitation-action'); // Book Reader Author Show Invitation Page
        Route::get('/book/invitation/{id}/decline', 'PilotReaderAuthorController@bookInvitationDecline')->name('learner.book-invitation-decline'); // Book Reader Author Show Invitation Page
        Route::put('/book-author/book/{id}/update', 'PilotReaderAuthorController@bookAuthorBookUpdate')->name('learner.book-author-book-update'); // Book Reader Author Update Book Page
        Route::get('/book-author/book/{id}/chapter/new/{type}', 'PilotReaderAuthorController@bookAuthorBookCreateChapter')->name('learner.book-author-book-create-chapter'); // Book Reader Author Book Chapter Create Page
        Route::post('/book-author/book/{id}/chapter/new/{type}', 'PilotReaderAuthorController@bookAuthorBookCreateChapter')->name('learner.book-author-book-create-chapter'); // Book Reader Author Book Chapter Create Page
        Route::post('/book-author/book/{id}/sort-chapter', 'PilotReaderAuthorController@bookAuthorBookSortChapter')->name('learner.book-author-book-sort-chapter'); // Update the chapter sort
        Route::post('/book/chapter/{id}/update-field', 'PilotReaderAuthorController@bookChapterUpdateField')->name('learner.book-chapter-update-field'); // Update the chapter by field
        Route::get('/book-author/book/{book_id}/chapter/{chapter_id}', 'PilotReaderAuthorController@bookAuthorBookViewChapter')->name('learner.book-author-book-view-chapter'); // Book Reader Author Book Chapter View Page
        Route::get('/book-author/book/{book_id}/chapter/{chapter_id}/edit', 'PilotReaderAuthorController@bookAuthorBookUpdateChapter')->name('learner.book-author-book-update-chapter'); // Book Reader Author Book Chapter Update Page
        Route::put('/book-author/book/{book_id}/chapter/{chapter_id}/edit', 'PilotReaderAuthorController@bookAuthorBookUpdateChapter')->name('learner.book-author-book-update-chapter'); // Book Reader Author Book Chapter Update Page
        Route::delete('/book-author/book/{book_id}/chapter/{chapter_id}/delete', 'PilotReaderAuthorController@bookAuthorBookDeleteChapter')->name('learner.book-author-book-delete-chapter'); // Book Reader Author Book Chapter Update Page
        Route::post('/book-author/book/destroy', 'PilotReaderAuthorController@bookAuthorBookDelete')->name('learner.book-author-book-destroy'); // Book Reader Author Show Book Page
        Route::post('/chapter/feedback/create', 'PilotReaderAuthorController@authorChapterFeedbackCreate')->name('learner.book-author-book-chapter-feedback-create'); // Book Reader Author Book Chapter Note Create
        Route::post('/chapter/feedback/update', 'PilotReaderAuthorController@authorChapterFeedbackUpdate')->name('learner.book-author-book-chapter-feedback-update'); // Book Reader Author Book Chapter Note Create
        Route::post('/chapter/note/create', 'PilotReaderAuthorController@authorChapterNoteCreate')->name('learner.book-author-book-chapter-note-create'); // Book Reader Author Book Chapter Note Create
        Route::post('/chapter/note/update', 'PilotReaderAuthorController@authorChapterNoteUpdate')->name('learner.book-author-book-chapter-note-update'); // Book Reader Author Book Chapter Note Update
        Route::post('/chapter/draft/delete', 'PilotReaderAuthorController@authorChapterDeleteDraft')->name('learner.book-author-book-chapter-draft-delete'); // Book Reader Author Book Chapter Note Update
        Route::get('/chapter/{id}/note/list', 'PilotReaderAuthorController@authorChapterNoteList')->name('learner.book-author-book-chapter-note-list'); // Book Reader Author Import Book Page
        Route::get('/book-author/book/{id}/import', 'PilotReaderAuthorController@bookAuthorBookImport')->name('learner.book-author-book-import'); // Book Reader Author Import Book Page
        Route::post('/book-author/book/{id}/import', 'PilotReaderAuthorController@bookAuthorBookImport')->name('learner.book-author-book-import'); // Book Reader Author Import Book Page
        Route::post('/book-author/chapter/bulk-import', 'PilotReaderAuthorController@saveBulkChapters')->name('learner.bulk-import-chapter'); // Book Reader Author Import Book Page
        Route::post('/book-author/chapter/bookmark/set', 'PilotReaderAuthorController@setBookMark')->name('learner.book.chapter.set-bookmark'); // Book Reader Author Import Book Page
        Route::get('/book-author/chapter/bookmark/get/{id}', 'PilotReaderAuthorController@getBookMark')->name('learner.book.chapter.get-bookmark'); // Book Reader Author Import Book Page
        Route::get('/reader-directory', 'PilotReaderDirectoryController@index')->name('learner.reader-directory.index');
        Route::get('/reader-directory/about', 'PilotReaderDirectoryController@about')->name('learner.reader-directory.about');
        Route::get('/reader-directory/query/sent/list', 'PilotReaderDirectoryController@queryReaderSentList')->name('learner.reader-directory.query-sent-list');
        Route::get('/reader-directory/query/received/list', 'PilotReaderDirectoryController@queryReaderReceivedList')->name('learner.reader-directory.query-received-list');
        Route::post('/reader-directory/query/list', 'PilotReaderDirectoryController@listQueries')->name('learner.reader-directory.query-reader-list');
        Route::post('/reader-directory/query/decision/submit', 'PilotReaderDirectoryController@saveQueryDecision')->name('learner.reader-directory.query-decision-submit');
        Route::post('/reader-directory/list', 'PilotReaderDirectoryController@listReaderProfile')->name('learner.reader-directory.list-profile');
        Route::post('/reader-directory/list/book', 'PilotReaderDirectoryController@listBook')->name('learner.reader-directory.list-book');
        Route::post('/reader-directory/query/sent', 'PilotReaderDirectoryController@queryReader')->name('learner.reader-directory.query-sent');
        Route::get('/pilot-reader/profile', 'PilotReaderAccountController@index')->name('learner.pilot-reader.account.index'); // Book Reader Author Import Book Page
        Route::get('/pilot-reader/profile/preferences/view', 'PilotReaderAccountController@viewUserPreferences')->name('learner.pilot-reader.account.preferences.view'); // Book Reader Author Import Book Page
        Route::post('/pilot-reader/profile/preferences/set', 'PilotReaderAccountController@setUserPreferences')->name('learner.pilot-reader.account.preferences.set'); // Book Reader Author Import Book Page
        Route::get('/pilot-reader/profile/reader', 'PilotReaderAccountController@readerProfile')->name('learner.pilot-reader.account.reader-profile'); // Book Reader Author Import Book Page
        Route::get('/pilot-reader/profile/reader/view', 'PilotReaderAccountController@viewReaderProfile')->name('learner.pilot-reader.account.reader-profile-view'); // Book Reader Author Import Book Page
        Route::post('/pilot-reader/profile/reader/set', 'PilotReaderAccountController@setReaderProfile')->name('learner.pilot-reader.account.reader-profile-set');
        Route::post('/notification/{id}/mark-as-read','LearnerController@markNotificationAsRead')->name('learner.notification.mark-as-read');
        Route::post('/notification/{id}/delete','LearnerController@deleteNotification')->name('learner.notification.delete');
        Route::get('/private-groups','PrivateGroupsController@index')->name('learner.private-groups.index');
        Route::get('/private-groups/{id}','PrivateGroupsController@show')->name('learner.private-groups.show');
        Route::get('/private-groups/{id}/get-data','PrivateGroupsController@getGroupData')->name('learner.private-groups.get-data');
        Route::post('/private-groups/create','PrivateGroupsController@createGroup')->name('learner.private-groups.create');
        Route::post('/private-groups/update','PrivateGroupsController@updateGroup')->name('learner.private-groups.update');
        Route::get('/private-groups/{id}/discussions','PrivateGroupDiscussionsController@index')->name('learner.private-groups.discussion');
        Route::get('/private-groups/{id}/discussion/{discussion_id}','PrivateGroupDiscussionsController@show')->name('learner.private-groups.discussion.show');
        Route::get('/private-groups/discussions/list/{group_id}', 'PrivateGroupDiscussionsController@listDiscussion');
        Route::post('/private-groups/discussions/create', 'PrivateGroupDiscussionsController@create');
        Route::post('/private-groups/discussion/update', 'PrivateGroupDiscussionsController@update');
        Route::get('/private-groups/discussion/replies/get/{id}', 'PrivateGroupDiscussionRepliesController@getDiscussionReplies');
        Route::post('/private-groups/discussion/reply/create', 'PrivateGroupDiscussionRepliesController@createReply');
        Route::post('/private-groups/discussion/reply/update', 'PrivateGroupDiscussionRepliesController@updateReply');
        Route::get('/private-groups/{id}/books','PrivateGroupsController@books')->name('learner.private-groups.books');
        Route::get('/private-groups/shared-book/list/{group_id}','PrivateGroupSharedBookController@listSharedBook');
        Route::post('/private-groups/shared-book/share','PrivateGroupSharedBookController@shareBook');
        Route::post('/private-groups/shared-book/update','PrivateGroupSharedBookController@updateSharedBook');
        Route::post('/private-groups/shared-book/remove','PrivateGroupSharedBookController@destroySharedBook');
        Route::get('/private-groups/shared-book/book/{book_id}','PrivateGroupSharedBookController@getBookDetail');
        Route::post('/private-groups/shared-book/book/become-reader','PrivateGroupSharedBookController@becomeReader');
        Route::get('/private-groups/{id}/preferences','PrivateGroupsController@preferences')->name('learner.private-groups.preferences');
        Route::get('/private-groups/preferences/get/{id}','PrivateGroupsController@viewPreference')->name('learner.private-groups.preferences-get');
        Route::post('/private-groups/preferences/set','PrivateGroupsController@setPreference')->name('learner.private-groups.preferences-set');
        Route::get('/private-groups/{id}/members','PrivateGroupMembersController@index')->name('learner.private-groups.members');
        Route::get('/private-groups/{id}/edit-group','PrivateGroupsController@editGroup')->name('learner.private-groups.edit-group');
        Route::post('/private-groups/member/link/get','PrivateGroupMembersController@getInvitationLink')->name('learner.private-groups.invitation-link.get');
        Route::get('/private-groups/invitation/{status}/{token}', 'PrivateGroupMembersController@confirmInvitation')->name('learner.private-groups.invitation.action');
        Route::post('/private-group/invite/send', 'PrivateGroupMembersController@authenticatedSendInvitation');
        Route::get('/private-groups/{id}/members/invitations/list/{status}','PrivateGroupMembersController@listInvitations');
        Route::post('/private-groups/member/invitation/cancel','PrivateGroupMembersController@cancelInvitation');
        Route::post('/private-groups/member/invitation/remove','PrivateGroupMembersController@removeMember');

        // Profile Email

        Route::group(['prefix' => 'email'], function() {
            Route::get('list', 'LearnerController@listEmails');
            Route::post('primary/set', 'LearnerController@setPrimaryEmail');
            Route::post('destroy', 'LearnerController@removeSecondaryEmail');
            Route::post('confirmation', 'LearnerController@sendEmailConfirmation');
        });

    });

    Route::get('/api/pilotleser/login', 'Frontend\LearnerController@pilotleserLogin');


    // Authentication
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
        'middleware' => 'guest',
    ], function () {
        Route::get('login', 'LoginController@showFrontend')->name('auth.login.show');
        Route::get('login/self-publishing', 'LoginController@showSelfPublishing')->name('auth.login.self-publishing-show');

        Route::post('login', 'LoginController@login')->name('frontend.login.store');
        Route::post('login/self-publishing', 'LoginController@selfPublishingLogin')->name('frontend.login.self-publishing-store');
        Route::post('checkout/login', 'LoginController@checkoutLogin')->name('frontend.login.checkout.store');
        Route::post('register', 'RegisterController@store')->name('frontend.register.store');
        Route::post('passwordreset', 'ResetPasswordController@store')->name('frontend.passwordreset.store');
        Route::get('passwordreset/{token}', 'ResetPasswordController@resetForm')->name('frontend.passwordreset.form');
        Route::post('passwordreset/{token}/update', 'ResetPasswordController@updatePassword')->name('frontend.passwordreset.update');
        Route::post('password-change', 'ResetPasswordController@changePassword')->name('frontend.password-change');

        //Route::get('login/email/{email_hash}', 'LoginController@emailLogin')->name('auth.login.email');
        //Route::get('login/email-normal/{email}', 'LoginController@emailLoginNormal')->name('auth.login.email-normal');

        /*Route::get('login/email-redirect/{email}/{redirect_link}', 'LoginController@emailLoginRedirect')
            ->name('auth.login.emailRedirect');*/

        // socialite route
        Route::get('login/facebook','LoginController@redirectToFacebook')->name('auth.login.facebook');
        Route::get('login/facebook/callback','LoginController@handleFacebookCallback');
        Route::get('login/google','LoginController@redirectToGoogle')->name('auth.login.google');
        Route::get('login/google/callback','LoginController@handleGoogleCallback');
    });

    // without checking middleware
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::get('login/email/{email_hash}', 'LoginController@emailLogin')->name('auth.login.email');
        Route::get('login/email-redirect/{email}/{redirect_link}', 'LoginController@emailLoginRedirect')
            ->name('auth.login.emailRedirect');
        Route::get('login/email-normal/{email}', 'LoginController@emailLoginNormal')->name('auth.login.email-normal');
        Route::get('/vipps-login/{state?}', 'LoginController@vippsLogin')->name('auth.login.vipps');
        Route::get('/vipps-login-redirect', 'LoginController@vippsLoginRedirect');
    });


    //PAYPAL ROUTES
    Route::get('/paypal/{order?}', [
        'name' => 'PayPal Express Checkout',
        'as' => 'app.home',
        'uses' => 'PaypalController@form',
    ]);

    Route::post('/checkout/payment/{order}/paypal', [
        'name' => 'PayPal Express Checkout',
        'as' => 'checkout.payment.paypal',
        'uses' => 'PaypalController@checkout',
    ]);

    Route::get('/paypal/checkout/{order}/{page?}/completed', [
        'name' => 'PayPal Express Checkout',
        'as' => 'paypal.checkout.completed',
        'uses' => 'PaypalController@completed',
    ]);

    Route::get('/paypal/checkout/{order}/cancelled', [
        'name' => 'PayPal Express Checkout',
        'as' => 'paypal.checkout.cancelled',
        'uses' => 'PaypalController@cancelled',
    ]);

    Route::post('/webhook/paypal/{order?}/{env?}', [
        'name' => 'PayPal Express IPN',
        'as' => 'webhook.paypal.ipn',
        'uses' => 'PaypalController@webhook',
    ]);

});


/**
 * Admin Routes
 *
 *
 */
Route::group([
	'domain' => $admin,
], function(){

    Route::group([
        'middleware' => ['admin', 'logActivity'],
        'namespace' => 'Backend'
    ], function(){

        // Dashboard Page
        Route::get('/', 'PageController@dashboard')->name('backend.dashboard');
        Route::get('backend/{id}/download_manuscript', 'PageController@downloadManuscript')->name('backend.download_manuscript');
        Route::get('backend/{id}/download_shop_manuscript', 'PageController@downloadShopManuscript')->name('backend.download_shop_manuscript');
        Route::get('backend/{id}/download_assigned_manuscript', 'PageController@downloadAssignedManuscript')->name('backend.download_assigned_manuscript');
        Route::post('backend/change-password', 'PageController@changePassword')->name('backend.change-password');
        Route::get('/tests', 'PageController@tests');
        Route::get('head-editor/dashboard', 'HeadEditorController@index')->name('admin.head-editor-dashboard')->middleware('headEditor');
        Route::post('/update-expected-finish/{type}/{id}', 'PageController@updateExpectedFinish')->name('backend.update-expected-finish');
        Route::post('/self-publishing/feedback/{id}/approve', 'HeadEditorController@approveSelfPublishingFeedback')->name('head_editor.self-publishing-feedback.approve');
        Route::get('/svea-orders', 'PageController@sveaOrders')->name('admin.svea.orders');
        Route::post('/self-publishing-request/{id}/approve', 'PageController@approveSelfPublishingRequest')->name('admin.self-publishing-portal-request.approve');
        Route::delete('/self-publishing-request/{id}/delete', 'PageController@deleteSelfPublishingRequest')->name('admin.self-publishing-portal-request.destroy');
        Route::get('/learner-not-started-manu', 'PageController@learnerNotStartedManu');
        Route::get('/learner-avail-course/{year}', 'PageController@learnerAvailedCourseYear');
        Route::get('/learner-with-no-paid-records/export', 'PageController@exportLearnersWithNoPaidRecords');
        Route::get('/learner-with-no-paid-records/delete', 'PageController@deleteLearnersWithNoPaidRecords');
        Route::post('/send-email-to-queue', 'PageController@sendEmailToQueue')->name('admin.send-email-to-queue');
        Route::get('/learners/search', 'PageController@searchLearners');

        Route::resource('page_meta','PageMetaController',[
            'except' => ['show', 'create', 'edit'],
            'names' => [
                'index' => 'admin.page_meta.index',
                'store' => 'admin.page_meta.store',
                'update' => 'admin.page_meta.update',
                'destroy' => 'admin.page_meta.delete',
            ],
        ]);

        // Learners Route
        Route::get('learner/list-notes', 'LearnerController@listNotes')->name('admin.learner.list_notes');
        Route::get('learner/generate-password', 'LearnerController@generatePassword');
        Route::post('learner/register', 'LearnerController@registerLearner')->name('admin.learner.register');
        Route::get('learner/export-vipps-efaktura', 'LearnerController@exportLearnerWithVipps');
        Route::get('learner/{id}/email-history', 'LearnerController@learnerEmailHistory')->name('admin.learner.email-history');
        Route::resource('learner', 'LearnerController', [
            'names' => [
                'index' => 'admin.learner.index',
                'show' => 'admin.learner.show',
                'update' => 'admin.learner.update',
                'destroy' => 'admin.learner.delete',
            ],
        ]);
        Route::get('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}', 'LearnerController@shopManuscriptTakenShow')->name('shop_manuscript_taken');
        Route::post('learner/{id}/email', 'LearnerController@sendEmail')->name('admin.shop_manuscript_taken.email'); // Send email
        Route::get('shop-manuscript/{id}/download_synopsis', 'LearnerController@downloadManuscriptSynopsis')->name('admin.learner.download_synopsis');
        Route::post('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}/comment', 'LearnerController@shopManuscriptTakenShowComment')->name('shop_manuscript_taken_comment');
        Route::get('learner/{user_id}/assignment/{id}', 'LearnerController@assignment')->name('admin.learner.assignment');
        Route::post('learner/{user_id}/assignment/{id}/delete-add-one', 'LearnerController@deleteAssignmentAddOn')->name('admin.learner.assignment.delete-add-one');
        Route::post('learner/{user_id}/auto-renew', 'LearnerController@setAutoRenewCourses')->name('admin.learner.update-auto-renew');
        Route::post('learner/{user_id}/could-buy-course', 'LearnerController@setCouldBuyCourse')->name('admin.learner.update-could-buy-course');
        Route::post('learner/{user_id}/webinar-auto-register-update', 'LearnerController@autoRegisterCourseWebinar')->name('admin.learner.webinar-auto-register-update');
        Route::post('learner/{user_id}/update-is-publishing-learner', 'LearnerController@isPublishingLearner');
        Route::get('learner/{user_id}/course-certificate/{certificate_id}/download', 'LearnerController@downloadCourseCertificate')->name('admin.learner.download-course-certificate');
        Route::get('learner/order/{order_id}/editing-services', 'LearnerController@selfPublishingOrders');

        Route::post('shop-manuscript/{id}/update_document', 'LearnerController@updateDocumentShopManuscriptTaken')->name('shop_manuscript_taken.update_document');
        Route::post('shop-manuscript/{id}/synopsis', 'LearnerController@saveSynopsis')->name('shop_manuscript_taken.save_synopsis');


        Route::post('learner/activate_course_taken', 'LearnerController@activate_course_taken')->name('activate_course_taken');
        Route::post('learner/delete_course_taken', 'LearnerController@delete_course_taken')->name('delete_course_taken');
        Route::post('learner/activate_shop_manuscript_taken', 'LearnerController@activate_shop_manuscript_taken')->name('activate_shop_manuscript_taken');
        Route::post('learner/delete_shop_manuscript_taken', 'LearnerController@delete_shop_manuscript_taken')->name('delete_shop_manuscript_taken');
        Route::post('/learner/{id}/add_shop_manuscript', 'LearnerController@addShopManuscript')->name('admin.shop-manuscript.add_learner'); // Shop Manuscript add learner
        Route::post('/learner/{id}/update_workshop_count', 'LearnerController@updateWorkshopCount')->name('admin.learner.update_workshop_count'); // Update workshop count for learner
        Route::post('/workshop-taken/{id}/edit-notes', 'LearnerController@updateWorkshopTakenNotes')->name('admin.learner.workshop-taken.update-notes'); // Update workshop count for learner
        Route::post('/course_taken/{id}/update_started_at', 'LearnerController@updateCourseTakenStartedAt')->name('admin.course_taken.updated_started_at');
        Route::post('/course_taken/{id}/set_availability', 'LearnerController@setCourseTakenAvailability')->name('admin.course_taken.set_availability'); // Shop Manuscript add learner
        Route::post('/course_taken/{id}/send-regret-form', 'LearnerController@sendRegretForm')->name('admin.course_taken.send_regret_form');
        Route::post('/course_taken/{course_taken_id}/allow_lesson_access/{lesson_id}', 'LearnerController@allow_lesson_access')->name('admin.course_taken.allow_lesson_access'); //allow_lesson_access
        Route::post('/course_taken/{course_taken_id}/default_lesson_access/{lesson_id}', 'LearnerController@default_lesson_access')->name('admin.course_taken.default_lesson_access'); //default_lesson_access
        Route::post('/course_taken/{id}/set-expiry-reminder', 'LearnerController@setCourseTakenExpiryReminder')
            ->name('admin.course_taken.set_expiry_reminder');
        Route::post('learner/add_to_workshop', 'LearnerController@addToWorkshop')->name('learner.add_to_workshop');
        Route::post('learner/add_notes/{id}', 'LearnerController@addNotes')->name('learner.add_notes');
        Route::post('/is-manuscript-locked-status', 'LearnerController@updateManuscriptLockedStatus')->name('admin.learner.shop-manuscript-taken-locked-status'); // Manuscript lock status
        Route::get('learner/login_activity/{id}', 'LearnerController@loginActivity')->name('admin.learner.login_activity');
        Route::get('/word-written-goal/{id}/statistic', 'LearnerController@goalStatistic')->name('admin.learner.goal-statistic');
        Route::post('learner/{id}/add-other-service', 'LearnerController@addOtherService')->name('admin.learner.add-other-service');
        Route::post('other-service/{id}/assign-editor/{type}', 'LearnerController@otherServiceAssignEditor')->name('admin.other-service.assign-editor');
        Route::post('other-service/{id}/delete/{type}', 'LearnerController@deleteOtherService')->name('admin.other-service.delete');
        Route::post('coaching-timer/{id}/approve', 'LearnerController@approveCoachingTimer')->name('admin.coaching-timer.approve');
        Route::post('learner/{id}/add-coaching-timer', 'LearnerController@addCoachingTimer')->name('admin.learner.add-coaching-timer');
        Route::post('learner/{id}/add-diploma', 'LearnerController@addDiploma')->name('admin.learner.add-diploma');
        Route::post('diploma/{id}/edit', 'LearnerController@editDiploma')->name('admin.learner.edit-diploma');
        Route::delete('diploma/{id}/delete', 'LearnerController@deleteDiploma')->name('admin.learner.delete-diploma');
        Route::get('diploma/{id}/download', 'LearnerController@downloadDiploma')->name('admin.learner.download-diploma');

        Route::post('learner/invoice/{id}/update-due', 'LearnerController@updateInvoiceDue')->name('admin.learner.invoice.update-due');
        Route::delete('learner/invoice/{id}/delete', 'LearnerController@deleteInvoice')->name('admin.learner.invoice.delete');
        Route::post('learner/invoice/{id}/create-fiken-credit-note', 'LearnerController@addFikenCreditNote')
            ->name('admin.learner.invoice.create-fiken-credit-note');
        Route::post('learner/invoice/{id}/e-faktura', 'LearnerController@vippsEFaktura')->name('admin.learner.invoice.vipps-e-faktura');
        Route::post('learner/{user_id}/set-vipss-efaktura', 'LearnerController@setVippsEFaktura')->name('admin.learner.set-vipps-e-faktura');
        Route::post('learner/{user_id}/send-username-and-password', 'LearnerController@sendUsernameAndPassword')->name('admin.learner.send-username-and-password');
        Route::post('learner/{user_id}/restore-course/{former_course_id}', 'LearnerController@restoreCourse')->name('admin.learner.restore-course');
        Route::post('learner/svea/{order_id}/create-credit-note', 'LearnerController@createSveaCreditNote')->name('admin.learner.svea.create-credit-note');
        Route::post('learner/svea/{order_id}/deliver-order', 'LearnerController@deliverSveaOrder')->name('admin.learner.svea.deliver-order');
        Route::delete('learner/course/{course_taken_id}/delete', 'LearnerController@deleteFromCourse')->name('admin.learner.delete-from-course');
        Route::post('learner/{learner_id}/course/{course_taken_id}/renew', 'LearnerController@renewCourse')->name('admin.learner.renew-course');
        Route::post('learner/{learner_id}/send-email', 'LearnerController@sendLearnerEmail')->name('admin.learner.send-email');
        Route::post('learner/{learner_id}/add-email', 'LearnerController@addSecondaryEmail')->name('admin.learner.add-email');
        Route::post('learner/{email_id}/set-primary-email', 'LearnerController@setPrimaryEmail')->name('admin.learner.set-primary-email');
        Route::delete('learner/{email_id}/delete-secondary-email', 'LearnerController@removeSecondaryEmail')->name('admin.learner.remove-secondary-email');
        Route::post('learner/{learner_id}/save-for-sale-books', 'LearnerController@saveForSaleBooks')->name('admin.learner.save-for-sale-books');
        Route::delete('learner/{learner_id}/for-sale-books/{id}/delete', 'LearnerController@deleteForSaleBooks')->name('admin.learner.delete-for-sale-books');
        Route::post('learner/{learner_id}/save-book-sales', 'LearnerController@saveBookSales')->name('admin.learner.save-book-sales');
        Route::delete('learner/{learner_id}/book-sales/{id}/delete', 'LearnerController@deleteBookSales')->name('admin.learner.delete-book-sales');
        Route::post('learner/{learner_id}/webinar-registrant/{registrant_id}/send-email', 'LearnerController@sendWebinarRegistrantEmail')
            ->name('admin.learner.send-webinar-registrant-email');

        Route::post('learner/{learner_id}/private-message', 'LearnerController@addPrivateMessage')->name('admin.learner.add-private-message');
        Route::put('learner/{learner_id}/private-message/{id}', 'LearnerController@updatePrivateMessage')->name('admin.learner.update-private-message');
        Route::delete('learner/{learner_id}/private-message/{id}/delete', 'LearnerController@deletePrivateMessage')->name('admin.learner.delete-private-message');
        Route::post('learner/{learner_id}/set-preferred-editor', 'LearnerController@setPreferredEditor')->name('admin.learner.set-preferred-editor');
        Route::post('learner/{learner_id}/add-self-publishing', 'LearnerController@addSelfPublishing')->name('admin.learner.add-self-publishing');

        Route::post('task/{id}/finish', 'TaskController@finishTask')->name('admin.task.finish');
        Route::resource('task', 'TaskController', [
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
        Route::resource('course/testimonial', 'CourseTestimonialController', [
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
        Route::post('course/testimonial/{id}/clone','CourseTestimonialController@cloneRecord')->name('admin.course-testimonial.clone');

        // Course Testimonials Route
        Route::resource('course/video/testimonial', 'CourseVideoTestimonialController', [
            'names' => [
                'create' => 'admin.course-video-testimonial.create',
                'store' => 'admin.course-video-testimonial.store',
                'edit' => 'admin.course-video-testimonial.edit',
                'update' => 'admin.course-video-testimonial.update',
                'destroy' => 'admin.course-video-testimonial.destroy',
            ],
        ]);

        // Courses Route
        Route::get('course/get-all-learners', 'CourseController@getAllPaidLearners'); // get all learners that avail a paid course
        Route::get('course/webinars', 'CourseController@allUpcomingWebinars')->name('admin.course.all-upcoming-webinars');
        Route::resource('course', 'CourseController', [
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
        Route::post('course/{id}/update/email', 'CourseController@update_email')->name('admin.course.update.email');
        Route::post('course/{id}/welcome-email/send', 'CourseController@sendWelcomeEmail')->name('admin.course.welcome-email.send');
        Route::post('course/{id}/clone', 'CourseController@clone_course')->name('admin.course.clone');
        Route::post('course/{id}/add_similar_course', 'CourseController@add_similar_course')->name('admin.course.add_similar_course');
        Route::post('course/remove_similar_course/{similar_course_id}', 'CourseController@remove_similar_course')->name('admin.course.remove_similar_course');
        Route::post('/course/learner/add', 'LearnerController@addLearner')->name('learner.course.add.learner'); // Add Learner To Course
        Route::post('/course/learner/remove', 'LearnerController@removeLearner')->name('learner.course.remove.learner'); // Remove Learner From Course
        Route::post('/course-status', 'CourseController@updateStatus')->name('learner.course.status'); // Courses Page
        Route::post('/course-for-sale', 'CourseController@updateForSaleStatus')->name('learner.course.for-sale-status'); // Courses For Sale Status
        Route::post('/course-is-free', 'CourseController@updateIsFreeStatus')->name('learner.course.is-free-status'); // Courses For Sale Status
        Route::post('/course/{id}/send-email-to-learners', 'CourseController@sendEmailToLearners')->name('learner.course.send-email-to-learners'); // Add Learner To Course
        Route::post('/course/{id}/not-started-reminder', 'CourseController@notStartedCourseReminder')->name('learner.course.not-started-reminder');
        Route::post('/course/{id}/set-course-taken-end-date', 'CourseController@setCourseTakenEndDate')->name('learner.course.set-end-date');
        Route::get('/course/{id}/learner-list-excel/{type?}', 'CourseController@learnerListExcel')->name('learner.course.learner-list-excel'); // Add Learner To Course
        Route::get('/course/{id}/learner-active-list-excel', 'CourseController@learnerActiveListExcel')->name('learner.course.learner-active-list-excel'); // Add Learner To Course
        Route::post('/course/{id}/expirationReminder', 'CourseController@expirationReminder')->name('admin.course.expiration-reminder');
        Route::post('/course/{id}/add-learners-to-webinars', 'CourseController@addLearnersToWebinars')->name('admin.course.add-learners-to-webinars');
        Route::post('/course/{id}/certificate-dates', 'CourseController@updateCertificateDates')->name('admin.course.update-certificate-dates');
        Route::get('course/resend-welcome-email/{package_id}/{user_id}/{course_taken_id}', 'CourseController@resendWelcomeEmailToUser');
        //Route::get('course/{id}/certificate', 'CourseController@certificate')->name('admin.course.certificate');
        Route::get('course/{id}/download-certificate', 'CourseController@downloadCertificate')->name('admin.course.download-certificate-template');
        Route::post('course/{id}/save-certificate-template', 'CourseController@saveCertificateTemplate')->name('admin.course.save-certificate-template');
        Route::get('/course/{id}/export-hidden-webinars', 'CourseController@exportHiddenWebinars');
        Route::get('/course/export-former-learners/bulk', 'CourseController@exportFormerLearners');
        Route::get('/course/export-current-learners/bulk', 'CourseController@exportCurrentLearners');
        Route::get('/course/application/{id}/details', 'CourseController@applicationDetails');
        Route::get('/course/application/{id}/download', 'CourseController@applicationDownload')->name('admin.course.application.download');
        Route::post('/course/application/{id}/approve', 'CourseController@applicationApprove')->name('admin.course.application.approve');
        Route::delete('/course/application/{id}/delete', 'CourseController@applicationDelete')->name('admin.course.application.delete');
        Route::post('course-taken/{id}/update-can-receive-email', 'CourseController@canReceiveEmailUpdate');
        Route::post('course-taken/{id}/update-in-facebook-group', 'CourseController@inFacebookGroupUpdate');
        Route::post('course-taken/{id}/exclude-in-registration', 'CourseController@excludeInScheduledRegistration');

        Route::get('/shareable-course/get-package/{course_id}', 'ShareableCourseController@getCoursePackage');
        Route::resource('shareable-course', 'ShareableCourseController', [
            'except' => ['crete', 'show', 'edit'],
            'names' => [
                'index' => 'admin.shareable-course.index',
                'store' => 'admin.shareable-course.store',
                'update' => 'admin.shareable-course.update',
                'destroy' => 'admin.shareable-course.destroy',
            ],
        ]);

        // Email Out Route
        Route::post('/course/{course_id}/email-out/{email_out}/send-email', 'EmailOutController@sendEmailToLearners')
            ->name('admin.email-out.send-email');
        Route::resource('/course/{course_id}/email-out', 'EmailOutController', [
            'except' => 'show',
            'names' => [
                'create' => 'admin.email-out.create',
                'store' => 'admin.email-out.store',
                'edit' => 'admin.email-out.edit',
                'update' => 'admin.email-out.update',
                'destroy' => 'admin.email-out.destroy',
            ],
        ]);

        // Course Reward Coupon Route
        Route::resource('/course/{course_id}/reward-coupons', 'CourseRewardCouponController', [
            'except' => 'show',
            'names' => [
                'create' => 'admin.reward-coupons.create',
                'store' => 'admin.reward-coupons.store',
                'edit' => 'admin.reward-coupons.edit',
                'update' => 'admin.reward-coupons.update',
                'destroy' => 'admin.reward-coupons.destroy',
            ],
        ]);

        Route::get('/course/{course_id}/package/{package_id}/certificate', 'CourseController@certificate')
        ->name('admin.package.certificate');
        Route::get('/course/{course_id}/package/{package_id}/download-certificate', 'CourseController@downloadPackageCertificate')
        ->name('admin.package.download-certificate-template');
        Route::post('/course/{course_id}/package/{package_id}/save-certificate-template', 'CourseController@savePackageCertificateTemplate')
        ->name('admin.package.save-certificate-template');

        Route::post('/course/{course_id}/reward-coupons/multiple-store', 'CourseRewardCouponController@multipleStore')
            ->name('admin.reward-coupons.multiple-store');

        Route::get('/course/{course_id}/reward-coupons/export-to-text', 'CourseRewardCouponController@exportToText')
            ->name('admin.reward-coupons.export-to-text');

        Route::resource('course/{id}/discount', 'CourseDiscountController', [
            'except' => ['show', 'create', 'edit'],
            'names' => [
                'index' => 'admin.course-discount.index',
                'store' => 'admin.course-discount.store',
                'update' => 'admin.course-discount.update',
                'destroy' => 'admin.course-discount.destroy',
            ],
        ]);


        // Free Courses Route
        Route::resource('free-course', 'FreeCourseController', [
            'except' => ['show', 'create', 'edit'],
            'names' => [
                'index' => 'admin.free-course.index',
                'store' => 'admin.free-course.store',
                'update' => 'admin.free-course.update',
                'destroy' => 'admin.free-course.destroy',
            ],
        ]);

        Route::post('free-course/webinar', 'FreeCourseController@storeWebinar')->name('admin.free-webinar.store');
        Route::put('free-course/webinar/{id}/update', 'FreeCourseController@updateWebinar')->name('admin.free-webinar.update');
        Route::delete('free-course/webinar/{id}/delete', 'FreeCourseController@deleteWebinar')->name('admin.free-webinar.destroy');
        Route::post('free-course/webinar/{id}/presenter/store', 'FreeCourseController@storeWebinarPresenter')->name('admin.free-webinar.presenter.store');
        Route::put('free-course/webinar/{webinar_id}/presenter/{id}/update', 'FreeCourseController@updateWebinarPresenter')->name('admin.free-webinar.presenter.update');
        Route::delete('free-course/webinar/{webinar_id}/presenter/{id}/delete', 'FreeCourseController@deleteWebinarPresenter')->name('admin.free-webinar.presenter.delete');




        // Package Route
        Route::resource('course/{course_id}/package', 'PackageController', [
            'names' => [
                'store' => 'admin.course.package.store',
                'update' => 'admin.course.package.update',
                'destroy' => 'admin.course.package.destroy',
            ],
        ]);

        Route::post('course/{course_id}/package/{package_id}/include-coaching', 'PackageController@includeCoaching')
            ->name('admin.course.package.include-coaching');



        // Package Course  Route
        Route::resource('package_course', 'PackageCourseController', [
            'except' => ['show', 'index', 'edit', 'update', 'create'],
            'names' => [
                'store' => 'admin.package_course.store',
                'destroy' => 'admin.package_course.destroy'
            ]
        ]);



        // Workshop Route
        Route::resource('workshop', 'WorkshopController', [
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
        Route::post('workshop/{workshop_taken_id}/attendee/{attendee_id}', 'WorkshopController@removeAttendee')->name('admin.workshop.remove_attendee');
        Route::post('workshop/{id}/download_pdf', 'WorkshopController@downloadAttendees')->name('admin.workshop.download_pdf');
        Route::get('workshop/{id}/download_excel', 'WorkshopController@downloadAttendeesExcel')->name('admin.workshop.download_excel');
        Route::post('workshop/{id}/add-to-course', 'WorkshopController@addLearnersToCourse')->name('admin.workshop.add-learners-to-course');
        Route::post('workshop/{id}/send_email', 'WorkshopController@sendEmailToAttendees')->name('admin.workshop.send_email');
        Route::get('workshop/{id}/view_email_attendees', 'WorkshopController@viewEmailLogAttendees')->name('admin.workshop.send_email_log');
        Route::post('/workshop-status', 'WorkshopController@updateStatus')->name('admin.workshop.status'); // Courses Page
        Route::post('/workshop-for-sale', 'WorkshopController@updateForSaleStatus')->name('admin.workshop.for-sale-status'); // Courses For Sale Status
        Route::post('workshop/{id}/update/email', 'WorkshopController@update_email')->name('admin.workshop.update.email');

        // Workshop Presenter Route
        Route::resource('workshop/{workshop_id}/workshop-presenter', 'WorkshopPresenterController', [
            'except' => ['index', 'show', 'create', 'edit'],
            'names' => [
                'store' => 'admin.course.workshop-presenter.store',
                'update' => 'admin.course.workshop-presenter.update',
                'destroy' => 'admin.course.workshop-presenter.destroy',
            ],
        ]);


        // Workshop Menu Route
        Route::resource('workshop/{workshop_id}/workshop-menu', 'WorkshopMenuController', [
            'except' => ['index', 'show', 'create', 'edit'],
            'names' => [
                'store' => 'admin.course.workshop-menu.store',
                'update' => 'admin.course.workshop-menu.update',
                'destroy' => 'admin.course.workshop-menu.destroy',
            ],
        ]);


        // Lessons Route
        Route::resource('/course/{course_id}/lesson', 'LessonController', [
            'except' => 'show',
            'names' => [
                'create' => 'admin.lesson.create',
                'store' => 'admin.lesson.store',
                'edit' => 'admin.lesson.edit',
                'update' => 'admin.lesson.update',
                'destroy' => 'admin.lesson.destroy',
            ],
        ]);
        Route::post('/lesson/save_order', 'LessonController@save_order')->name('admin.lesson.save_order'); // Save lesson order
        Route::get('/lesson/download-document/{id}', 'LessonController@downloadLessonDocument')->name('admin.lesson.download-lesson-document');
        Route::delete('/lesson/delete-document/{id}', 'LessonController@deleteLessonDocument')->name('admin.lesson.delete-lesson-document');
        Route::delete('/lesson/delete-whole-file/{id}', 'LessonController@deleteLessonFile')->name('admin.lesson.delete-lesson-whole-file');
        Route::post('/lesson/{id}/add-content', 'LessonController@addContent')->name('admin.lesson.add_content'); // Save lesson order
        Route::get('/lesson/{id}/get-lesson-content', 'LessonController@getLessonContent')->name('admin.lesson.get_lesson_content'); // Save lesson order
        Route::post('/lesson-content/{id}/delete-lesson-content', 'LessonController@deleteLessonContent')->name('admin.lesson.delete_lesson_content'); // Save lesson order

        // Lessons Route
        Route::get('/admin/export_nearly_expired_courses', 'AdminController@exportNearlyExpiredCourses')->name('admin.admin.export_nearly_expired_courses');
        Route::post('/admin/{id}/page-access', 'AdminController@pageAccess')->name('admin.admin.page-access');
        Route::post('/admin-status', 'AdminController@adminStatus')->name('admin.admin.status');
        Route::post('/admin/type-change', 'AdminController@adminTypeChange');
        Route::get('/admin/clear/cache', 'AdminController@clearCache')->name('admin.clear.cache');
        Route::resource('/admin', 'AdminController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.admin.index',
                'show' => 'admin.admin.show',
                'store' => 'admin.admin.store',
                'update' => 'admin.admin.update',
                'destroy' => 'admin.admin.destroy',
            ],
        ]);
        Route::post('/save-staff/{id?}', 'AdminController@saveStaff')->name('admin.staff.save');
        Route::delete('/delete-staff/{id?}', 'AdminController@deleteStaff')->name('admin.staff.delete');
        Route::get('/fiken-redirect', 'AdminController@fikenRedirect')->name('admin.fiken.redirect');

        Route::post('/contract/{id}/send-contract', 'ContractController@sendContract')
            ->name('admin.contract.send-contract');
        Route::post('/contract/template/save/{id?}', 'ContractController@saveContractTemplate')->name('admin.contract-template.save');
        Route::delete('/contract/template/delete/{id?}', 'ContractController@deleteContractTemplate')->name('admin.contract-template.delete');
        Route::post('/contract/{id}/sign', 'ContractController@signContract')->name('admin.contract.sign');
        Route::post('/contract/{id}/status', 'ContractController@contractStatus')->name('admin.contract.status');
        Route::get('/contract/{id}/download-pdf', 'ContractController@downloadPDF')->name('admin.contract.download-pdf');
        Route::resource('/contract','ContractController',[
            'names' => [
                'index' => 'admin.contract.index',
                'create' => 'admin.contract.create',
                'show' => 'admin.contract.show',
                'store' => 'admin.contract.store',
                'edit' => 'admin.contract.edit',
                'update' => 'admin.contract.update',
                'destroy' => 'admin.contract.destroy'
            ]
        ]);

        Route::resource('/email','EmailController',[
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.email.index',
                'show' => 'admin.email.show',
                'store' => 'admin.email.store',
                'update' => 'admin.email.update',
                'destroy' => 'admin.email.destroy',
            ]
        ]);

        Route::post('email/login', 'EmailController@login')->name('admin.email.login');
        Route::get('email/move/{id}', 'EmailController@move')->name('admin.email.move');
        Route::get('email/delete/{id}', 'EmailController@delete')->name('admin.email.delete');
        Route::post('email/forward/{id}', 'EmailController@forward')->name('admin.email.forward');
        Route::post('email/reply', 'EmailController@reply')->name('admin.email.reply');



        // Videos Route
        Route::resource('video', 'VideoController', [
            'names' => [
                'store' => 'admin.video.store',
                'update' => 'admin.video.update',
                'destroy' => 'admin.video.destroy',
            ],
        ]);



        // Webinar Route
        Route::resource('webinar', 'WebinarController', [
            'except' => ['create', 'edit', 'show', 'index'],
            'names' => [
                'store' => 'admin.webinar.store',
                'update' => 'admin.webinar.update',
                'destroy' => 'admin.webinar.destroy',
            ],
        ]);
        Route::post('webinar/{id}/delete', 'WebinarController@destroy')->name('admin.webinar.delete');
        Route::put('webinar/{id}/make-replay', 'WebinarController@makeReplay')->name('admin.webinar.make-replay');
        Route::post('webinar/{id}/set-schedule', 'WebinarController@setSchedule')->name('admin.webinar.schedule');
        Route::post('webinar/{id}/update-field', 'WebinarController@updateField')->name('admin.webinar.update-field');
        Route::post('webinar/{id}/course/{course_id}/email-out', 'WebinarController@webinarEmailOut')->name('admin.webinar.email-out');
        Route::post('webinar/{id}/course/{course_id}/auto-register-learners', 'WebinarController@autoRegisterLearnersToWebinar')
            ->name('admin.webinar.auto-register-learners');
        Route::get('/webinar/{id}/registrant/list', 'WebinarController@registrantList');
        Route::delete('/webinar/registrant/{id}/delete', 'WebinarController@removeRegistrant')
            ->name('admin.webinar.remove-registrant');

        // Webinar Presenter Route
        Route::resource('webinar/{webinar_id}/presenter', 'WebinarPresenterController', [
            'except' => ['index', 'show', 'create', 'edit'],
            'names' => [
                'store' => 'admin.webinar.webinar-presenter.store',
                'update' => 'admin.webinar.webinar-presenter.update',
                'destroy' => 'admin.webinar.webinar-presenter.destroy',
            ],
        ]);

        // Webinar Editor Route
        Route::post('storeWebinarEditor/{webinar_id}', 'WebinarEditorController@store')->name('admin.webinar.webinar-editor.store');
        Route::post('updateWebinarEditor/{id}', 'WebinarEditorController@update')->name('admin.webinar.webinar-editor.update');
        Route::delete('deleteWebinarEditor/{id}', 'WebinarEditorController@deleteEditor')->name('admin.webinar.webinar-editor.delete');

        // Assignments Route
        Route::resource('/assignment', 'AssignmentController', [
            'except' => ['show', 'create', 'edit', 'store', 'update', 'destroy'],
            'names' => [
                'index' => 'admin.assignment.index',
            ],
        ]);

        Route::get('/course/{course_id}/assignment/{assignment_id}/list-manuscripts-without-editor', 
        'AssignmentController@listManuscriptsWithoutEditor');
        Route::post('/course/{course_id}/assignment/{assignment_id}/assign-editor-to-manuscripts', 
        'AssignmentController@assignEditorToManuscripts')->name('admin.assignment.assign-editor-to-manuscripts');

        Route::resource('course/{course_id}/assignment', 'AssignmentController', [
            'except' => ['index', 'create', 'edit'],
            'names' => [
                'show' => 'admin.assignment.show',
                'store' => 'admin.assignment.store',
                'update' => 'admin.assignment.update',
                'destroy' => 'admin.assignment.destroy',
            ],
        ]);

        Route::get('/power-office/self-publishing/{publishing_id}/add-to-po', 'PowerOfficeController@addSelfPublshingToPowerOffice')
        ->name('admin.power-office.self-publishing.add-to-po');
        Route::post('/power-office/self-publishing/{publishing_id}/add-to-po', 'PowerOfficeController@addSelfPublshingToPowerOffice')
        ->name('admin.power-office.self-publishing.add-to-po');
        Route::get('/power-office/self-publishing/{publishing_id}/invoice/{invoice_id}/view', 
            'PowerOfficeController@selfPublishingPowerOfficeInvoice')
            ->name('admin.power-office.self-publishing.view-po-order');
        Route::get('/power-office/{id}/download', 'PowerOfficeController@downloadInvoice')
            ->name('admin.power-office.download');

        Route::post('/project/activity/save', 'ProjectController@saveActivity');
        Route::delete('/project/activity/{id}/delete', 'ProjectController@deleteActivity');
        Route::post('/project/{id}/notes/save', 'ProjectController@saveNote');
        Route::post('/project/{id}/learner/add', 'ProjectController@addLearner');
        Route::post('/project/{id}/whole-book/save', 'ProjectController@saveWholeBook');
        Route::post('/project/whole-book/{id}/update-status', 'ProjectController@saveWholeBookStatus');
        Route::delete('/project/whole-book/{id}/delete', 'ProjectController@deleteWholeBook');
        Route::get('/project/{id}/whole-book/{whole_book}/download', 'ProjectController@downloadWholeBook');
        Route::delete('/project/book-critique/{id}/delete', 'ProjectController@deleteBookCritique');
        Route::post('/project/book-critique/{id}/feedback', 'ProjectController@saveBookCritiqueFeedback');
        Route::post('/project/{id}/book/save', 'ProjectController@saveBook');
        Route::delete('/project/book/{id}/delete', 'ProjectController@deleteBook');
        Route::post('/project/{id}/book-pictures/save', 'ProjectController@saveBookPicture')->name('admin.project.save-picture');
        Route::delete('/project/book-pictures/{id}/delete', 'ProjectController@deleteBookPicture')->name('admin.project.delete-picture');
        Route::post('/project/{id}/book-formatting/save', 'ProjectController@saveBookFormatting')->name('admin.project.save-book-formatting');
        Route::post('/project/book-formatting/{id}/approve-feedback', 'ProjectController@approveBookFormattingFeedback')
        ->name('admin.project.book-formatting.approve-feedback');
        Route::delete('/project/book-formatting/{id}/delete', 'ProjectController@deleteBookFormatting')->name('admin.project.delete-book-formatting');
        Route::post('/project/{id}/add-other-service', 'ProjectController@addOtherService')->name('admin.project.add-other-service');
        Route::get('/project/{id}/graphic-work', 'ProjectController@graphicWork')->name('admin.project.graphic-work');
        Route::post('/project/{id}/graphic-work/save', 'ProjectController@saveGraphicWork')->name('admin.project.save-graphic-work');
        Route::delete('/project/{id}/graphic-work/{graphic_work_id}/delete', 'ProjectController@deleteGraphicWork')->name('admin.project.delete-graphic-work');
        Route::get('/project/{id}/cover/{cover_id}', 'ProjectController@cover')->name('admin.project.cover.show');
        Route::get('/project/{id}/book-format/{format_id}', 'ProjectController@bookFormat')->name('admin.project.book-format.show');
        Route::get('/project/{id}/registration', 'ProjectController@registration')->name('admin.project.registration');
        Route::post('/project/{id}/registration/save', 'ProjectController@saveRegistration')->name('admin.project.save-registration');
        Route::delete('/project/{id}/registration/{registration_id}/delete', 'ProjectController@deleteRegistration')->name('admin.project.delete-registration');
        Route::get('/project/{id}/marketing', 'ProjectController@marketing')->name('admin.project.marketing');
        Route::post('/project/{id}/marketing/save', 'ProjectController@saveMarketing')->name('admin.project.save-marketing');
        Route::delete('/project/{id}/marketing/{marketing_id}/delete', 'ProjectController@deleteMarketing')->name('admin.project.delete-marketing');
        Route::get('/project/{id}/marketing-plan', 'ProjectController@marketingPlan')->name('admin.project.marketing-plan');
        Route::get('/project/{id}/contract', 'ProjectController@contract')->name('admin.project.contract');
        Route::post('/project/{id}/contract', 'ProjectController@storeContract')->name('admin.project.contract-store');
        Route::post('/project/{id}/contract/upload', 'ProjectController@uploadContract')->name('admin.project.contract-upload');
        Route::post('/project/{id}/contract/{contract_id}/signed-upload', 'ProjectController@uploadSignedContract')
            ->name('admin.project.contract-signed-upload');
        Route::get('/project/{id}/contract/create', 'ProjectController@createContract')->name('admin.project.contract-create');
        Route::get('/project/{id}/contract/{contract_id}/edit', 'ProjectController@editContract')->name('admin.project.contract-edit');
        Route::put('/project/{id}/contract/{contract_id}/update', 'ProjectController@updateContract')->name('admin.project.contract-update');
        Route::get('/project/{id}/contract/{contract_id}', 'ProjectController@showContract')->name('admin.project.contract-show');
        Route::get('/project/{id}/invoice', 'ProjectController@invoice')->name('admin.project.invoice');
        Route::post('/project/{id}/invoice/save', 'ProjectController@saveInvoice')->name('admin.project.invoice.save');
        Route::delete('/project/{id}/invoice/{invoice_id}/delete', 'ProjectController@deleteInvoice')->name('admin.project.invoice.delete');
        Route::post('/project/{id}/manual-invoice/save', 'ProjectController@saveManualInvoice')->name('admin.project.manual-invoice.save');
        Route::delete('/project/{id}/manual-invoice/{invoice_id}/delete', 'ProjectController@deleteManualInvoice')->name('admin.project.manual-invoice.delete');
        Route::get('/project/{id}/storage', 'ProjectController@storage')->name('admin.project.storage');
        Route::post('/project/{id}/storage', 'ProjectController@storage');
        Route::get('/project/{id}/storage/{registration_id}/details', 'ProjectController@storageDetails')
            ->name('admin.project.storage-details');
        Route::post('/project/{id}/storage/save-book', 'ProjectController@saveStorageBook')->name('admin.project.storage.save-book');
        Route::delete('/project/{id}/storage/delete', 'ProjectController@deleteStorageBook')->name('admin.project.storage.delete-book');
        Route::post('project/{id}/storage/save-book-sales', 'ProjectController@saveBookSales')->name('admin.project.storage.save-book-sales');
        Route::post('project/{id}/storage/import-book-sales', 'ProjectController@importBookSales')
            ->name('admin.project.storage.import-book-sales');
        Route::delete('project/storage/book-sales/{book_id}/delete', 'ProjectController@deleteBookSales')->name('admin.project.storage.delete-book-sales');
        Route::post('/project/book/{id}/storage/save-details', 'ProjectController@saveStorageBookDetails')->name('admin.project.storage.save-details');
        Route::post('/project/book/{id}/storage/save-various', 'ProjectController@saveStorageVarious')->name('admin.project.storage.save-various');
        Route::post('/project/book/{id}/storage/save-distribution-cost', 'ProjectController@saveDistributionCost')
            ->name('admin.project.storage.save-distribution-cost');
        Route::get('/project/book/{id}/storage/sales-details', 'ProjectController@storageSalesDetails');
        Route::delete('/project/book/distribution-cost/{dist_id}/delete', 'ProjectController@deleteDistributionCost')
            ->name('admin.project.storage.delete-distribution-cost');
        Route::post('/project/book/{id}/storage/save-sales', 'ProjectController@saveStorageSales')->name('admin.project.storage.save-sales');
        Route::delete('/project/storage/{id}/delete-sales', 'ProjectController@deleteStorageSales')->name('admin.project.storage.delete-sales');
        Route::get('/project/{id}/e-book', 'ProjectController@ebook')->name('admin.project.ebook');
        Route::post('/project/{id}/e-book/save', 'ProjectController@saveEbook')->name('admin.project.save-ebook');
        Route::delete('/project/{id}/e-book/{ebook_id}/delete', 'ProjectController@deleteEbook')->name('admin.project.delete-ebook');
        Route::get('/project/{id}/audio', 'ProjectController@audio')->name('admin.project.audio');
        Route::post('/project/{id}/audio/save', 'ProjectController@saveAudio')->name('admin.project.save-audio');
        Route::delete('/project/{id}/audio/{audio_id}/delete', 'ProjectController@deleteAudio')->name('admin.project.delete-audio');
        Route::get('/project/{id}/print', 'ProjectController@print')->name('admin.project.print');
        Route::post('/project/{id}/print/save', 'ProjectController@savePrint')->name('admin.project.save-print');
        Route::get('/project/{id}/notes', 'ProjectController@showNotes')->name('admin.project.notes');
        Route::get('/project', 'ProjectController@index')->name('admin.project.index');
        Route::post('/project/save', 'ProjectController@saveProject');
        Route::get('/project/{id}', 'ProjectController@show')->name('admin.project.show');
        Route::delete('/project/{id}/delete', 'ProjectController@deleteProject');

        Route::get('/storage-books', 'StorageBookController@index')->name('admin.storage-books.index');

        Route::post('generate_assignment_group/{id}', 'AssignmentController@generateGroup')->name('assignment.generate_assignment_group');
        Route::post('assignment/{id}/uploadManuscript', 'AssignmentController@uploadManuscript')->name('assignment.group.upload_manuscript');
        Route::post('assignment/{id}/add-on-for-learner', 'AssignmentController@addOnForLearner')->name('assignment.add-on-for-learner');
        Route::post('assignment/{id}/update-submission-date', 'AssignmentController@updateSubmissionDate')->name('assignment.update-submission-date');
        Route::post('assignment/{id}/update-available-date', 'AssignmentController@updateAvailableDate')->name('assignment.update-available-date');
        Route::post('assignment/{id}/update-max-words', 'AssignmentController@updateMaxWords')->name('assignment.update-max-words');
        Route::post('assignment_manuscript/{id}/delete', 'AssignmentController@deleteManuscript')->name('assignment.group.delete_manuscript');
        Route::post('assignment_manuscript/{id}/move', 'AssignmentController@moveManuscript')->name('assignment.group.move_manuscript');
        Route::post('assignment_manuscript/{id}/set_grade', 'AssignmentController@setGrade')->name('assignment.group.set_grade');
        Route::post('assignment_manuscript/{id}/replace_manuscript', 'AssignmentController@replaceManuscript')->name('assignment.group.replace_manuscript');
        Route::post('assignment_manuscript/lock-status', 'AssignmentController@updateLockStatus')->name('assignment.group.lock-status'); // Courses For Sale Status
        Route::post('assignment_manuscript/dashboard-status', 'AssignmentController@updateDashboardStatus')->name('assignment.group.dashboard-status'); // Courses For Sale Status
        Route::post('assignment_manuscript/{id}/update_manu_types', 'AssignmentController@updateTypes')->name('assignment.group.update_manu_types');
        Route::post('assignment_manuscript/{id}/assignEditor', 'AssignmentController@assignManuscriptEditor')->name('assignment.group.assign_manu_editor');
        Route::delete('assignment_manuscript/{id}/remove-editor', 'AssignmentController@removeManuscriptEditor')->name('assignment.group.remove_manu_editor');
        Route::post('assignment_manuscript/{id}/edit-dates', 'AssignmentController@assignManuscriptEditDates')->name('backend.assignment.edit-dates');
        Route::post('assignment_manuscript/{id}/download_editor_manuscript', 'AssignmentController@downloadEditorManuscript')->name('assignment.group.download_editor_manuscript');
        Route::post('assignment_manuscript/{id}/learner/{learner_id}/feedback', 'AssignmentController@manuscriptFeedbackNoGroup')->name('assignment.group.manuscript-feedback-no-group');
        Route::post('assignment_manuscript/{id}/send-email-to-user', 'AssignmentController@emailManuscriptUser')->name('assignment.send-email-to-manuscript-user');
        Route::post('assignment_manuscript/update-feedback/{id}', 'AssignmentController@manuscriptFeedbackNoGroupUpdate')->name('assignment.group.manuscript-feedback-no-group-update');
        Route::post('assignment_manuscript/update-availability/{id}', 'AssignmentController@manuscriptFeedbackNoGroupUpdateAvailability')->name('assignment.group.manuscript-feedback-no-group-update-availability');
        Route::post('assignment_manuscript/update-join-group/{id}', 'AssignmentController@updateJoinGroup')->name('assignment.update-join-group');
        Route::get('assignment/{id}/download', 'AssignmentController@downloadManuscript')->name('assignment.group.download_manuscript');
        Route::get('assignment-manuscript/{id}/download-letter', 'AssignmentController@downloadManuscriptLetter')->name('assignment.manuscript.download_letter');
        Route::get('assignment/{id}/downloadAll', 'AssignmentController@downloadAllManuscript')->name('assignment.group.download_all_manuscript');
        Route::get('assignment/{id}/exportEmailList', 'AssignmentController@exportEmailList')->name('assignment.group.export_email_list');
        Route::get('assignment/{id}/export-all-learners-include-add-on-learners', 'AssignmentController@exportLearnersIncludeAddOnLearners')
            ->name('assignment.export-all-learners-include-add-on-learners');
        Route::post('assignment/{id}/send-email-to-list', 'AssignmentController@sendEmailToList')->name('assignment.group.send-email-to-list');
        Route::get('assignment/{id}/generate-doc', 'AssignmentController@generateDoc')->name('assignment.group.generate-doc');
        Route::get('assignment/{id}/download-generate-doc', 'AssignmentController@downloadGenerateDoc')->name('assignment.group.download-generate-doc');
        Route::get('assignment/{id}/download-excel-sheet', 'AssignmentController@downloadExcelSheet')->name('assignment.group.download-excel-sheet');
        Route::post('assignment/{id}/assign-editor', 'AssignmentController@assignEditor')->name('assignment.assign_editor');
        Route::delete('assignment/{id}/remove-editor', 'AssignmentController@removeEditor')->name('assignment.remove_editor');
        Route::post('assignment/template/save/{id?}', 'AssignmentController@saveAssignmentTemplate')->name('assignment.template.save');
        Route::delete('assignment/template/delete/{id}', 'AssignmentController@deleteAssignmentTemplate')->name('assignment.template.delete');
        Route::post('assignment/learner-assignment/save/{id?}', 'AssignmentController@learnerAssignment')->name('assignment.learner-assignment.save');
        Route::post('assignment/{id}/disabled-learner-assignment/save', 'AssignmentController@disabledLearnerAssignment')
        ->name('assignment.disable-learner-assignment.save');
        Route::delete('assignment/learner-assignment/{id}/delete', 'AssignmentController@deleteLearnerAssignment')->name('assignment.learner-assignment.delete');
        Route::post('assignment/multiple-learner-assignment/save', 'AssignmentController@multipleLearnerAssignment')->name('assignment.multiple-learner-assignment.save');
        Route::get('/assignment/{assignment_id}/course/{course_id}/assignment-with-course-learners', 
        'AssignmentController@assignmentWithCourseLearner');
        Route::post('/assignment/{id}/disable-learner', 'AssignmentController@disableLearner');


        // Assignment Groups Route
        Route::resource('course/{course_id}/assignment/{assignment_id}/group', 'AssignmentGroupController', [
            'except' => ['index', 'create', 'edit'],
            'names' => [
                'show' => 'admin.assignment-group.show',
                'store' => 'admin.assignment-group.store',
                'update' => 'admin.assignment-group.update',
                'destroy' => 'admin.assignment-group.destroy',
            ],
        ]);
        Route::post('course/{course_id}/assignment/{assignment_id}/group/{id}/add_learner', 'AssignmentGroupController@add_learner')->name('assignment.group.add_learner');
        Route::post('course/{course_id}/assignment/{assignment_id}/group/{group_id}/remove_learner/{id}', 'AssignmentGroupController@remove_learner')->name('assignment.group.remove_learner');
        Route::get('course/{course_id}/assignment/{assignment_id}/group/{group_id}/download_all', 'AssignmentGroupController@downloadAll')->name('assignment.group.download_all');
        Route::post('course/{course_id}/assignment/{assignment_id}/group/{group_id}/feedback-availability', 'AssignmentGroupController@setGroupFeedbackAvailability')->name('assignment.group.feedback-availability');
        Route::post('/group/{group_id}/learner/{id}/submit_feedback', 'AssignmentGroupController@submit_feedback')->name('admin.assignment.group.submit_feedback'); // Submit assignment feedback
        Route::post('/group/{group_id}/learner/{id}/submit_feedback_learner', 'AssignmentGroupController@submit_feedback_learner')->name('admin.assignment.group.submit_feedback_learner'); // Submit assignment feedback
        Route::post('/assignment-group/{group_id}/set-feedback-to-other-learners/{group_learner_id}',
            'AssignmentGroupController@setFeedbackToOtherLearner')->name('admin.assignment.group.learner.set-feedback-to-other');
        Route::post('/feedback/{id}/remove_feedback', 'AssignmentGroupController@remove_feedback')->name('admin.assignment.group.remove_feedback'); // Remove assignment feedback
        Route::post('/feedback/{id}/update_feedback', 'AssignmentGroupController@update_feedback')->name('admin.assignment.group.update_feedback'); // Update assignment feedback
        Route::post('/feedback/{id}/update_feedback_admin', 'AssignmentGroupController@update_feedback_admin')->name('admin.assignment.group.update_feedback_admin'); // Update assignment feedback admin
        Route::post('/feedback/{id}/approve', 'AssignmentGroupController@approve')->name('admin.assignment.group.approve'); // Approve assignment feedback admin
        Route::post('/feedback/lock-status', 'AssignmentGroupController@updateFeedbackLockStatus')->name('learner.assignment.group.lock-status'); // Courses For Sale Status
        Route::get('/feedback/{id}/download', 'AssignmentGroupController@downloadFeedback')->name('assignment.feedback.download_manuscript');
        Route::get('/assignment-group/{group_id}/get-feedback-to-other-learners/{group_learner_id}', 'AssignmentGroupController@getFeedbackToOtherLearner')
            ->name('learner.assignment.group.get-feedback-to-other-learners');



        // Manuscripts Route
        Route::resource('/manuscript', 'ManuscriptController', [
            'except' => ['edit', 'create'],
            'names' => [
                'index' => 'admin.manuscript.index',
                'store' => 'admin.manuscript.store',
                'show' => 'admin.manuscript.show',
                'update' => 'admin.manuscript.update',
                'destroy' => 'admin.manuscript.destroy',
            ],
        ]);
        Route::post('/manuscript/{id}', 'ManuscriptController@addFeedback')->name('admin.feedback.store'); // Store Feedback
        Route::post('/feedback/{id}/delete', 'ManuscriptController@destroyFeedback')->name('admin.feedback.destroy'); // Delete Feedback
        Route::post('/manuscript/{id}/email', 'ManuscriptController@sendEmail')->name('admin.manuscript.email'); // Send email
        Route::get('email-template', 'EmailTemplateController@index')->name('admin.email-template.index');
        Route::post('/email_template/add_email_template', 'EmailTemplateController@addEmailTemplate')->name('admin.manuscript.add_email_template'); // Store Email Template
        Route::put('/email_template/edit_email_template/{id}', 'EmailTemplateController@editEmailTemplate')->name('admin.manuscript.edit_email_template'); // Update Email Template
        Route::post('/email_template/courseEditAdd/{course_id}', 'EmailTemplateController@courseEditAdd')->name('admin.email_template.courseEditAdd'); // Update Email Template


        Route::group([
            'prefix' => 'sale'
        ], function(){

            Route::get('/', 'SaleController@index')->name('admin.sales.index');
            Route::get('/load-tab-content', 'SaleController@loadTabContent');
            Route::post('/send-email/{id}/{parent}', 'SaleController@sendEmail')
                ->name('admin.sales.send-email');
            Route::get('/move-to-archive/{id}', 'SaleController@moveToArchive')->name('admin.sales.move-to-archive');
            Route::post('/is-invoice-sent', 'SaleController@orderInvoiceSent');
            Route::post('/is-order-withdrawn', 'SaleController@isOrderWithdrawn');
            Route::get('/add-to-po/{order_id}', 'SaleController@addToPowerOffice')->name('admin.sale.add-to-po');

        });

        Route::get('/email-history', 'EmailHistoryController@index')->name('admin.email-history.index');
        Route::resource('/replays', 'ReplayController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.replay.index',
                'store' => 'admin.replay.store',
                'update' => 'admin.replay.update',
                'destroy' => 'admin.replay.delete',
            ],
        ]);

        Route::get('/checkout-log', 'CheckoutLogController@index')->name('admin.checkout-log.index');

        Route::group([
            'prefix' => 'upcoming'
        ], function(){
            Route::get('/', 'UpcomingController@index')->name('admin.upcoming.index');
            Route::post('/{id}/save', 'UpcomingController@saveSection')->name('admin.upcoming.save');
        });

        Route::resource('/publishing', 'PublishingController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.publishing.index',
                'create' => 'admin.publishing.create',
                'store' => 'admin.publishing.store',
                'edit' => 'admin.publishing.edit',
                'update' => 'admin.publishing.update',
                'destroy' => 'admin.publishing.destroy',
            ],
        ]);

        // FAQ Route
        Route::resource('/faq', 'FaqController', [
            'only' => ['index', 'store', 'update', 'destroy'],
            'names' => [
                'index' => 'admin.faq.index',
                'store' => 'admin.faq.store',
                'update' => 'admin.faq.update',
                'destroy' => 'admin.faq.destroy',
            ],
        ]);

        Route::resource('/competition', 'CompetitionController', [
            'only' => ['index', 'store', 'update', 'destroy'],
            'names' => [
                'index' => 'admin.competition.index',
                'store' => 'admin.competition.store',
                'update' => 'admin.competition.update',
                'destroy' => 'admin.competition.destroy',
            ],
        ]);

        Route::resource('/writing-group', 'WritingGroupController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.writing-group.index',
                'create' => 'admin.writing-group.create',
                'store' => 'admin.writing-group.store',
                'edit' => 'admin.writing-group.edit',
                'update' => 'admin.writing-group.update',
                'destroy' => 'admin.writing-group.destroy',
            ],
        ]);

        Route::resource('/solution','SolutionController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.solution.index',
                'create' => 'admin.solution.create',
                'store' => 'admin.solution.store',
                'edit' => 'admin.solution.edit',
                'update' => 'admin.solution.update',
                'destroy' => 'admin.solution.destroy',
            ],
        ]);

        Route::resource('/sos-children','SosChildrenController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.sos-children.index',
                'create' => 'admin.sos-children.create',
                'store' => 'admin.sos-children.store',
                'edit' => 'admin.sos-children.edit',
                'update' => 'admin.sos-children.update',
                'destroy' => 'admin.sos-children.destroy',
            ],
        ]);
        Route::get('/sos-children/edit-main-description', 'SosChildrenController@getEditMainDescription')
            ->name('admin.sos-children.get-main-description');
        Route::post('/sos-children/edit-main-description', 'SosChildrenController@editMainDescription')
            ->name('admin.sos-children.post-main-description');

        Route::put('/blog/status-update/{id}', 'BlogController@statusUpdate')
            ->name('admin.sos-children.main-description');
        Route::resource('/blog','BlogController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.blog.index',
                'create' => 'admin.blog.create',
                'store' => 'admin.blog.store',
                'edit' => 'admin.blog.edit',
                'update' => 'admin.blog.update',
                'destroy' => 'admin.blog.destroy',
            ],
        ]);

        Route::resource('/publisher-book','PublisherBookController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.publisher-book.index',
                'create' => 'admin.publisher-book.create',
                'store' => 'admin.publisher-book.store',
                'edit' => 'admin.publisher-book.edit',
                'update' => 'admin.publisher-book.update',
                'destroy' => 'admin.publisher-book.destroy',
            ],
        ]);

        Route::group([
            'prefix' => '/publisher-book-library',
        ],function(){
            Route::post('/{book_id}/store', 'PublisherBookController@storeLibrary')->name('publisher-book-library.store');
            Route::put('/{id}/update', 'PublisherBookController@updateLibrary')->name('publisher-book-library.update');
            Route::delete('/{id}/delete', 'PublisherBookController@deleteLibrary')->name('publisher-book-library.delete');
        });

        Route::resource('/opt-in','OptInController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.opt-in.index',
                'create' => 'admin.opt-in.create',
                'store' => 'admin.opt-in.store',
                'edit' => 'admin.opt-in.edit',
                'update' => 'admin.opt-in.update',
                'destroy' => 'admin.opt-in.destroy',
            ],
        ]);

        Route::resource('/poem', 'PoemController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.poem.index',
                'create' => 'admin.poem.create',
                'store' => 'admin.poem.store',
                'edit' => 'admin.poem.edit',
                'update' => 'admin.poem.update',
                'destroy' => 'admin.poem.destroy',
            ]
        ]);

        Route::resource('/solution/{solution_id}/article','SolutionArticleController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.solution-article.index',
                'create' => 'admin.solution-article.create',
                'store' => 'admin.solution-article.store',
                'edit' => 'admin.solution-article.edit',
                'update' => 'admin.solution-article.update',
                'destroy' => 'admin.solution-article.destroy',
            ],
        ]);

        Route::get('/free-manuscript', 'FreeManuscriptController@index')->name('admin.free-manuscript.index');
        Route::post('/free-manuscript/{id}/delete', 'FreeManuscriptController@deleteFreeManuscript')->name('admin.free-manuscript.delete');
        Route::post('/free-manuscript/{id}/edit-content', 'FreeManuscriptController@editContent')->name('admin.free-manuscript.edit-content');
        Route::post('/free-manuscript/{id}/assign_editor', 'FreeManuscriptController@assignEditor')->name('admin.free-manuscript.assign_editor');
        Route::post('/free-manuscript/{id}/send_feedback', 'FreeManuscriptController@sendFeedback')->name('admin.free-manuscript.send_feedback');
        Route::get('/free-manuscript/{id}/feedback-history', 'FreeManuscriptController@feedbackHistory')->name('admin.free-manuscript.feedback-history');
        Route::get('/free-manuscript/{id}/download', 'FreeManuscriptController@downloadContent')->name('admin.free-manuscript.download');
        Route::post('/free-manuscript/{id}/resend-feedback', 'FreeManuscriptController@resendFeedback')->name('admin.free-manuscript.resend-feedback');
        Route::post('/free-manuscript/{id}/approve_feedback', 'FreeManuscriptController@approveFeedback')->name('head_editor.free-manuscript.feedback_approve');

        Route::resource('/other-service','OtherServiceController', [
            'except' => ['show'],
            'names' => [
                'index' => 'admin.other-service.index',
                'create' => 'admin.other-service.create',
                'store' => 'admin.other-service.store',
                'edit' => 'admin.other-service.edit',
                'update' => 'admin.other-service.update',
                'destroy' => 'admin.other-service.destroy',
            ],
        ]);

        Route::post('/other-service/{id}/coaching-timer/approve_date', 'OtherServiceController@approveDate')->name('admin.other-service.coaching-timer.approve_date');
        Route::post('/other-service/{id}/coaching-timer/set-approve-date', 'OtherServiceController@setCoachingApproveDate')
            ->name('admin.other-service.coaching-timer.set-coaching-approve-date');
        Route::post('/other-service/{id}/coaching-timer/suggest_date', 'OtherServiceController@suggestDate')->name('admin.other-service.coaching-timer.suggestDate');
        Route::post('/other-service/set-approved-date', 'OtherServiceController@setApprovedDate')->name('admin.other-service.coaching-timer.set-approved-date');
        Route::post('/other-service/{id}/coaching-timer/set_replay', 'OtherServiceController@setReplay')->name('admin.other-service.coaching-timer.set_replay');
        Route::post('/other-service/{id}/coaching-timer/mark_as_finished', 'OtherServiceController@markAsFinished')->name('admin.other-service.coaching-timer.mark_as_finished');
        Route::post('/other-service/{id}/update-status/{type}', 'OtherServiceController@updateStatus')->name('admin.other-service.update-status');
        Route::post('/other-service/{id}/lock-status/{type}', 'OtherServiceController@updateLocked')->name('admin.other-service.update-locked');
        Route::post('/other-service/{id}/update-expected-finish/{type}', 'OtherServiceController@updateExpectedFinish')->name('admin.other-service.update-expected-finish');
        Route::get('/other-service/{id}/download/{type}', 'OtherServiceController@downloadOtherServiceDoc')->name('admin.other-service.download-doc'); // Download assignment feedback
        Route::post('/other-service/{id}/add-feedback/{type}', 'OtherServiceController@addFeedback')->name('admin.other-service.add-feedback');
        Route::get('/other-service/{id}/download-feedback/{type}', 'OtherServiceController@downloadFeedback')->name('admin.other-service.download-feedback');
        Route::delete('/other-service/{id}/coaching-timer/delete', 'OtherServiceController@deleteCoaching')->name('admin.other-service.coaching-timer.delete');

        // Shop Manuscripts Route
        Route::resource('/shop-manuscript', 'ShopManuscriptController', [
            'except' => ['edit', 'create', 'show'],
            'names' => [
                'index' => 'admin.shop-manuscript.index',
                'store' => 'admin.shop-manuscript.store',
                'update' => 'admin.shop-manuscript.update',
                'destroy' => 'admin.shop-manuscript.destroy',
            ],
        ]);
        Route::post('/shop-manuscript-taken/{id}/assign_editor', 'ShopManuscriptController@updateTaken')->name('admin.shop-manuscript-taken.update_taken'); // Assign editor
        Route::post('/shop-manuscript-taken/{id}/add-feedback', 'ShopManuscriptController@addFeedback')->name('admin.shop-manuscript-taken-feedback.store'); // Store Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{id}/delete', 'ShopManuscriptController@destroyFeedback')->name('admin.shop-manuscript-taken-feedback.delete'); // Remove Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{id}/update-genre', 'ShopManuscriptController@updateGenre')->name('admin.shop-manuscript-taken.update-genre'); // Remove Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{id}/update-coaching-time-later', 'ShopManuscriptController@updateCoachingTimeLater')
        ->name('admin.shop-manuscript-taken.update-coaching-time-later');
        Route::post('/shop-manuscript-taken/{id}/update-description', 'ShopManuscriptController@updateDescription')->name('admin.shop-manuscript-taken.update-description'); // Remove Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{feedback_id}/approve-feedback', 'ShopManuscriptController@approveFeedback')->name('admin.shop-manuscript-taken.approve-feedback');


        Route::get('/test', 'ShopManuscriptController@testEmail');

        // Invoices Route
        Route::post('/invoice/create-new', 'InvoiceController@addInvoice')->name('admin.invoice.new');
        Route::resource('/invoice', 'InvoiceController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.invoice.index',
                'show' => 'admin.invoice.show',
                'store' => 'admin.invoice.store',
                'update' => 'admin.invoice.update',
                'destroy' => 'admin.invoice.destroy',
            ],
        ]);
        Route::post('/invoice/{id}', 'InvoiceController@addTransaction')->name('admin.transaction.store'); // Store Transaction
        Route::post('/invoice/{invoice_id}/transaction/{transaction_id}', 'InvoiceController@updateTransaction')->name('admin.transaction.update'); // Update Transaction
        Route::post('/invoice/{invoice_id}/transaction/{transaction_id}/delete', 'InvoiceController@destroyTransaction')->name('admin.transaction.destroy'); // Delete Transaction
        Route::get('/invoice/{id}/download-fiken', 'InvoiceController@downloadFikenPdf')->name('admin.invoice.download-fiken-pdf'); // Store Transaction


        Route::get('/yearly_calendar', 'AdminController@yearlyCalendar')->name('admin.yearly-calendar.index');


        // Package shop manuscripts route
        Route::resource('package_shop_manuscript/{id}', 'PackageShopManuscriptController', [
            'except' => ['index', 'show', 'edit', 'create', 'update', 'destroy'],
            'names' => [
                'store' => 'admin.package_shop_manuscript.store',
            ],
        ]);
        Route::post('package_shop_manuscript/{id}/remove', 'PackageShopManuscriptController@delete')->name('admin.package_shop_manuscript.destroy');


        // Package workshops route
        Route::post('package_workshop/{id}/approve', 'PackageWorkshopController@approve')->name('admin.package_workshop.approve');
        Route::post('package_workshop/{id}/disapprove', 'PackageWorkshopController@disapprove')->name('admin.package_workshop.disapprove');

        // Editors route
        Route::resource('/editor', 'EditorController', [
            'except' => 'show',
            'names' => [
                'index' => 'admin.editor.index',
                'create' => 'admin.editor.create',
                'store' => 'admin.editor.store',
                'edit' => 'admin.editor.edit',
                'update' => 'admin.editor.update',
                'destroy' => 'admin.editor.destroy',
            ],
        ]);

        Route::view('cron-log', 'backend.support.cron-log')->name('admin.cron-log.index');

        // Goto-webinar route
        Route::resource('/goto-webinar', 'GotoWebinarController', [
            'except' => 'show',
            'names' => [
                'index' => 'admin.goto-webinar.index',
                'create' => 'admin.goto-webinar.create',
                'store' => 'admin.goto-webinar.store',
                'edit' => 'admin.goto-webinar.edit',
                'update' => 'admin.goto-webinar.update',
                'destroy' => 'admin.goto-webinar.destroy',
            ],
        ]);

        //testimonial routes
        Route::resource('/testimonial', 'TestimonialController', [
            'except' => 'show',
            'names' => [
                'index' => 'admin.testimonial.index',
                'create' => 'admin.testimonial.create',
                'store' => 'admin.testimonial.store',
                'edit' => 'admin.testimonial.edit',
                'update' => 'admin.testimonial.update',
                'destroy' => 'admin.testimonial.destroy',
            ],
        ]);

        //testimonial routes
        Route::resource('/file', 'FilesController', [
            'except' => ['show', 'edit', 'create'],
            'names' => [
                'index' => 'admin.file.index',
                'store' => 'admin.file.store',
                'update' => 'admin.file.update',
                'destroy' => 'admin.file.destroy',
            ],
        ]);

        Route::get('/personal-trainer/export', 'PersonalTrainerController@export');
        Route::resource('/personal-trainer', 'PersonalTrainerController', [
            'except' => ['edit'],
            'names' => [
                'index' => 'admin.personal-trainer.index',
                'show' => 'admin.personal-trainer.show',
                'create' => 'admin.personal-trainer.create',
                'store' => 'admin.personal-trainer.store',
                'destroy' => 'admin.personal-trainer.destroy',
            ],
        ]);

        Route::get('/single-competition', 'PageController@singleCompetition')
            ->name('admin.single-competition.index');
        Route::get('/single-competition/{id}', 'PageController@singleCompetitionShow')
            ->name('admin.single-competition.show');
        Route::post('/single-competition', 'PageController@singleCompetitionStore')
            ->name('admin.single-competition.store');
        Route::put('/single-competition/{id}', 'PageController@singleCompetitionUpdate')
            ->name('admin.single-competition.update');
        Route::delete('/single-competition/{id}', 'PageController@singleCompetitionDelete')
            ->name('admin.single-competition.delete');
        Route::delete('/single-competition/{id}/manuscript', 'PageController@singleCompetitionDeleteManuscript')
            ->name('admin.single-competition.delete-manuscript');

        //Calendar Notes
        Route::resource('/calendar-note', 'CalendarNoteController', [
            'except' => 'show',
            'names' => [
                'index' => 'admin.calendar-note.index',
                'create' => 'admin.calendar-note.create',
                'store' => 'admin.calendar-note.store',
                'edit' => 'admin.calendar-note.edit',
                'update' => 'admin.calendar-note.update',
                'destroy' => 'admin.calendar-note.destroy',
            ],
        ]);


        // Calendar Page
        Route::get('/calendar', 'PageController@calendar')->name('backend.calendar');
        Route::get('/pilot-reader', 'PageController@pilotReader')->name('backend.pilot-reader');

        // Finish Assignment
        Route::post('/assignment/{id}/finish', 'PageController@finishAssignment')->name('backend.assignment.finish');

        // Settings
        Route::post('/settings/update/welcome_email', 'SettingsController@updateEmail')->name('admin.settings.update.welcome_email'); // Store Feedback
        Route::post('/settings/update/terms', 'SettingsController@updateTerms')->name('admin.settings.update.terms'); // Store Terms
        Route::post('/settings/update/other-terms', 'SettingsController@updateOtherTerms')->name('admin.settings.update.other-terms');
        Route::post('/settings/update/opt-in-terms', 'SettingsController@updateOptInTerms')->name('admin.settings.update.opt-in-terms'); // Store Terms
        Route::post('/settings/update/opt-in-description', 'SettingsController@updateOptInDescription')->name('admin.settings.update.opt-in-description'); // Store Terms
        Route::post('/settings/update/opt-in-rektor-description', 'SettingsController@updateOptInRektorDescription')->name('admin.settings.update.opt-in-rektor-description'); // Store Terms
        Route::post('/settings/update/gt_confirmation_email', 'SettingsController@gtConfirmationEmail')->name('admin.settings.update.gt_confirmation_email'); // Store Feedback
        Route::post('/settings/update/webinar_email_template', 'SettingsController@webinarEmailTemplate')->name('admin.settings.update.webinar_email_template');
        Route::post('/settings/update/gt_reminder_email_template', 'SettingsController@gtReminderEmail')->name('admin.settings.update.gt_reminder_email_template');
        Route::post('/settings/update/course_not_started_reminder', 'SettingsController@courseNotStartedReminder')->name('admin.settings.update.course_not_started_reminder');
        Route::post('/settings/update/head-editor', 'SettingsController@headEditor')->name('admin.settings.update.head-editor');
        Route::post('/settings/update/editors-note', 'SettingsController@updateEditorsNote')->name('admin.settings.update.editors-note'); // Store Terms
        Route::post('/settings/create/{name}', 'SettingsController@create')->name('admin.settings.create');
        Route::get('/news', 'SettingsController@news')->name('admin.news.index');
        Route::post('/news/save', 'SettingsController@saveNews')->name('admin.news.save');

        Route::resource('/genre', 'GenreController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.genre.index',
                'show' => 'admin.genre.show',
                'store' => 'admin.genre.store',
                'update' => 'admin.genre.update',
                'destroy' => 'admin.genre.destroy',
            ],
        ]);

        Route::delete('/self-publishing/remove-learner/{id}', 'SelfPublishingController@removeLearnerFromPublishing')
            ->name('admin.learner.remove-self-publishing');
        Route::get('/self-publishing/{id}/learners', 'SelfPublishingController@learners')->name('admin.self-publishing.learners');
        Route::post('/self-publishing/{id}/add-learners', 'SelfPublishingController@addLearners')->name('admin.self-publishing.add-learners');
        Route::get('/self-publishing/{id}/download-manuscript', 'SelfPublishingController@selfPublishingDownloadManuscript')
            ->name('admin.self-publishing.download-manuscript');
        Route::post('/self-publishing/{id}/add-feedback', 'SelfPublishingController@addFeedback')->name('admin.self-publishing.add-feedback');
        Route::get('/self-publishing/feedback/{feedback_id}/download', 'SelfPublishingController@downloadFeedback')->name('admin.self-publishing.download-feedback');
        Route::delete('/self-publishing/delete-learner/{learner_id}', 'SelfPublishingController@deleteLearner')->name('admin.self-publishing.delete-learner');
        Route::get('/self-publishing/orders', 'SelfPublishingController@orders')->name('admin.self-publishing.orders');
        Route::resource('/self-publishing', 'SelfPublishingController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.self-publishing.index',
                'show' => 'admin.self-publishing.show',
                'store' => 'admin.self-publishing.store',
                'update' => 'admin.self-publishing.update',
                'destroy' => 'admin.self-publishing.destroy',
            ],
        ]);

        Route::get('/book-publisher/calculator', 'BookPublisherController@calculator')->name('admin.book-publisher.calculator');

        Route::resource('/marketing-plan', 'MarketingPlanController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.marketing-plan.index',
                'show' => 'admin.marketing-plan.show',
                'store' => 'admin.marketing-plan.store',
                'update' => 'admin.marketing-plan.update',
                'destroy' => 'admin.marketing-plan.destroy',
            ],
        ]);

        Route::post('/book-for-sale/{book_for_sale_id}/save-inventory', 'BookForSaleController@saveInventory')
        ->name('admin.book-for-sale.save-inventory');
        Route::post('/book-for-sale/{book_for_sale_id}/save-sales', 'BookForSaleController@saveSales')
        ->name('admin.book-for-sale.save-sales');
        Route::get('/book-for-sale/{book_for_sale_id}/details', 'BookForSaleController@saleDetails');
        Route::get('/book-for-sale/{book_for_sale_id}/details', 'BookForSaleController@saleDetails');
        Route::delete('/book-for-sale/sales-report/{sale_id}/delete', 'BookForSaleController@deleteSales');
        Route::post('/book-for-sale/{book_for_sale_id}/save-distribution-cost', 'BookForSaleController@saveDistributionCost')
        ->name('admin.book-for-sale.save-distribution-cost');
        Route::delete('/book-for-sale/distribution-cost/{dist_id}/delete', 'BookForSaleController@deleteDistributionCost')
        ->name('admin.book-for-sale.delete-distribution-cost');
        
        Route::resource('/book-for-sale', 'BookForSaleController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.book-for-sale.index',
                'show' => 'admin.book-for-sale.show',
            ],
        ]);

        Route::get('/application', 'PageController@application')->name('admin.application');

        Route::post('/task/save', 'ProjectController@saveTask')->name('admin.project-task.save');
        Route::put('/project/task/{id}/update', 'ProjectController@updateTask')->name('admin.project-task.update');
        Route::post('/project/task/{id}/finish', 'ProjectController@finishTask')->name('admin.project-task.finish');
        Route::delete('/project/task/{id}/delete', 'ProjectController@deleteTask')->name('admin.project-task.delete');
        Route::post('/time-register/save', 'TimeRegisterController@save')->name('admin.time-register.save');
        Route::delete('/time-register/{id}/delete', 'TimeRegisterController@destroy')->name('admin.time-register.delete');
        Route::get('/time-register/{id}/time-used-list', 'TimeRegisterController@timeUsedList');
        Route::post('/time-register/{id}/save-time-used', 'TimeRegisterController@saveTimeUsed');
        Route::delete('/time-register/time-used/{id}/delete', 'TimeRegisterController@deleteTimeUsed');

        Route::get('/services', 'PublishingPackageController@services')->name('admin.service.index');
        Route::get('/all-services', 'PublishingPackageController@getAllServices');
        Route::post('/service/{id}/update-field', 'PublishingPackageController@updateServiceField');
        Route::post('/save-service', 'PublishingPackageController@saveService');

        Route::get('/assemble-book-packages/all-options', 'AssembleBookController@getOptions');
        Route::post('/assemble-book-packages/save-cover-or-color', 'AssembleBookController@saveCoverOrColor');
        Route::post('/assemble-book-packages/save-count-or-help', 'AssembleBookController@saveCountOrHelp');
        Route::view('/assemble-book-packages', 'backend.assemble-books.list');

        // Advisories
        Route::put('/advisory/{id}', 'AdvisoryController@update')->name('admin.advisory.update');

        Route::resource('/pulse', 'PulseController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.pulse.index',
                'show' => 'admin.pulse.show',
                'store' => 'admin.pulse.store',
                'update' => 'admin.pulse.update',
                'destroy' => 'admin.pulse.destroy',
            ],
        ]);
        Route::post('/pulse/{id}/update-pulse-title', 'PulseController@updatePulseTitle')->name('admin.pulse.update-pulse-title'); // Assign User
        Route::post('/pulse/remove-subscriber', 'PulseController@removeSubscriber')->name('admin.pulse.remove-subscriber'); // Assign User

        Route::resource('/board','BoardController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'admin.board.index',
                'show' => 'admin.board.show',
                'store' => 'admin.board.store',
                'update' => 'admin.board.update',
                'destroy' => 'admin.board.destroy',
            ],
        ]);
        Route::post('/board/{id}/assign-user', 'BoardController@assignUser')->name('admin.board.assign-user'); // Assign User
        Route::post('/board/{id}/add-pulse', 'BoardController@addPulse')->name('admin.board.add-pulse'); // Assign User
        Route::post('/board/{id}/update-group-title', 'BoardController@updateGroupTitle')->name('update-group-title'); // Assign User
        Route::post('/board/{id}/update-pulse-status', 'BoardController@setStatus')->name('admin.board.update-pulse-status'); // Update pulse status
        Route::post('/board/{id}/update-timeline', 'BoardController@setTimeline')->name('admin.board.update-timeline'); // Update timeline

        Route::put('/survey/{id}/update-date', 'SurveyController@updateDate')->name('admin.survey.update-date');
        Route::resource('/survey', 'SurveyController', [
            'except' => ['create'],
            'names' => [
                'index' => 'admin.survey.index',
                'show' => 'admin.survey.show',
                'edit' => 'admin.survey.edit',
                'store' => 'admin.survey.store',
                'update' => 'admin.survey.update',
                'destroy' => 'admin.survey.destroy',
            ],
        ]);

        Route::get('/survey/{id}/download-answers','SurveyController@downloadAnswers')->name('admin.survey.download-answers');
        Route::get('/survey/{id}/answers','SurveyController@answers')->name('admin.survey.answers');

        Route::resource('/survey/{survey_id}/question', 'SurveyQuestionController', [
            'except' => ['create'],
            'names' => [
                'index' => 'admin.survey.question.index',
                'show' => 'admin.survey.question.show',
                'edit' => 'admin.survey.question.edit',
                'store' => 'admin.survey.question.store',
                'update' => 'admin.survey.question.update',
                'destroy' => 'admin.survey.question.destroy',
            ],
        ]);

        Route::get('translations','PageController@translations');

        Route::get('translations/view','PageController@translations');

        Route::prefix('zoom')->group(function () {
            Route::get('/', 'ZoomController@index');
            Route::get('webinar/{user_id}', 'ZoomController@webinars')->name('admin.zoom.webinars');
            Route::get('webinar/{user_id}/create', 'ZoomController@createWebinar')->name('admin.zoom.webinar.create');
            Route::post('webinar/{user_id}/store', 'ZoomController@storeWebinar')->name('admin.zoom.webinar.store');
            Route::get('webinar/{webinar_id}/edit', 'ZoomController@editWebinar')->name('admin.zoom.webinar.edit');
            Route::put('webinar/{webinar_id}/update', 'ZoomController@updateWebinar')->name('admin.zoom.webinar.update');
            Route::delete('webinar/{webinar_id}/delete', 'ZoomController@deleteWebinar')->name('admin.zoom.webinar.delete');
            Route::post('webinar/{webinar_id}/panelist', 'ZoomController@storePanelist')->name('admin.zoom.webinar.panelist.store');
            Route::delete('webinar/{webinar_id}/panelist/{panelist_id}', 'ZoomController@deletePanelist')->name('admin.zoom.webinar.panelist.delete');
        });

        // head editor route
        Route::post('personal_assignment/{id}/approve_feedback/{learner_id}', 'AssignmentController@approveFeedbackNoGroup')->name('head_editor.personal_assignment.feedbac_approve');
        Route::post('course_assignment/{id}/approve_feedback/{learner_id}/feedback/{feedback_id}', 'AssignmentGroupController@approveFeedbackCourse')->name('head_editor.course_assignment.feedback_approve');
        Route::post('shop-manuscript-taken/{id}/approve-feedback/{learner_id}/feedback/{feedback_id}', 'ShopManuscriptController@approveFeedback')->name('head_editor.shop-manuscript-taken-feedback.approve');
        Route::post('other-service/{id}/approve-feedback/{type}', 'OtherServiceController@approveFeedback')->name('head_editor.other-service.approve-feedback');

        // editor assignment
        Route::post('editor_assignment_price/save', 'EditorAssignmentPriceController@save')->name('editor_assignment_price.save');
        Route::post('editor_assignment_price/{id}/delete', 'EditorAssignmentPriceController@delete')->name('editor_assignment_price.delete');
        Route::get('editor_total_worked/{id}', 'EditorController@total')->name('admin.total_editor_worked');
        Route::post('saveGenrePrefences/{from_admin}', 'EditorController@saveGenrePrefences')->name('admin.save-genre-prefences');
        Route::post('deleteGenrePreferences/{id}', 'EditorController@deleteGenrePreferences')->name('admin.delete-genre-preferences');
        Route::post('hideShowEditor/{editor_id}/{hide}', 'EditorController@hideShowEditor')->name('admin.hide-show-editor');

        Route::get('showEditorHidden/{editor_id}', 'EditorController@showEditorHidden')->name('admin.show-editor-hidden');
        Route::get('deleteEditorHidden/{id}', 'EditorController@deleteEditorHidden')->name('admin.delete-editor-hidden');
        Route::post('setHowManyManuscriptYouCanTake/{id}', 'EditorController@setHowManyManuscriptYouCanTake')->name('admin.setHowManyManuscriptYouCanTake');
        Route::post('sendRequestToEditor/{id}', 'LearnerController@sendRequestToEditor')->name('admin.send-request-to-editor');
        Route::post('headEditorToEditor/{editor_id}/{type}/{title}/{learner}', 'HeadEditorController@sendEmail')->name('admin.head-editor-to-editor');

    });


    // Authentication
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::post('login', 'LoginController@adminLogin')->name('admin.login.store');
    });


    Route::get('/backup', 'Backend\PageController@backup')->name('backup');
    Route::get('/check-nearly-expired-course', 'Backend\PageController@checkNearlyExpiredCourses');
    Route::get('/user-activity', 'Backend\PageController@userActivity');
    Route::get('/user-activity/{id}', 'Backend\PageController@userActivityDetails');

    Route::get('/dropbox/shared-link/{path}', 'Frontend\DropboxController@createSharedLink')
        ->where('path', '.*')
        ->name('dropbox.shared_link');
    Route::get('/dropbox/download/{path}', 'Frontend\DropboxController@downloadFile')
        ->where('path', '.*')
        ->name('dropbox.download_file');
});


/**
 * Editor Routes
 *
 */
Route::group([
    'domain' => $editor,
], function(){
    Route::group([
        'middleware' => ['editor', 'logActivity'],
        'namespace' => 'Editor'
    ], function(){

        Route::get('/', 'PageController@dashboard')->name('editor.dashboard');
        Route::get('/upcoming-assignments', 'PageController@upcomingAssignments')->name('editor.upcoming-assignment');
        Route::get('assignmentArchive','PageController@assignmentArchive')->name('editor.assignment-archive');
        Route::get('manuscriptYouCanTake', 'ManuscriptEditorCanTakeController@index')->name('editor.manuscript-you-can-take');
        Route::post('manuscriptYouCanTake/save', 'ManuscriptEditorCanTakeController@save')->name('editor.manuscript-you-can-take-save');
        Route::post('manuscriptYouCanTake/{id}/delete', 'ManuscriptEditorCanTakeController@delete')->name('editor.manuscript-you-can-take.delete');
        Route::get('/yearly-calendar', 'PageController@yearlyCalendar')->name('editor.yearly-calendar.index');
        Route::get('/editors-note', 'PageController@editorsNote')->name('editor.editors-note');
        Route::get('/assigned-webinar', 'AssignedWebinarController@show')->name('editor.assigned-webinar');
        Route::post('/self-publishing/{id}/feedback', 'PageController@selfPublishingFeedback')->name('editor.self-publishing.feedback');
        Route::get('/self-publishing/{id}/download-manuscript', 'PageController@selfPublishingDownloadManuscript')->name('editor.self-publishing.download-manuscript');
        Route::post('/assignment-manuscript/{id}/mark-finished', 'PageController@assignmentManuscriptFinished')->name('editor.assignment-manuscript.mark-finished');
        Route::get('/project/{id}', 'PageController@projectDetails')->name('editor.project.show');
        Route::post('/project/{id}/update-editor-hours', 'PageController@projectEditorHours')->name('editor.project.update-editor-hours');
    });

    Route::group([
        'middleware' => 'editor',
        'namespace' => 'Backend'
    ], function(){

        Route::post('backend/change-password', 'PageController@changePassword')->name('editor.change-password');
        Route::post('assignment_manuscript/{id}/learner/{learner_id}/feedback', 'AssignmentController@manuscriptFeedbackNoGroup')->name('editor.assignment.group.manuscript-feedback-no-group');
        Route::post('/shop-manuscript-taken/{id}/add-feedback', 'ShopManuscriptController@addFeedback')->name('editor.admin.shop-manuscript-taken-feedback.store');
        Route::get('backend/{id}/download_shop_manuscript', 'PageController@downloadShopManuscript')->name('editor.backend.download_shop_manuscript');
        Route::get('backend/{id}/download_assigned_manuscript', 'PageController@downloadAssignedManuscript')->name('editor.backend.download_assigned_manuscript');
        Route::post('/group/{group_id}/learner/{id}/submit_feedback', 'AssignmentGroupController@submit_feedback')->name('editor.assignment.group.submit_feedback'); // Submit assignment feedback
        Route::post('/other-service/{id}/update-status/{type}', 'OtherServiceController@updateStatus')->name('editor.other-service.update-status');
        Route::post('/other-service/{id}/add-feedback/{type}', 'OtherServiceController@addFeedback')->name('editor.other-service.add-feedback');
        Route::get('/other-service/{id}/download/{type}', 'OtherServiceController@downloadOtherServiceDoc')->name('editor.other-service.download-doc'); // Download assignment feedback
        Route::post('/other-service/{id}/coaching-timer/set_replay', 'OtherServiceController@editorSetReplay')->name('editor.other-service.coaching-timer.set_replay');
        Route::get('settings', 'EditorController@settings')->name('editor.settings');
        Route::post('saveGenrePrefences/{from_admin}', 'EditorController@saveGenrePrefences')->name('editor.save-genre-prefences');
        Route::post('deleteGenrePreferences/{id}', 'EditorController@deleteGenrePreferences')->name('editor.delete-genre-preferences');
        Route::post('saveAssignmentManuscriptEditorCanTake/{id}/{assignment_manu_id}', 'EditorController@saveAssignmentManuscriptEditorCanTake')->name('editor.saveAssignmentManuscriptEditorCanTake');
        Route::get('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}', 'LearnerController@shopManuscriptTakenShowEditorPreview')->name('editor.shop_manuscript_taken');
        Route::get('acceptShopManuscriptRequest/{shop_manuscript_taken_id}/{accept}/{request_id}', 'ShopManuscriptController@editorAcceptRequest')->name('editor.acceptShopManuscriptRequest');
        Route::post('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}/comment', 'LearnerController@shopManuscriptTakenShowComment')->name('editor.shop_manuscript_taken_comment');
        Route::post('/update-expected-finish/{type}/{id}', 'PageController@updateExpectedFinish')->name('editor.personal-assignment.update-expected-finish');
        Route::get('assignment-manuscript/{id}/download-letter', 'AssignmentController@downloadManuscriptLetter')->name('editor.assignment.manuscript.download_letter');
        Route::post('/free-manuscript/{id}/edit-content', 'FreeManuscriptController@editContent')->name('editor.free-manuscript.edit-content');
        Route::post('/free-manuscript/{id}/send_feedback', 'FreeManuscriptController@sendFeedback')->name('editor.free-manuscript.send_feedback');
        Route::get('/free-manuscript/{id}/download', 'FreeManuscriptController@downloadContent')->name('editor.free-manuscript.download');
        Route::post('/time-register/save', 'TimeRegisterController@save')->name('editor.time-register.save');
        Route::delete('/time-register/{id}/delete', 'TimeRegisterController@destroy')->name('admin.time-register.delete');
        Route::get('/time-register/{id}/time-used-list', 'TimeRegisterController@timeUsedList');
        Route::post('/time-register/{id}/save-time-used', 'TimeRegisterController@saveTimeUsed');
        Route::delete('/time-register/time-used/{id}/delete', 'TimeRegisterController@deleteTimeUsed');
    });

    // Authentication
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::get('login/editor-email/{email_hash}', 'LoginController@editorEmailLogin')->name('editor.login.email');
        Route::post('login', 'LoginController@editorLogin')->name('editor.login.store');
    });
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

Route::get('/check-nearly-expired-course', 'HomeController@checkNearlyExpiredCourse');



/**
 * Authentication Routes
 *
 *
 */
Route::group([
    'namespace' => 'Auth',
], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::get('logout', 'LoginController@logout')->name('auth.logout-get');
        Route::post('logout', 'LoginController@logout')->name('auth.logout');
        Route::post('password', 'PasswordController@updatePassword');
    });
});

// Localization - use for vue
Route::get('/js/lang.js', function () {
    $strings = Cache::rememberForever('lang.js', function () {
        $lang = config('app.locale');

        $files   = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];

        foreach ($files as $file) {
            $name           = basename($file, '.php');
            $strings[$name] = require $file;
        }

        return $strings;
    });

    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');