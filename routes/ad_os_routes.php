<?php

use App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Route;

// Inbox Routes
Route::prefix('inbox')->group(function () {
    Route::get('/', [Backend\InboxController::class, 'index'])->name('admin.inbox.index');
    Route::get('/conversation/{id}', [Backend\InboxController::class, 'show'])->name('admin.inbox.show');
    Route::post('/conversation/{id}/reply', [Backend\InboxController::class, 'reply'])->name('admin.inbox.reply');
    Route::post('/conversation/{id}/comment', [Backend\InboxController::class, 'comment'])->name('admin.inbox.comment');
    Route::post('/conversation/{id}/assign', [Backend\InboxController::class, 'assign'])->name('admin.inbox.assign');
    Route::post('/conversation/{id}/status', [Backend\InboxController::class, 'updateStatus'])->name('admin.inbox.status');
    Route::post('/conversation/{id}/star', [Backend\InboxController::class, 'toggleStar'])->name('admin.inbox.toggle-star');
    Route::post('/conversation/{id}/spam', [Backend\InboxController::class, 'markSpam'])->name('admin.inbox.spam');
    Route::post('/conversation/{id}/ai-draft', [Backend\InboxController::class, 'generateAiDraft'])->name('admin.inbox.ai-draft');
    Route::post('/conversation/{id}/follow-up', [Backend\InboxController::class, 'setFollowUp'])->name('admin.inbox.follow-up');
    Route::post('/import-helpwise', [Backend\InboxController::class, 'importFromHelpwise'])->name('admin.inbox.import-helpwise');
    Route::get('/canned-responses', [Backend\InboxController::class, 'cannedResponses'])->name('admin.inbox.canned-responses');
    Route::post('/canned-responses', [Backend\InboxController::class, 'storeCannedResponse'])->name('admin.inbox.canned-responses.store');
});
