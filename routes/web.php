<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\Stats\ActivitiesPerAgeGroupController;
use App\Http\Controllers\Stats\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/item/{hash}/{slug?}', ItemController::class)->name('item');

Route::prefix('stats')->name('stats.')->group(function () {
    Route::get('/tag/export', [TagController::class, 'export'])->name('tag.export');

    Route::get('/activities-per-age-group', ActivitiesPerAgeGroupController::class)->name('activities-per-age-group');
});
