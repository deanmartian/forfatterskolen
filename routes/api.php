<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AssignmentController;
use App\Http\Controllers\Api\V1\CalendarController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CoachingTimeController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EmailHistoryController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\FreeManuscriptController;
use App\Http\Controllers\Api\V1\FreeWebinarController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\PortalController;
use App\Http\Controllers\Api\V1\PrivateMessageController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\PublisherBookController;
use App\Http\Controllers\Api\V1\ShopManuscriptController;
use App\Http\Controllers\Api\V1\ShopManuscriptCheckoutController;
use App\Http\Controllers\Api\V1\WebinarController;
use App\Http\Controllers\Api\V1\VippsController;
use App\Http\Controllers\Api\V1\WorkshopController as ApiWorkshopController;
use App\Http\Controllers\Api\V1\WritingGroupController;
use App\Http\Controllers\Api\V1\WordWrittenController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PrivateGroupController;
use App\Http\Controllers\Api\V1\PilotReaderController;
use App\Http\Controllers\Api\V1\SelfPublishingController as ApiSelfPublishingController;
use App\Http\Controllers\Api\V1\ProjectController as ApiProjectController;
use App\Http\Controllers\Api\V1\SurveyController as ApiSurveyController;
use App\Http\Controllers\Api\V1\BookSaleController;
use App\Http\Controllers\Api\V1\UpgradeController;
use App\Http\Controllers\Api\V1\MarketingController;
use App\Http\Controllers\Api\V1\ProgressPlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::prefix('cross-domain')->group(function () {
    Route::post('/get-token', [Auth\LoginController::class, 'crossDomainToken']);
    Route::post('/login', [Auth\LoginController::class, 'crossDomainLogin']);
});

