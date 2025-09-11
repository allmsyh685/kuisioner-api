<?php

use Illuminate\Support\Facades\Route;

// Apply shared token middleware to all API routes
Route::middleware('shared.token')->group(function () {
    // Public endpoints (now require Bearer token)
    Route::get('/questions', [\App\Http\Controllers\QuestionController::class, 'index']);
    Route::post('/responses', [\App\Http\Controllers\ResponseController::class, 'store']);
    Route::post('/scores', [\App\Http\Controllers\ScoreController::class, 'store']);
    Route::get('/scores/leaderboard', [\App\Http\Controllers\ScoreController::class, 'leaderboard']);
    Route::get('/scores/rank', [\App\Http\Controllers\ScoreController::class, 'rank']);

    // Admin endpoints
    Route::prefix('admin')->group(function () {
        Route::get('/questions', [\App\Http\Controllers\Admin\QuestionAdminController::class, 'index']);
        Route::post('/questions', [\App\Http\Controllers\Admin\QuestionAdminController::class, 'store']);
        Route::put('/questions/{id}', [\App\Http\Controllers\Admin\QuestionAdminController::class, 'update']);
        Route::delete('/questions/{id}', [\App\Http\Controllers\Admin\QuestionAdminController::class, 'destroy']);

        Route::get('/responses', [\App\Http\Controllers\Admin\ResponseAdminController::class, 'index']);
        // Place specific routes BEFORE the /{id} param route to avoid collisions
        Route::get('/responses/statistics', [\App\Http\Controllers\Admin\ResponseAdminController::class, 'statistics']);
        Route::get('/responses/export', [\App\Http\Controllers\Admin\ResponseAdminController::class, 'export']);
        Route::get('/responses/{id}', [\App\Http\Controllers\Admin\ResponseAdminController::class, 'show']);
    });
});


