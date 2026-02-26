<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('topics', TopicController::class);
        Route::apiResource('languages', LanguageController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('users', UserController::class);
    });
});
