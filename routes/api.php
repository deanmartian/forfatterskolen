<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\FreeWebinarController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LessonController;
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
    Route::get('/free-webinars', [FreeWebinarController::class, 'index']);
    Route::get('/free-webinars/{id}', [FreeWebinarController::class, 'show']);

    Route::middleware('apiJwt')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/dashboard', [DashboardController::class, 'show']);
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::get('/invoices/{id}/pdf', [InvoiceController::class, 'pdf']);
        Route::get('/courses/{id}/lessons', [CourseController::class, 'lessons']);
        Route::get('/lessons/{id}', [LessonController::class, 'show']);
        Route::post('/files/signed-upload', [FileController::class, 'signedUpload']);
        Route::get('/files/{file}/signed-download', [FileController::class, 'signedDownload']);
        Route::post('/files/{file}/upload', [FileController::class, 'upload'])
            ->middleware('signed')
            ->name('api.v1.files.upload');
    });

    Route::get('/files/{file}/download', [FileController::class, 'download'])
        ->middleware('signed')
        ->name('api.v1.files.download');
});
