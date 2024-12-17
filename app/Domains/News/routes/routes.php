<?php

use App\Domains\News\Controllers\ArticleController;
use App\Domains\News\Controllers\UserPreferenceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'api'])->group(function () {
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('articles.show');

    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences'])->name('preferences.get');
    Route::post('/preferences', [UserPreferenceController::class, 'setPreferences'])->name('preferences.set');
    Route::get('/personalized-articles', [UserPreferenceController::class, 'getPersonalizedArticles'])->name('preferences.personalized');
});
