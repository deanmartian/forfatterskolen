<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Backend;
use App\Http\Controllers\Giutbok;
use Illuminate\Support\Facades\Route;

if (config('app.app_site') == 'no') {
    $domain = 'giutbok.forfatterskolen.no';
} elseif (config('app.app_site') == 'localhost') {
    $domain = 'giutbok.forfatterskolen.local';
} elseif (config('app.app_site') == 'dev.no') {
    $domain = 'giutbok.forfatterskolen.no';
}

Route::domain($domain)->group(function () {
    Route::get('learner/generate-password', [\Backend\LearnerController::class, 'generatePassword']);

    Route::middleware('giutbok', 'logActivity')->group(function () {
        Route::get('/', [Giutbok\PageController::class, 'dashboard'])->name('g-admin.dashboard');
        Route::post('/change-password', [Giutbok\PageController::class, 'changePassword'])->name('g-admin.change-password');

        Route::post('/project/book-formatting/{id}/feedback', [Giutbok\PageController::class, 'addBookFormatFeedback'])
            ->name('g-admin.book-format.add-feedback');

        Route::post('learner/register', [Giutbok\LearnerController::class, 'registerLearner'])->name('g-admin.learner.register');
        Route::prefix('learner')->group(function () {
            Route::get('/', [Giutbok\LearnerController::class, 'index'])->name('g-admin.learner.index');
        });

        Route::get('/self-publishing', [Giutbok\SelfPublishingController::class, 'index'])->name('g-admin.self-publishing.index');
        Route::get('/self-publishing/{id}/learners', [Giutbok\SelfPublishingController::class, 'learners'])->name('g-admin.self-publishing.learners');
        Route::get('learner/{id}', [Giutbok\LearnerController::class, 'show'])->name('g-admin.learner.show');
        Route::post('/project/whole-book/{id}', [Giutbok\PageController::class, 'updateProjectWholeBook'])->name('g-admin.project-whole-book.update');

        Route::get('/dropbox/shared-link/{path}', [Giutbok\PageController::class, 'createSharedLink'])
            ->where('path', '.*')
            ->name('g-admin.dropbox.shared_link');
        Route::get('/dropbox/download/{path}', [Giutbok\PageController::class, 'downloadFile'])
            ->where('path', '.*')
            ->name('g-admin.dropbox.download_file');
    });

    Route::middleware('giutbok')->group(function () {

        Route::post('backend/change-password', [Backend\PageController::class, 'changePassword'])->name('giutbok.change-password');

        Route::get('/self-publishing/{id}/download-manuscript', [Backend\SelfPublishingController::class, 'selfPublishingDownloadManuscript'])
            ->name('g-admin.self-publishing.download-manuscript');
        Route::get('/self-publishing/{id}/learners', [Backend\SelfPublishingController::class, 'learners'])->name('g-admin.self-publishing.learners');
        Route::post('/self-publishing/{id}/add-feedback', [Backend\SelfPublishingController::class, 'addFeedback'])->name('g-admin.self-publishing.add-feedback');
        Route::get('/self-publishing/feedback/{feedback_id}/download', [Backend\SelfPublishingController::class, 'downloadFeedback'])->name('g-admin.self-publishing.download-feedback');
        Route::post('/self-publishing/{id}/add-learners', [Backend\SelfPublishingController::class, 'addLearners'])->name('g-admin.self-publishing.add-learners');
        Route::delete('/self-publishing/delete-learner/{learner_id}', [Backend\SelfPublishingController::class, 'deleteLearner'])
            ->name('g-admin.self-publishing.delete-learner');
        Route::resource('/self-publishing', Backend\SelfPublishingController::class, [
            'names' => [
                'show' => 'g-admin.self-publishing.show',
                'store' => 'g-admin.self-publishing.store',
                'update' => 'g-admin.self-publishing.update',
                'destroy' => 'g-admin.self-publishing.destroy',
            ],
        ])->except('create', 'edit', 'index');

        Route::get('learner/generate-password', [Backend\LearnerController::class, 'generatePassword']);
        Route::post('learner/add_to_workshop', [Backend\LearnerController::class, 'addToWorkshop'])->name('g-admin.learner.add_to_workshop');
        Route::post('learner/{user_id}/update-is-publishing-learner', [Backend\LearnerController::class, 'isPublishingLearner']);
        Route::post('learner/{learner_id}/add-email', [Backend\LearnerController::class, 'addSecondaryEmail'])->name('g-admin.learner.add-email');
        Route::post('learner/{email_id}/set-primary-email', [Backend\LearnerController::class, 'setPrimaryEmail'])->name('g-admin.learner.set-primary-email');
        Route::delete('learner/{email_id}/delete-secondary-email', [Backend\LearnerController::class, 'removeSecondaryEmail'])->name('g-admin.learner.remove-secondary-email');
        Route::put('learner/{user_id}', [Backend\LearnerController::class, 'update'])->name('g-admin.learner.update');
        Route::delete('learner/{user_id}/', [Backend\LearnerController::class, 'destroy'])->name('g-admin.learner.delete');
        Route::post('learner/{user_id}/auto-renew', [Backend\LearnerController::class, 'setAutoRenewCourses'])->name('g-admin.learner.update-auto-renew');
        Route::post('learner/{user_id}/could-buy-course', [Backend\LearnerController::class, 'setCouldBuyCourse'])->name('g-admin.learner.update-could-buy-course');
        Route::post('learner/add_notes/{id}', [Backend\LearnerController::class, 'addNotes'])->name('g-admin.learner.add_notes');
        Route::post('learner/{learner_id}/send-email', [Backend\LearnerController::class, 'sendLearnerEmail'])->name('g-admin.learner.send-email');
        Route::post('learner/{learner_id}/set-preferred-editor', [Backend\LearnerController::class, 'setPreferredEditor'])->name('g-admin.learner.set-preferred-editor');
        Route::post('learner/{user_id}/set-vipss-efaktura', [Backend\LearnerController::class, 'setVippsEFaktura'])->name('g-admin.learner.set-vipps-e-faktura');
        Route::post('/learner/{id}/add_shop_manuscript', [Backend\LearnerController::class, 'addShopManuscript'])->name('g-admin.shop-manuscript.add_learner'); // Shop Manuscript add learner
        Route::post('/is-manuscript-locked-status', [Backend\LearnerController::class, 'updateManuscriptLockedStatus']);
        Route::post('learner/activate_shop_manuscript_taken', [Backend\LearnerController::class, 'activate_shop_manuscript_taken'])->name('g-admin.activate_shop_manuscript_taken');
        Route::post('learner/delete_shop_manuscript_taken', [Backend\LearnerController::class, 'delete_shop_manuscript_taken'])->name('g-admin.delete_shop_manuscript_taken');
        Route::post('learner/{learner_id}/add-self-publishing', [Backend\LearnerController::class, 'addSelfPublishing'])->name('g-admin.learner.add-self-publishing');
        Route::post('/learner/{id}/update_workshop_count', [Backend\LearnerController::class, 'updateWorkshopCount'])->name('g-admin.learner.update_workshop_count'); // Update workshop count for learner
        Route::post('learner/invoice/{id}/update-due', [Backend\LearnerController::class, 'updateInvoiceDue'])->name('g-admin.learner.invoice.update-due');
        Route::delete('learner/invoice/{id}/delete', [Backend\LearnerController::class, 'deleteInvoice'])->name('g-admin.learner.invoice.delete');
        Route::post('learner/invoice/{id}/e-faktura', [Backend\LearnerController::class, 'vippsEFaktura'])->name('g-admin.learner.invoice.vipps-e-faktura');
        Route::post('learner/invoice/{id}/create-fiken-credit-note', [Backend\LearnerController::class, 'addFikenCreditNote'])
            ->name('g-admin.learner.invoice.create-fiken-credit-note');
        Route::post('learner/svea/{order_id}/create-credit-note', [Backend\LearnerController::class, 'createSveaCreditNote'])->name('g-admin.learner.svea.create-credit-note');
        Route::post('learner/svea/{order_id}/deliver-order', [Backend\LearnerController::class, 'deliverSveaOrder'])->name('g-admin.learner.svea.deliver-order');
        Route::post('/manuscript', [Backend\ManuscriptController::class, 'store'])->name('g-admin.manuscript.store');
        Route::post('learner/{user_id}/assignment/{id}/delete-add-one', [Backend\LearnerController::class, 'deleteAssignmentAddOn'])->name('g-admin.learner.assignment.delete-add-one');
        Route::post('learner/{id}/add-other-service', [Backend\LearnerController::class, 'addOtherService'])->name('g-admin.learner.add-other-service');
        Route::post('learner/{id}/add-coaching-timer', [Backend\LearnerController::class, 'addCoachingTimer'])->name('g-admin.learner.add-coaching-timer');

        Route::post('assignment_manuscript/{id}/send-email-to-user', [Backend\AssignmentController::class, 'emailManuscriptUser'])->name('g-assignment.send-email-to-manuscript-user');
        Route::post('assignment/learner-assignment/save/{id?}', [Backend\AssignmentController::class, 'learnerAssignment'])->name('g-assignment.learner-assignment.save');
        Route::post('assignment/{id}/update-submission-date', [Backend\AssignmentController::class, 'updateSubmissionDate'])->name('g-assignment.update-submission-date');
        Route::post('assignment/{id}/update-available-date', [Backend\AssignmentController::class, 'updateAvailableDate'])->name('g-assignment.update-available-date');
        Route::post('assignment/{id}/update-max-words', [Backend\AssignmentController::class, 'updateMaxWords'])->name('g-assignment.update-max-words');

        Route::post('/other-service/{id}/update-expected-finish/{type}', [Backend\OtherServiceController::class, 'updateExpectedFinish'])->name('g-admin.other-service.update-expected-finish');
        Route::post('/other-service/{id}/update-status/{type}', [Backend\OtherServiceController::class, 'updateStatus'])->name('g-admin.other-service.update-status');
        Route::post('/other-service/{id}/lock-status/{type}', [Backend\OtherServiceController::class, 'updateLocked'])->name('g-admin.other-service.update-locked');
        Route::post('other-service/{id}/assign-editor/{type}', [Backend\LearnerController::class, 'otherServiceAssignEditor'])->name('g-admin.other-service.assign-editor');
        Route::post('other-service/{id}/delete/{type}', [Backend\LearnerController::class, 'deleteOtherService'])->name('g-admin.other-service.delete');
        Route::post('/other-service/set-approved-date', [Backend\OtherServiceController::class, 'setApprovedDate'])->name('g-admin.other-service.coaching-timer.set-approved-date');
        Route::post('/other-service/{id}/coaching-timer/set-approve-date', [Backend\OtherServiceController::class, 'setCoachingApproveDate'])
            ->name('g-admin.other-service.coaching-timer.set-coaching-approve-date');
        Route::post('/other-service/{id}/coaching-timer/set_replay', [Backend\OtherServiceController::class, 'setReplay'])
            ->name('g-admin.other-service.coaching-timer.set_replay');
        Route::post('/other-service/{id}/add-feedback/{type}', [Backend\OtherServiceController::class, 'addFeedback'])->name('g-admin.other-service.add-feedback');
        Route::get('/other-service/{id}/download/{type}', [Backend\OtherServiceController::class, 'downloadOtherServiceDoc'])->name('g-admin.other-service.download-doc'); // Download assignment feedback
        Route::delete('/other-service/{id}/coaching-timer/delete', [Backend\OtherServiceController::class, 'deleteCoaching'])->name('g-admin.other-service.coaching-timer.delete');

        Route::post('/task/save', [Backend\ProjectController::class, 'saveTask'])->name('g-admin.project-task.save');
        Route::post('/project/task/{id}/finish', [Backend\ProjectController::class, 'finishTask']);
        Route::delete('/project/task/{id}/delete', [Backend\ProjectController::class, 'deleteTask']);
        Route::post('/project/activity/save', [Backend\ProjectController::class, 'saveActivity']);
        Route::delete('/project/activity/{id}/delete', [Backend\ProjectController::class, 'deleteActivity']);
        Route::post('/project/{id}/notes/save', [Backend\ProjectController::class, 'saveNote']);
        Route::post('/project/{id}/learner/add', [Backend\ProjectController::class, 'addLearner']);
        Route::post('/project/{id}/whole-book/save', [Backend\ProjectController::class, 'saveWholeBook']);
        Route::delete('/project/whole-book/{id}/delete', [Backend\ProjectController::class, 'deleteWholeBook']);
        Route::post('/project/{id}/book/save', [Backend\ProjectController::class, 'saveBook']);
        Route::delete('/project/book/{id}/delete', [Backend\ProjectController::class, 'deleteBook']);
        Route::post('/project/{id}/book-pictures/save', [Backend\ProjectController::class, 'saveBookPicture'])->name('g-admin.project.save-picture');
        Route::delete('/project/book-pictures/{id}/delete', [Backend\ProjectController::class, 'deleteBookPicture'])->name('g-admin.project.delete-picture');
        Route::post('/project/{id}/book-formatting/save', [Backend\ProjectController::class, 'saveBookFormatting'])->name('g-admin.project.save-book-formatting');
        Route::delete('/project/book-formatting/{id}/delete', [Backend\ProjectController::class, 'deleteBookFormatting'])->name('g-admin.project.delete-book-formatting');
        Route::post('/project/{id}/add-other-service', [Backend\ProjectController::class, 'addOtherService'])->name('g-admin.project.add-other-service');
        Route::get('/project/{id}/graphic-work', [Backend\ProjectController::class, 'graphicWork'])->name('g-admin.project.graphic-work');
        Route::post('/project/{id}/graphic-work/save', [Backend\ProjectController::class, 'saveGraphicWork'])->name('g-admin.project.save-graphic-work');
        Route::delete('/project/{id}/graphic-work/{graphic_work_id}/delete', [Backend\ProjectController::class, 'deleteGraphicWork'])->name('g-admin.project.delete-graphic-work');
        Route::get('/project/{id}/cover/{cover_id}', [Backend\ProjectController::class, 'cover'])->name('g-admin.project.cover.show');
        Route::get('/project/{id}/registration', [Backend\ProjectController::class, 'registration'])->name('g-admin.project.registration');
        Route::post('/project/{id}/registration/save', [Backend\ProjectController::class, 'saveRegistration'])->name('g-admin.project.save-registration');
        Route::delete('/project/{id}/registration/{registration_id}/delete', [Backend\ProjectController::class, 'deleteRegistration'])->name('g-admin.project.delete-registration');
        Route::get('/project/{id}/marketing', [Backend\ProjectController::class, 'marketing'])->name('g-admin.project.marketing');
        Route::post('/project/{id}/marketing/save', [Backend\ProjectController::class, 'saveMarketing'])->name('g-admin.project.save-marketing');
        Route::delete('/project/{id}/marketing/{marketing_id}/delete', [Backend\ProjectController::class, 'deleteMarketing'])->name('g-admin.project.delete-marketing');
        Route::get('/project/{id}/marketing-plan', [Backend\ProjectController::class, 'marketingPlan'])->name('g-admin.project.marketing-plan');
        Route::get('/project/{id}/contract', [Backend\ProjectController::class, 'contract'])->name('g-admin.project.contract');
        Route::post('/project/{id}/contract', [Backend\ProjectController::class, 'storeContract'])->name('g-admin.project.contract-store');
        Route::post('/project/{id}/contract/upload', [Backend\ProjectController::class, 'uploadContract'])->name('g-admin.project.contract-upload');
        Route::post('/project/{id}/contract/{contract_id}/signed-upload', [Backend\ProjectController::class, 'uploadSignedContract'])
            ->name('g-admin.project.contract-signed-upload');
        Route::get('/project/{id}/contract/create', [Backend\ProjectController::class, 'createContract'])->name('g-admin.project.contract-create');
        Route::get('/project/{id}/contract/{contract_id}/edit', [Backend\ProjectController::class, 'editContract'])->name('g-admin.project.contract-edit');
        Route::put('/project/{id}/contract/{contract_id}/update', [Backend\ProjectController::class, 'updateContract'])->name('g-admin.project.contract-update');
        Route::get('/project/{id}/contract/{contract_id}', [Backend\ProjectController::class, 'showContract'])->name('g-admin.project.contract-show');
        Route::get('/project/{id}/invoice', [Backend\ProjectController::class, 'invoice'])->name('g-admin.project.invoice');
        Route::post('/project/{id}/invoice/save', [Backend\ProjectController::class, 'saveInvoice'])->name('g-admin.project.invoice.save');
        Route::delete('/project/{id}/invoice/{invoice_id}/delete', [Backend\ProjectController::class, 'deleteInvoice'])->name('g-admin.project.invoice.delete');
        Route::post('/project/{id}/manual-invoice/save', [Backend\ProjectController::class, 'saveManualInvoice'])->name('g-admin.project.manual-invoice.save');
        Route::delete('/project/{id}/manual-invoice/{invoice_id}/delete', [Backend\ProjectController::class, 'deleteManualInvoice'])->name('g-admin.project.manual-invoice.delete');
        Route::get('/project/{id}/storage', [Backend\ProjectController::class, 'storage'])->name('g-admin.project.storage');
        Route::post('/project/{id}/storage', [Backend\ProjectController::class, 'storage'])->name('g-admin.project.storage.submit');
        Route::post('/project/{id}/storage/save-book', [Backend\ProjectController::class, 'saveStorageBook'])->name('g-admin.project.storage.save-book');
        Route::delete('/project/{id}/storage/delete', [Backend\ProjectController::class, 'deleteStorageBook'])->name('g-admin.project.storage.delete-book');
        Route::post('/project/book/{id}/storage/save-details', [Backend\ProjectController::class, 'saveStorageBookDetails'])->name('g-admin.project.storage.save-details');
        Route::post('/project/book/{id}/storage/save-various', [Backend\ProjectController::class, 'saveStorageVarious'])->name('g-admin.project.storage.save-various');
        Route::get('/project/{id}/e-book', [Backend\ProjectController::class, 'ebook'])->name('g-admin.project.ebook');
        Route::post('/project/{id}/e-book/save', [Backend\ProjectController::class, 'saveEbook'])->name('g-admin.project.save-ebook');
        Route::delete('/project/{id}/e-book/{ebook_id}/delete', [Backend\ProjectController::class, 'deleteEbook'])->name('g-admin.project.delete-ebook');
        Route::get('/project/{id}/audio', [Backend\ProjectController::class, 'audio'])->name('g-admin.project.audio');
        Route::post('/project/{id}/audio/save', [Backend\ProjectController::class, 'saveAudio'])->name('g-admin.project.save-audio');
        Route::delete('/project/{id}/audio/{audio_id}/delete', [Backend\ProjectController::class, 'deleteAudio'])->name('g-admin.project.delete-audio');
        Route::get('/project/{id}/print', [Backend\ProjectController::class, 'print'])->name('g-admin.project.print');
        Route::post('/project/{id}/print/save', [Backend\ProjectController::class, 'savePrint'])->name('g-admin.project.save-print');
        Route::get('/project', [Backend\ProjectController::class, 'index'])->name('g-admin.project.index');
        Route::post('/project/save', [Backend\ProjectController::class, 'saveProject']);
        Route::get('/project/{id}', [Backend\ProjectController::class, 'show'])->name('g-admin.project.show');
        Route::delete('/project/{id}/delete', [Backend\ProjectController::class, 'deleteProject']);

        Route::post('task/{id}/finish', [Backend\TaskController::class, 'finishTask'])->name('g-admin.task.finish');
        Route::resource('task', Backend\TaskController::class, [
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

        Route::post('/invoice/create-new', [Backend\InvoiceController::class, 'addInvoice'])->name('g-admin.invoice.new');
        Route::resource('/invoice', Backend\InvoiceController::class, [
            'names' => [
                'index' => 'g-admin.invoice.index',
                'show' => 'g-admin.invoice.show',
                'store' => 'g-admin.invoice.store',
                'update' => 'g-admin.invoice.update',
                'destroy' => 'g-admin.invoice.destroy',
            ],
        ])->except('create', 'edit');
    });

    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('login', [Auth\LoginController::class, 'giutbokLogin'])->name('giutbok.login.store');
        Route::get('login/email-redirect/{email}/{redirect_link}', [Auth\LoginController::class, 'giutbokEmailLoginRedirect'])
            ->name('giutbok.login.emailRedirect');
    });
});