Route::prefix('v1')->middleware(['cors', 'apiRequestId'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::get('/health', [HealthController::class, 'show']);
    Route::get('/courses/for-sale', [CourseController::class, 'forSale']);
    Route::get('/courses/taken', [CourseController::class, 'taken'])
        ->middleware('apiJwt');
    Route::get('/courses/{id}', [CourseController::class, 'showPublic']);
    Route::get('/courses/{id}/plan', [CourseController::class, 'plan']);
    Route::get('/courses/{id}/packages', [CourseController::class, 'packages']);
    Route::get('/free-webinars', [FreeWebinarController::class, 'index']);
    Route::get('/free-webinars/{id}', [FreeWebinarController::class, 'show']);
    Route::post('/free-manuscripts', [FreeManuscriptController::class, 'store']);
    Route::get('/publisher-books', [PublisherBookController::class, 'index']);
    Route::get('/shop-manuscripts/by-word-count', [ShopManuscriptController::class, 'byWordCount']);
    Route::get('/workshops/for-sale', [ApiWorkshopController::class, 'forSale']);
    Route::get('/vipps/fallback', [VippsController::class, 'fallback'])
        ->name('api.v1.vipps.fallback');

    Route::middleware('apiJwt')->group(function () {
        Route::get('/shop-manuscripts/{id}/thankyou', [ShopManuscriptCheckoutController::class, 'thankyou'])
            ->name('api.v1.shop-manuscripts.thankyou');
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::match(['put', 'patch', 'post'], '/profile', [ProfileController::class, 'update']);
        Route::get('/dashboard', [DashboardController::class, 'show']);
        Route::get('/learner/coaching-time', [CoachingTimeController::class, 'index']);
        Route::get('/learner/coaching-time/available', [CoachingTimeController::class, 'available']);
        Route::post('/learner/coaching-time/request', [CoachingTimeController::class, 'request']);
        Route::post('/learner/coaching-time/add-session', [CoachingTimeController::class, 'addSession']);
        Route::get('/calendar/events', [CalendarController::class, 'events']);
        Route::get('/learner/private-messages', [PrivateMessageController::class, 'index']);
        Route::get('/learner/email-history', [EmailHistoryController::class, 'index']);
        Route::get('/learner/email-history/search', [EmailHistoryController::class, 'search']);
        Route::post('/learner/change-portal', [PortalController::class, 'update']);
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'pdf']);
        Route::match(['get', 'post'], '/checkout/courses/{courseId}/discount', [CheckoutController::class, 'discount']);
        Route::post('/checkout/courses/{courseId}/start', [CheckoutController::class, 'startCourseCheckout']);
        Route::get('/checkout/status/{reference}', [CheckoutController::class, 'status']);
        Route::get('/courses/{id}/lessons', [CourseController::class, 'lessons']);
        Route::get('/courses/{id}/webinars', [WebinarController::class, 'courseIndex']);
        Route::get('/courses/certificates/{id}/download', [CourseController::class, 'downloadCertificate']);
        Route::get('/lessons/{id}', [LessonController::class, 'show']);
        Route::post('/files/signed-upload', [FileController::class, 'signedUpload']);
        Route::get('/files/{file}/signed-download', [FileController::class, 'signedDownload']);
        Route::post('/files/{file}/upload', [FileController::class, 'upload'])
            ->middleware('signed')
            ->name('api.v1.files.upload');
        Route::post('/documents/convert-to-docx', [App\Http\Controllers\Frontend\DocumentConversionController::class, 'convertToDocx'])
            ->name('api.v1.documents.convert-to-docx');
        Route::get('/assignments', [AssignmentController::class, 'index']);
        Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
        Route::post('/assignments/{id}/submit', [AssignmentController::class, 'submit']);
        Route::post('/assignments/submissions/{id}/replace', [AssignmentController::class, 'replaceSubmission']);
        Route::get('/assignments/submissions/{id}/download', [AssignmentController::class, 'downloadSubmission']);
        Route::get('/assignments/feedback/{id}/download', [AssignmentController::class, 'downloadFeedback']);
        Route::get('/webinars', [WebinarController::class, 'index']);
        Route::match(['get', 'post'], '/learner/course-webinar', [WebinarController::class, 'learnerCourseWebinar']);
        Route::get('/webinars/{id}', [WebinarController::class, 'show']);
        Route::get('/webinars/{id}/join', [WebinarController::class, 'join']);
        Route::post('/webinars/{id}/register', [WebinarController::class, 'register']);
        Route::get('/learner/shop-manuscripts', [ShopManuscriptController::class, 'index']);
        Route::get('/learner/shop-manuscripts/{id}', [ShopManuscriptController::class, 'show']);
        Route::get('/learner/shop-manuscripts/{id}/download/synopsis', [ShopManuscriptController::class, 'downloadSynopsis']);
        Route::get('/learner/shop-manuscripts/{id}/download/{type}', [ShopManuscriptController::class, 'download']);
        Route::get('/learner/shop-manuscripts/{id}/feedback/{feedbackId}/download', [ShopManuscriptController::class, 'downloadFeedback']);
        Route::post('/learner/shop-manuscripts/{id}/comments', [ShopManuscriptController::class, 'postComment']);
        Route::post('/learner/shop-manuscripts/{id}/upload', [ShopManuscriptController::class, 'upload']);
        Route::post('/learner/shop-manuscripts/{id}/upload-synopsis', [ShopManuscriptController::class, 'uploadSynopsis']);
        Route::post('/learner/shop-manuscripts/{id}/update-uploaded', [ShopManuscriptController::class, 'updateUploaded']);
        Route::delete('/learner/shop-manuscripts/{id}/uploaded', [ShopManuscriptController::class, 'deleteUploaded']);
        Route::post('/learner/shop-manuscripts/{id}/checkout', [ShopManuscriptCheckoutController::class, 'store']);
        Route::get('/learner/shop-manuscripts/checkout/{orderId}', [ShopManuscriptCheckoutController::class, 'show']);
        Route::post('/learner/shop-manuscripts/checkout/{orderId}/cancel', [ShopManuscriptCheckoutController::class, 'cancel']);

        // Workshops
        Route::get('/workshops', [ApiWorkshopController::class, 'index']);
        Route::get('/workshops/{id}', [ApiWorkshopController::class, 'show']);

        // Writing Groups
        Route::get('/writing-groups', [WritingGroupController::class, 'index']);
        Route::get('/writing-groups/{id}', [WritingGroupController::class, 'show']);
        Route::put('/writing-groups/{id}', [WritingGroupController::class, 'update']);

        // Word Written
        Route::get('/word-written', [WordWrittenController::class, 'index']);
        Route::post('/word-written', [WordWrittenController::class, 'store']);
        Route::get('/word-written/goals', [WordWrittenController::class, 'goals']);
        Route::post('/word-written/goals', [WordWrittenController::class, 'storeGoal']);
        Route::put('/word-written/goals/{id}', [WordWrittenController::class, 'updateGoal']);
        Route::delete('/word-written/goals/{id}', [WordWrittenController::class, 'deleteGoal']);
        Route::get('/word-written/goals/{id}/statistic', [WordWrittenController::class, 'goalStatistic']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

        // Private Groups
        Route::get('/private-groups', [PrivateGroupController::class, 'index']);
        Route::post('/private-groups', [PrivateGroupController::class, 'store']);
        Route::get('/private-groups/{id}', [PrivateGroupController::class, 'show']);
        Route::put('/private-groups/{id}', [PrivateGroupController::class, 'update']);
        Route::get('/private-groups/{id}/discussions', [PrivateGroupController::class, 'discussions']);
        Route::post('/private-groups/{id}/discussions', [PrivateGroupController::class, 'storeDiscussion']);
        Route::get('/private-groups/{groupId}/discussions/{discussionId}', [PrivateGroupController::class, 'showDiscussion']);
        Route::post('/private-groups/{groupId}/discussions/{discussionId}/replies', [PrivateGroupController::class, 'storeReply']);
        Route::get('/private-groups/{id}/members', [PrivateGroupController::class, 'members']);
        Route::get('/private-groups/{id}/books', [PrivateGroupController::class, 'books']);

        // Pilot Reader (Book Author)
        Route::get('/books', [PilotReaderController::class, 'books']);
        Route::post('/books', [PilotReaderController::class, 'storeBook']);
        Route::get('/books/{id}', [PilotReaderController::class, 'showBook']);
        Route::put('/books/{id}', [PilotReaderController::class, 'updateBook']);
        Route::delete('/books/{id}', [PilotReaderController::class, 'deleteBook']);
        Route::get('/books/{bookId}/chapters', [PilotReaderController::class, 'chapters']);
        Route::post('/books/{bookId}/chapters', [PilotReaderController::class, 'storeChapter']);
        Route::get('/books/{bookId}/chapters/{chapterId}', [PilotReaderController::class, 'showChapter']);
        Route::put('/books/{bookId}/chapters/{chapterId}', [PilotReaderController::class, 'updateChapter']);
        Route::delete('/books/{bookId}/chapters/{chapterId}', [PilotReaderController::class, 'deleteChapter']);
        Route::post('/books/{bookId}/chapters/sort', [PilotReaderController::class, 'sortChapters']);
        Route::get('/books/{bookId}/readers', [PilotReaderController::class, 'readers']);
        Route::get('/books/{bookId}/invitations', [PilotReaderController::class, 'invitations']);
        Route::get('/chapters/{chapterId}/notes', [PilotReaderController::class, 'chapterNotes']);

        // Self-Publishing
        Route::get('/self-publishing', [ApiSelfPublishingController::class, 'index']);
        Route::get('/self-publishing/{id}', [ApiSelfPublishingController::class, 'show']);
        Route::get('/self-publishing/orders', [ApiSelfPublishingController::class, 'orders']);
        Route::get('/self-publishing/feedback/{id}/download', [ApiSelfPublishingController::class, 'downloadFeedback']);

        // Projects
        Route::get('/projects', [ApiProjectController::class, 'index']);
        Route::post('/projects', [ApiProjectController::class, 'store']);
        Route::get('/projects/{id}', [ApiProjectController::class, 'show']);
        Route::post('/projects/{id}/set-standard', [ApiProjectController::class, 'setStandard']);
        Route::get('/projects/{id}/graphic-work', [ApiProjectController::class, 'graphicWork']);
        Route::get('/projects/{id}/registration', [ApiProjectController::class, 'registration']);
        Route::get('/projects/{id}/contracts', [ApiProjectController::class, 'contracts']);
        Route::get('/projects/{id}/invoices', [ApiProjectController::class, 'invoices']);
        Route::get('/projects/{id}/storage', [ApiProjectController::class, 'storage']);
        Route::get('/projects/{id}/marketing', [ApiProjectController::class, 'marketing']);

        // Surveys
        Route::get('/surveys/{id}', [ApiSurveyController::class, 'show']);
        Route::post('/surveys/{id}/submit', [ApiSurveyController::class, 'submit']);

        // Book Sales
        Route::get('/book-sales', [BookSaleController::class, 'index']);
        Route::get('/book-sales/{id}', [BookSaleController::class, 'show']);
        Route::post('/book-sales', [BookSaleController::class, 'store']);
        Route::delete('/book-sales/{id}', [BookSaleController::class, 'destroy']);
        Route::get('/book-sales/by-month/{year}', [BookSaleController::class, 'salesByMonth']);
        Route::get('/book-sales/monthly-details/{year}/{month}', [BookSaleController::class, 'monthlyDetails']);

        // Upgrades
        Route::get('/upgrades', [UpgradeController::class, 'index']);
        Route::get('/upgrades/course/{courseTakenId}/package/{packageId}', [UpgradeController::class, 'courseUpgradeDetails']);

        // Marketing Plans
        Route::get('/marketing-plans', [MarketingController::class, 'index']);
        Route::get('/marketing-plans/{id}', [MarketingController::class, 'show']);
        Route::post('/projects/{projectId}/marketing-plan/answer', [MarketingController::class, 'saveAnswer']);

        // Progress Plan
        Route::get('/progress-plan', [ProgressPlanController::class, 'index']);
        Route::get('/progress-plan/{step}', [ProgressPlanController::class, 'show']);
        Route::post('/progress-plan/manuscripts/upload', [ProgressPlanController::class, 'uploadManuscript']);
    });

    Route::post('/payments/vipps/shop-manuscripts/webhook', [ShopManuscriptCheckoutController::class, 'vippsWebhook']);

    Route::get('/files/{file}/download', [FileController::class, 'download'])
        ->middleware('signed')
        ->name('api.v1.files.download');
});
