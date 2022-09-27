<?php

if(config('app.app_site') == 'no'){
    $domain = 'giutbok.forfatterskolen.no';
}elseif(config('app.app_site') == 'localhost'){
    $domain = 'giutbok.forfatterskolen.local';
}

Route::group([
    'domain' => $domain,
], function(){
    Route::group([
        'middleware' => ['giutbok'],
        'namespace' => 'Giutbok'
    ], function(){
        Route::get('/', 'PageController@dashboard')->name('g-admin.dashboard');
        Route::post('/change-password', 'PageController@changePassword')->name('editor.change-password');

        Route::post('learner/register', 'LearnerController@registerLearner')->name('g-admin.learner.register');
        Route::group([
            'prefix' => 'learner'
        ], function() {
            Route::get('/', 'LearnerController@index')->name('g-admin.learner.index');
        });

        Route::get('/self-publishing', 'SelfPublishingController@index')->name('g-admin.self-publishing.index');
        Route::get('/self-publishing/{id}/learners', 'SelfPublishingController@learners')->name('g-admin.self-publishing.learners');
        Route::get('learner/{id}', 'LearnerController@show')->name('g-admin.learner.show');
    });

    Route::group([
        'middleware' => 'giutbok',
        'namespace' => 'Backend'
    ], function() {

        Route::post('backend/change-password', 'PageController@changePassword')->name('giutbok.change-password');

        Route::get('/self-publishing/{id}/download-manuscript', 'SelfPublishingController@selfPublishingDownloadManuscript')
            ->name('g-admin.self-publishing.download-manuscript');
        Route::post('/self-publishing/{id}/add-feedback', 'SelfPublishingController@addFeedback')->name('g-admin.self-publishing.add-feedback');
        Route::post('/self-publishing/{id}/add-learners', 'SelfPublishingController@addLearners')->name('g-admin.self-publishing.add-learners');
        Route::delete('/self-publishing/delete-learner/{learner_id}', 'SelfPublishingController@deleteLearner')
            ->name('g-admin.self-publishing.delete-learner');
        Route::resource('/self-publishing', 'SelfPublishingController', [
            'except' => ['create', 'edit', 'index'],
            'names' => [
                'show' => 'g-admin.self-publishing.show',
                'store' => 'g-admin.self-publishing.store',
                'update' => 'g-admin.self-publishing.update',
                'destroy' => 'g-admin.self-publishing.destroy',
            ],
        ]);

        Route::get('learner/generate-password', 'LearnerController@generatePassword');
        Route::post('learner/add_to_workshop', 'LearnerController@addToWorkshop')->name('g-admin.learner.add_to_workshop');
        Route::post('learner/{user_id}/update-is-publishing-learner', 'LearnerController@isPublishingLearner');
        Route::post('learner/{learner_id}/add-email', 'LearnerController@addSecondaryEmail')->name('g-admin.learner.add-email');
        Route::post('learner/{email_id}/set-primary-email', 'LearnerController@setPrimaryEmail')->name('g-admin.learner.set-primary-email');
        Route::delete('learner/{email_id}/delete-secondary-email', 'LearnerController@removeSecondaryEmail')->name('g-admin.learner.remove-secondary-email');
        Route::put('learner/{user_id}', 'LearnerController@update')->name('g-admin.learner.update');
        Route::delete('learner/{user_id}/', 'LearnerController@destroy')->name('g-admin.learner.delete');
        Route::post('learner/{user_id}/auto-renew', 'LearnerController@setAutoRenewCourses')->name('g-admin.learner.update-auto-renew');
        Route::post('learner/{user_id}/could-buy-course', 'LearnerController@setCouldBuyCourse')->name('g-admin.learner.update-could-buy-course');
        Route::post('learner/add_notes/{id}', 'LearnerController@addNotes')->name('g-admin.learner.add_notes');
        Route::post('learner/{learner_id}/send-email', 'LearnerController@sendLearnerEmail')->name('g-admin.learner.send-email');
        Route::post('learner/{learner_id}/set-preferred-editor', 'LearnerController@setPreferredEditor')->name('g-admin.learner.set-preferred-editor');
        Route::post('learner/{user_id}/set-vipss-efaktura', 'LearnerController@setVippsEFaktura')->name('g-admin.learner.set-vipps-e-faktura');
        Route::post('/learner/{id}/add_shop_manuscript', 'LearnerController@addShopManuscript')->name('g-admin.shop-manuscript.add_learner'); // Shop Manuscript add learner
        Route::post('/is-manuscript-locked-status', 'LearnerController@updateManuscriptLockedStatus');
        Route::post('learner/activate_shop_manuscript_taken', 'LearnerController@activate_shop_manuscript_taken')->name('g-admin.activate_shop_manuscript_taken');
        Route::post('learner/delete_shop_manuscript_taken', 'LearnerController@delete_shop_manuscript_taken')->name('g-admin.delete_shop_manuscript_taken');
        Route::post('learner/{learner_id}/add-self-publishing', 'LearnerController@addSelfPublishing')->name('g-admin.learner.add-self-publishing');
        Route::post('/learner/{id}/update_workshop_count', 'LearnerController@updateWorkshopCount')->name('g-admin.learner.update_workshop_count'); // Update workshop count for learner
        Route::post('learner/invoice/{id}/update-due', 'LearnerController@updateInvoiceDue')->name('g-admin.learner.invoice.update-due');
        Route::delete('learner/invoice/{id}/delete', 'LearnerController@deleteInvoice')->name('g-admin.learner.invoice.delete');
        Route::post('learner/invoice/{id}/e-faktura', 'LearnerController@vippsEFaktura')->name('g-admin.learner.invoice.vipps-e-faktura');
        Route::post('learner/invoice/{id}/create-fiken-credit-note', 'LearnerController@addFikenCreditNote')
            ->name('g-admin.learner.invoice.create-fiken-credit-note');
        Route::post('learner/svea/{order_id}/create-credit-note', 'LearnerController@createSveaCreditNote')->name('g-admin.learner.svea.create-credit-note');
        Route::post('learner/svea/{order_id}/deliver-order', 'LearnerController@deliverSveaOrder')->name('g-admin.learner.svea.deliver-order');
        Route::post('/manuscript', 'ManuscriptController@store')->name('g-admin.manuscript.store');
        Route::post('learner/{user_id}/assignment/{id}/delete-add-one', 'LearnerController@deleteAssignmentAddOn')->name('g-admin.learner.assignment.delete-add-one');
        Route::post('learner/{id}/add-other-service', 'LearnerController@addOtherService')->name('g-admin.learner.add-other-service');
        Route::post('learner/{id}/add-coaching-timer', 'LearnerController@addCoachingTimer')->name('g-admin.learner.add-coaching-timer');

        Route::post('assignment_manuscript/{id}/send-email-to-user', 'AssignmentController@emailManuscriptUser')->name('g-assignment.send-email-to-manuscript-user');
        Route::post('assignment/learner-assignment/save/{id?}', 'AssignmentController@learnerAssignment')->name('g-assignment.learner-assignment.save');
        Route::post('assignment/{id}/update-submission-date', 'AssignmentController@updateSubmissionDate')->name('g-assignment.update-submission-date');
        Route::post('assignment/{id}/update-available-date', 'AssignmentController@updateAvailableDate')->name('g-assignment.update-available-date');
        Route::post('assignment/{id}/update-max-words', 'AssignmentController@updateMaxWords')->name('g-assignment.update-max-words');

        Route::post('/other-service/{id}/update-expected-finish/{type}', 'OtherServiceController@updateExpectedFinish')->name('g-admin.other-service.update-expected-finish');
        Route::post('/other-service/{id}/update-status/{type}', 'OtherServiceController@updateStatus')->name('g-admin.other-service.update-status');
        Route::post('other-service/{id}/assign-editor/{type}', 'LearnerController@otherServiceAssignEditor')->name('g-admin.other-service.assign-editor');
        Route::post('other-service/{id}/delete/{type}', 'LearnerController@deleteOtherService')->name('g-admin.other-service.delete');
        Route::post('/other-service/set-approved-date', 'OtherServiceController@setApprovedDate')->name('g-admin.other-service.coaching-timer.set-approved-date');
        Route::post('/other-service/{id}/coaching-timer/set-approve-date', 'OtherServiceController@setCoachingApproveDate')
            ->name('g-admin.other-service.coaching-timer.set-coaching-approve-date');
        Route::post('/other-service/{id}/coaching-timer/set_replay', 'OtherServiceController@setReplay')
            ->name('g-admin.other-service.coaching-timer.set_replay');
        Route::delete('/other-service/{id}/coaching-timer/delete', 'OtherServiceController@deleteCoaching')->name('g-admin.other-service.coaching-timer.delete');

        Route::post('task/{id}/finish', 'TaskController@finishTask')->name('g-admin.task.finish');
        Route::resource('task', 'TaskController', [
            'names' => [
                'index' => 'g-admin.task.index',
                'show' => 'g-admin.task.show',
                'create' => 'g-admin.task.create',
                'store' => 'g-admin.task.store',
                'edit' => 'g-admin.task.edit',
                'update' => 'g-admin.task.update',
                'destroy' => 'g-admin.task.destroy',
            ],
        ]);

        Route::post('/invoice/create-new', 'InvoiceController@addInvoice')->name('g-admin.invoice.new');
        Route::resource('/invoice', 'InvoiceController', [
            'except' => ['create', 'edit'],
            'names' => [
                'index' => 'g-admin.invoice.index',
                'show' => 'g-admin.invoice.show',
                'store' => 'g-admin.invoice.store',
                'update' => 'g-admin.invoice.update',
                'destroy' => 'g-admin.invoice.destroy',
            ],
        ]);
    });

    // Authentication
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::post('login', 'LoginController@giutbokLogin')->name('giutbok.login.store');
    });
});