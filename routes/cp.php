<?php

use Illuminate\Support\Facades\Route;
use Infofactory\StatamicAiSocial\Controllers\ConfigController;
use Infofactory\StatamicAiSocial\Controllers\GeneratePostController;

Route::prefix('ai-social')->name('statamic-ai-social.')->group(function () {
    Route::controller(ConfigController::class)->group(function () {
        Route::get('/config', 'edit')->name('config');
        Route::post('/config', 'update')->name('update-config');
    });

    Route::controller(GeneratePostController::class)->group(function () {
        Route::get('/generate/{entry}', 'index')->name('generate-page');
        Route::post('/generate/{entry}', 'generate')->name('generate');
    });
});
