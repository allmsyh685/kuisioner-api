<?php

use Illuminate\Support\Facades\Route;

// Handle preflight OPTIONS requests for CORS
Route::options('{any}', function () {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', request()->header('Origin') ?: 'https://game-kuisioner.vercel.app')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, X-XSRF-TOKEN')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

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


