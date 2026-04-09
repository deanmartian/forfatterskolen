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
    Route::post('/conversation/{id}/execute-tool/{actionId}', [Backend\InboxController::class, 'executeTool'])->name('admin.inbox.execute-tool');
    Route::post('/conversation/{id}/make-public', [Backend\InboxController::class, 'makePublic'])->name('admin.inbox.make-public');
    Route::post('/conversation/{id}/make-private', [Backend\InboxController::class, 'makePrivate'])->name('admin.inbox.make-private');
    Route::post('/conversation/{id}/follow-up', [Backend\InboxController::class, 'setFollowUp'])->name('admin.inbox.follow-up');
    Route::post('/compose', [Backend\InboxController::class, 'compose'])->name('admin.inbox.compose');
    Route::post('/bulk', [Backend\InboxController::class, 'bulk'])->name('admin.inbox.bulk');
    Route::post('/import-helpwise', [Backend\InboxController::class, 'importFromHelpwise'])->name('admin.inbox.import-helpwise');
    Route::get('/canned-responses', [Backend\InboxController::class, 'cannedResponses'])->name('admin.inbox.canned-responses');
    Route::post('/canned-responses', [Backend\InboxController::class, 'storeCannedResponse'])->name('admin.inbox.canned-responses.store');
    Route::get('/attachment/{filename}', [Backend\InboxController::class, 'downloadAttachment'])->name('admin.inbox.attachment');

    // Inline image upload fra paste/drag-and-drop i reply-feltet
    Route::post('/paste-image', [Backend\InboxController::class, 'pasteImage'])->name('admin.inbox.paste-image');

    // Inbox-innstillinger (per-bruker, f.eks. egen signatur)
    Route::get('/settings', [Backend\InboxController::class, 'settings'])->name('admin.inbox.settings');
    Route::post('/settings', [Backend\InboxController::class, 'storeSettings'])->name('admin.inbox.settings.store');
});
