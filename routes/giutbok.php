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
    });

    // Authentication
    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Auth',
    ], function () {
        Route::post('login', 'LoginController@giutbokLogin')->name('giutbok.login.store');
    });
});