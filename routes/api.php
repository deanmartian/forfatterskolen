<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\FileController;
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

Route::prefix('v1')->middleware(['cors'])->group(function () {
    Route::prefix('auth-test')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::middleware('apiJwt')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/dashboard', [DashboardController::class, 'show']);
        Route::get('/courses/taken', [CourseController::class, 'taken']);
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
