<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Community\CommunityController;
use App\Http\Controllers\Api\Feedback\FeedbackController;
use App\Http\Controllers\Api\Link\LinkController;
use App\Http\Controllers\Api\Onboarding\OnboardingController;
use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {

    // completed
    Route::post('/register', [
        AuthController::class,
        'register',
    ]);

    // Completed
    Route::post('/verify', [
        OtpController::class,
        'verifyOtp',
    ]);

    // completed
    Route::post('/resend-otp', [
        OtpController::class,
        'resendOtp',
    ]);

    // completed
    Route::post('/login', [
        AuthController::class,
        'login',
    ]);

    // completed
    Route::post('/refresh', [
        AuthController::class,
        'refreshToken',
    ]);

    // completed
    Route::post('/forgot-password', [
        AuthController::class,
        'forgotPassword',
    ]);

    // completed
    Route::post('/reset-password', [
        AuthController::class,
        'resetPassword',
    ]);

    Route::middleware('auth:api')->group(function () {

        Route::get('/me', [
            AuthController::class,
            'me',
        ]);
        
        // Completed
        Route::post('/logout', [
            AuthController::class,
            'logout',
        ]);

    });
});

Route::prefix('categories')->group(function () {
    Route::middleware('auth:api')->group(function () {
        // completed
        Route::get('/collect', [CategoryController::class, 'getCategories']);
        Route::post('/create', [CategoryController::class, 'createCategory']);
        Route::delete('/delete/{id}', [CategoryController::class, 'deleteCategoryById']);
        Route::patch('/edit/{id}', [CategoryController::class, 'updateCategory']);
        Route::get('/{categoryId}/published-links', [CategoryController::class, 'getPublishedLinksByCategory']);
        Route::patch('/published-state', [CategoryController::class, 'togglePublishedCategory']);
    });
});

Route::prefix('links')->group(function () {
    Route::middleware('auth:api')->group(function () {
        // completed
        Route::get('/', [LinkController::class, 'index']);
        Route::post('/create', [LinkController::class, 'create']);
        Route::delete('/{id}', [LinkController::class, 'delete']);
    });
});

Route::prefix('profile')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::patch('/update', [ProfileController::class, 'update']);
    });
});

Route::prefix('community')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [CommunityController::class, 'index']);
    });
});

Route::prefix('feedback')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/', [FeedbackController::class, 'index']);
        Route::post('/create', [FeedbackController::class, 'create']);
        Route::get('/stats', [FeedbackController::class, 'stats']);
    });
});


Route::prefix('onboarding')->group(function () {
    Route::middleware('auth:api')->group(function () {
         Route::patch('/status', [OnboardingController::class, 'update']);
    });

});