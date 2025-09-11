<?php

use App\Http\Controllers\SentimentController;
use Illuminate\Support\Facades\Route;

Route::prefix('sentiment')->group(function () {
    Route::post('/analyze', [SentimentController::class, 'analyze'])->name('sentiment.analyze');
    Route::post('/analyze-existing', [SentimentController::class, 'analyzeExisting'])->name('sentiment.analyze-existing');
    Route::get('/batch-analyze', [SentimentController::class, 'batchAnalyze'])->name('sentiment.batch-analyze');
    Route::get('/stats', [SentimentController::class, 'getStats'])->name('sentiment.stats');
    Route::get('/health-check', [SentimentController::class, 'healthCheck'])->name('sentiment.health-check');
});
