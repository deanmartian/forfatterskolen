<?php

// Domains

$front = 'dev.forfatterskolen.loc';
$admin = 'admin.forfatterskolen.loc';

$front = 'www.forfatterskolen.no';
$admin = 'admin.forfatterskolen.no';


/**
 * Front End Routes
 *
 *
 */
Route::group([
    'domain' => $front,
], function(){


    Route::group([
        'namespace' => 'Frontend',
    ], function () {

        Route::get('/', 'HomeController@index')->name('front.home'); // Homepage
        Route::get('/contact-us', 'HomeController@contact_us')->name('front.contact-us'); // Contact Us
        Route::get('/faq', 'HomeController@faq')->name('front.faq'); // FAQ
        Route::get('/subscribe-success', function(){
            return view('frontend.subscribe-success');
        })->name('front.subscribe-success'); // Homepage
        Route::get('/shop-manuscript', 'ShopManuscriptController@index')->name('front.shop-manuscript.index'); // Shop Manuscript Listing
        Route::get('/gratis-tekstvurdering', 'ShopManuscriptController@freeManuscriptShow')->name('front.free-manuscript.index'); // Free Manuscript
        Route::get('/gratis-tekstvurdering/success', 'ShopManuscriptController@freeManuscriptShowSuccess')->name('front.free-manuscript.success'); // Free Manuscript
        Route::post('/gratis-tekstvurdering/send', 'ShopManuscriptController@freeManuscriptSend')->name('front.free-manuscript.send'); // Free Manuscript Send

        Route::get('/shop-manuscript/{id}/checkout', 'ShopManuscriptController@checkout')->name('front.shop-manuscript.checkout'); // Checkout Shop Manuscript


        Route::post('/shop-manuscript/{id}/place_order', 'ShopManuscriptController@place_order')->name('front.shop-manuscript.place_order'); // Checkout Shop Manuscript
        

        // Test Manuscript (Shop Manuscript)
        Route::post('/test_manuscript', 'ShopManuscriptController@test_manuscript')->name('front.shop-manuscript.test_manuscript'); // Test count shop manuscript

        // Pay IPN
        Route::post('/paypalipn', 'ShopController@paypalIPN')->name('front.shop.paypalipn'); // Paypal IPN

        // Course
        Route::group([
            'prefix' => 'course'
        ], function(){
            Route::get('/', 'CourseController@index')->name('front.course.index'); // Course Listing
            Route::get('/{id}', 'CourseController@show')->name('front.course.show'); // Course Details
            Route::get('/{id}/checkout', 'ShopController@checkout')->name('front.course.checkout'); // Checkout
            Route::post('/{id}/checkout/place_order', 'ShopController@place_order')->name('front.course.place_order'); // Place Order
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

        Route::get('/thankyou', 'ShopController@thankyou')->name('front.shop.thankyou'); // Thank You


        /*Route::post('/cart/add', 'ShopController@add_to_cart')->name('front.shop.add_to_cart'); // Add To Cart
        Route::post('/cart/remove', 'ShopController@remove_from_cart')->name('front.shop.remove_from_cart'); // Remove From Cart*/


    });


    // Learner Dashboard
    Route::group([
        'middleware' => 'learner',
        'namespace' => 'Frontend',
        'prefix' => 'account',
    ], function(){
        //Route::get('/dashboard', 'LearnerController@dashboard')->name('learner.dashboard'); // Dashboard Page
        Route::get('/course', 'LearnerController@course')->name('learner.course'); // Courses Page
        Route::get('/course/{id}', 'LearnerController@courseShow')->name('learner.course.show'); // Single Course Page
        Route::get('/calendar', 'LearnerController@calendar')->name('learner.calendar'); // Calendar Page
        Route::get('/invoice', 'LearnerController@invoice')->name('learner.invoice'); // Invoice Listing Page
        Route::get('/invoice/{id}', 'LearnerController@invoiceShow')->name('learner.invoice.show'); // Invoice Single Page
        Route::get('/profile', 'LearnerController@profile')->name('learner.profile'); // Profile Page
        Route::get('/course/{course_id}/lesson/{id}', 'LearnerController@lesson')->name('learner.course.lesson'); // Lesson Page
        Route::get('/manuscript/{id}', 'LearnerController@manuscriptShow')->name('learner.manuscript.show'); // Manuscript Single Page
        Route::get('/shop-manuscript', 'LearnerController@shopManuscript')->name('learner.shop-manuscript'); // Shop Manuscripts Page
        Route::get('/shop-manuscript/{id}', 'LearnerController@shopManuscriptShow')->name('learner.shop-manuscript.show'); // Shop Manuscript Show Page
        Route::get('/workshop', 'LearnerController@workshop')->name('learner.workshop'); // Workshops Page
        Route::get('/webinar', 'LearnerController@webinar')->name('learner.webinar'); // Webinars Page
        Route::get('/assignment', 'LearnerController@assignment')->name('learner.assignment'); // Assignments Page
        Route::get('/assignment/group/{id}', 'LearnerController@group_show')->name('learner.assignment.group.show'); // Assignment show Page



        Route::post('/profile', 'LearnerController@profileUpdate')->name('learner.profile.update'); // Profile Update
        Route::post('/course/take', 'LearnerController@takeCourse')->name('learner.course.take'); // Take Course
        Route::post('/course/{id}/uploadManuscript', 'LearnerController@uploadManuscript')->name('learner.course.uploadManuscript'); // Upload manuscript to course
        Route::post('/shop-manuscript/{id}/comment', 'LearnerController@shopManuscriptPostComment')->name('learner.shop-manuscript.post-comment'); // Shop Manuscript Show Page
        Route::post('/assignment/{id}/upload', 'LearnerController@assignmentManuscriptUpload')->name('learner.assignment.add_manuscript'); // Upload assignment manuscript
        Route::post('/group/{group_id}/learner/{id}/submit_feedback', 'LearnerController@submit_feedback')->name('learner.assignment.group.submit_feedback'); // Upload assignment manuscript
    });



    // Authentication
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
        'middleware' => 'guest',
    ], function () {
        Route::get('login', 'LoginController@showFrontend')->name('auth.login.show');

        Route::post('login', 'LoginController@login')->name('frontend.login.store');
        Route::post('checkout/login', 'LoginController@checkoutLogin')->name('frontend.login.checkout.store');
        Route::post('register', 'RegisterController@store')->name('frontend.register.store');
        Route::post('passwordreset', 'ResetPasswordController@store')->name('frontend.passwordreset.store');
        Route::get('passwordreset/{token}', 'ResetPasswordController@resetForm')->name('frontend.passwordreset.form');
        Route::post('passwordreset/{token}/update', 'ResetPasswordController@updatePassword')->name('frontend.passwordreset.update');
    });

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
        'middleware' => 'admin',
        'namespace' => 'Backend'
    ], function(){

        // Dashboard Page
        Route::get('/', 'PageController@dashboard')->name('backend.dashboard');


        // Learners Route
        Route::resource('learner', 'LearnerController', [
            'names' => [
                'index' => 'admin.learner.index',
                'show' => 'admin.learner.show',
                'update' => 'admin.learner.update',
                'destroy' => 'admin.learner.delete',
            ],
        ]);
        Route::get('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}', 'LearnerController@shopManuscriptTakenShow')->name('shop_manuscript_taken');

        Route::post('learner/{id}/shop-manuscript/{shop_manuscript_taken_id}/comment', 'LearnerController@shopManuscriptTakenShowComment')->name('shop_manuscript_taken_comment');

        Route::post('shop-manuscript/{id}/update_document', 'LearnerController@updateDocumentShopManuscriptTaken')->name('shop_manuscript_taken.update_document');

        Route::post('learner/activate_course_taken', 'LearnerController@activate_course_taken')->name('activate_course_taken');
        Route::post('learner/delete_course_taken', 'LearnerController@delete_course_taken')->name('delete_course_taken');
        Route::post('learner/activate_shop_manuscript_taken', 'LearnerController@activate_shop_manuscript_taken')->name('activate_shop_manuscript_taken');
        Route::post('learner/delete_shop_manuscript_taken', 'LearnerController@delete_shop_manuscript_taken')->name('delete_shop_manuscript_taken');
        Route::post('/learner/{id}/add_shop_manuscript', 'LearnerController@addShopManuscript')->name('admin.shop-manuscript.add_learner'); // Shop Manuscript add learner
        Route::post('/course_taken/{id}/set_availability', 'LearnerController@setCourseTakenAvailability')->name('admin.course_taken.set_availability'); // Shop Manuscript add learner
        Route::post('/course_taken/{id}/allow_lesson_access/{lesson_id}', 'LearnerController@allow_lesson_access')->name('admin.course_taken.allow_lesson_access'); //allow_lesson_access
        Route::post('/course_taken/{id}/default_lesson_access/{lesson_id}', 'LearnerController@default_lesson_access')->name('admin.course_taken.default_lesson_access'); //default_lesson_access
        



        // Courses Route
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
        Route::post('course/{id}/clone', 'CourseController@clone_course')->name('admin.course.clone');
        Route::post('course/{id}/add_similar_course', 'CourseController@add_similar_course')->name('admin.course.add_similar_course');
        Route::post('course/remove_similar_course/{similar_course_id}', 'CourseController@remove_similar_course')->name('admin.course.remove_similar_course');
        Route::post('/course/learner/add', 'LearnerController@addLearner')->name('learner.course.add.learner'); // Add Learner To Course
        Route::post('/course/learner/remove', 'LearnerController@removeLearner')->name('learner.course.remove.learner'); // Remove Learner From Course





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





        // Package Route
        Route::resource('course/{id}/package', 'PackageController', [
            'names' => [
                'store' => 'admin.course.package.store',
                'update' => 'admin.course.package.update',
                'destroy' => 'admin.course.package.destroy',
            ],
        ]);



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
        Route::resource('/course/{id}/lesson', 'LessonController', [
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


        // Webinar Presenter Route
        Route::resource('webinar/{webinar_id}/presenter', 'WebinarPresenterController', [
            'except' => ['index', 'show', 'create', 'edit'],
            'names' => [
                'store' => 'admin.webinar.webinar-presenter.store',
                'update' => 'admin.webinar.webinar-presenter.update',
                'destroy' => 'admin.webinar.webinar-presenter.destroy',
            ],
        ]);



        // Assignments Route
        Route::resource('/assignment', 'AssignmentController', [
            'except' => ['show', 'create', 'edit', 'store', 'update', 'destroy'],
            'names' => [
                'index' => 'admin.assignment.index', 
            ],
        ]);
        Route::resource('course/{course_id}/assignment', 'AssignmentController', [
            'except' => ['index', 'create', 'edit'],
            'names' => [
                'show' => 'admin.assignment.show', 
                'store' => 'admin.assignment.store', 
                'update' => 'admin.assignment.update', 
                'destroy' => 'admin.assignment.destroy', 
            ],
        ]);

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
        Route::post('/manuscript/{id}/assign_editor', 'ManuscriptController@assignEditor')->name('admin.manuscript.assign_editor'); // Assign editor
        Route::post('/feedback/{id}/delete', 'ManuscriptController@destroyFeedback')->name('admin.feedback.destroy'); // Delete Feedback




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
        Route::post('/shop-manuscript-taken/{id}/assign_editor', 'ShopManuscriptController@assignEditor')->name('admin.shop-manuscript-taken.assign_editor'); // Assign editor
        Route::post('/shop-manuscript-taken/{id}', 'ShopManuscriptController@addFeedback')->name('admin.shop-manuscript-taken-feedback.store'); // Store Shop Manuscript Feedback
        Route::post('/shop-manuscript-taken/{id}/delete', 'ShopManuscriptController@destroyFeedback')->name('admin.shop-manuscript-taken-feedback.delete'); // Remove Shop Manuscript Feedback


        // Invoices Route
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
        Route::post('/invoice/{invoice_id}/transaction/{id}', 'InvoiceController@updateTransaction')->name('admin.transaction.update'); // Update Transaction
        Route::post('/invoice/{invoice_id}/transaction/{id}/delete', 'InvoiceController@destroyTransaction')->name('admin.transaction.destroy'); // Delete Transaction




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




        // Calendar Page
        Route::get('/calendar', 'PageController@calendar')->name('backend.calendar');


        // Settings
        Route::post('/settings/update/welcome_email', 'SettingsController@updateEmail')->name('admin.settings.update.welcome_email'); // Store Feedback

    });

    
    // Authentication
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::post('login', 'LoginController@adminLogin')->name('admin.login.store');
    });


    Route::get('/backup', 'Backend\PageController@backup')->name('backup');
});






/**
 * Authentication Routes
 *
 *
 */
Route::group([
    'namespace' => 'Auth',
], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('logout', 'LoginController@logout')->name('auth.logout');
        Route::post('password', 'PasswordController@updatePassword');
    });
});

