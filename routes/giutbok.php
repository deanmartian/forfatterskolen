<?php

if(config('app.app_site') == 'no'){
    $domain = 'giutbok.forfatterskolen.no';
}elseif(config('app.app_site') == 'localhost'){
    $domain = 'giutbok.forfatterskolen.local';
} elseif(config('app.app_site') == 'dev.no'){
    $domain = 'giutbok.forfatterskolen.no';
}

Route::group([
    'domain' => $domain,
], function(){
    Route::group([
        'middleware' => ['giutbok', 'logActivity'],
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
        Route::get('/self-publishing/{id}/learners', 'SelfPublishingController@learners')->name('g-admin.self-publishing.learners');
        Route::post('/self-publishing/{id}/add-feedback', 'SelfPublishingController@addFeedback')->name('g-admin.self-publishing.add-feedback');
        Route::get('/self-publishing/feedback/{feedback_id}/download', 'SelfPublishingController@downloadFeedback')->name('g-admin.self-publishing.download-feedback');
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
        Route::post('/other-service/{id}/add-feedback/{type}', 'OtherServiceController@addFeedback')->name('g-admin.other-service.add-feedback');
        Route::get('/other-service/{id}/download/{type}', 'OtherServiceController@downloadOtherServiceDoc')->name('g-admin.other-service.download-doc'); // Download assignment feedback
        Route::delete('/other-service/{id}/coaching-timer/delete', 'OtherServiceController@deleteCoaching')->name('g-admin.other-service.coaching-timer.delete');

        Route::post('/project/activity/save', 'ProjectController@saveActivity');
        Route::delete('/project/activity/{id}/delete', 'ProjectController@deleteActivity');
        Route::post('/project/{id}/notes/save', 'ProjectController@saveNote');
        Route::post('/project/{id}/whole-book/save', 'ProjectController@saveWholeBook');
        Route::delete('/project/whole-book/{id}/delete', 'ProjectController@deleteWholeBook');
        Route::post('/project/{id}/book/save', 'ProjectController@saveBook');
        Route::delete('/project/book/{id}/delete', 'ProjectController@deleteBook');
        Route::post('/project/{id}/book-pictures/save', 'ProjectController@saveBookPicture')->name('g-admin.project.save-picture');
        Route::delete('/project/book-pictures/{id}/delete', 'ProjectController@deleteBookPicture')->name('g-admin.project.delete-picture');
        Route::post('/project/{id}/book-formatting/save', 'ProjectController@saveBookFormatting')->name('g-admin.project.save-book-formatting');
        Route::delete('/project/book-formatting/{id}/delete', 'ProjectController@deleteBookFormatting')->name('g-admin.project.delete-book-formatting');
        Route::post('/project/{id}/add-other-service', 'ProjectController@addOtherService')->name('g-admin.project.add-other-service');
        Route::get('/project/{id}/graphic-work', 'ProjectController@graphicWork')->name('g-admin.project.graphic-work');
        Route::post('/project/{id}/graphic-work/save', 'ProjectController@saveGraphicWork')->name('g-admin.project.save-graphic-work');
        Route::delete('/project/{id}/graphic-work/{graphic_work_id}/delete', 'ProjectController@deleteGraphicWork')->name('g-admin.project.delete-graphic-work');
        Route::get('/project/{id}/registration', 'ProjectController@registration')->name('g-admin.project.registration');
        Route::post('/project/{id}/registration/save', 'ProjectController@saveRegistration')->name('g-admin.project.save-registration');
        Route::delete('/project/{id}/registration/{registration_id}/delete', 'ProjectController@deleteRegistration')->name('g-admin.project.delete-registration');
        Route::get('/project/{id}/marketing', 'ProjectController@marketing')->name('g-admin.project.marketing');
        Route::post('/project/{id}/marketing/save', 'ProjectController@saveMarketing')->name('g-admin.project.save-marketing');
        Route::delete('/project/{id}/marketing/{marketing_id}/delete', 'ProjectController@deleteMarketing')->name('g-admin.project.delete-marketing');
        Route::get('/project/{id}/contract', 'ProjectController@contract')->name('g-admin.project.contract');
        Route::post('/project/{id}/contract', 'ProjectController@storeContract')->name('g-admin.project.contract-store');
        Route::post('/project/{id}/contract/upload', 'ProjectController@uploadContract')->name('g-admin.project.contract-upload');
        Route::post('/project/{id}/contract/{contract_id}/signed-upload', 'ProjectController@uploadSignedContract')
            ->name('g-admin.project.contract-signed-upload');
        Route::get('/project/{id}/contract/create', 'ProjectController@createContract')->name('g-admin.project.contract-create');
        Route::get('/project/{id}/contract/{contract_id}/edit', 'ProjectController@editContract')->name('g-admin.project.contract-edit');
        Route::put('/project/{id}/contract/{contract_id}/update', 'ProjectController@updateContract')->name('g-admin.project.contract-update');
        Route::get('/project/{id}/contract/{contract_id}', 'ProjectController@showContract')->name('g-admin.project.contract-show');
        Route::get('/project', 'ProjectController@index')->name('g-admin.project.index');
        Route::post('/project/save', 'ProjectController@saveProject');
        Route::get('/project/{id}', 'ProjectController@show')->name('g-admin.project.show');
        Route::delete('/project/{id}/delete', 'ProjectController@deleteProject');

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