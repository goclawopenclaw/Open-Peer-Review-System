<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ReviewController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Submissions
    Route::apiResource('submissions', SubmissionController::class);
    Route::post('submissions/{submission}/submit', [SubmissionController::class, 'submit']);

    // Reviews
    Route::get('/reviews/pending', [ReviewController::class, 'pendingAssignments']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews/{review}', [ReviewController::class, 'show']);
    Route::post('/review-assignments/{assignment}/accept', [ReviewController::class, 'acceptAssignment']);
    Route::post('/review-assignments/{assignment}/decline', [ReviewController::class, 'declineAssignment']);
    Route::get('/submissions/{submission}/reviews', [ReviewController::class, 'submissionReviews']);
});
