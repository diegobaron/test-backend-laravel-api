<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\SchoolController;

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

Route::post('auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['apiJwt']], function() {
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/course/show/{id}', [CourseController::class, 'show']);
    Route::post('/course/create', [CourseController::class, 'store']);

    Route::get('/schools', [SchoolController::class, 'index']);
    Route::post('/school/create', [SchoolController::class, 'store']);
    Route::get('/school/show/{id}', [SchoolController::class, 'show']);
});
