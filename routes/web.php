<?php

use App\Http\Controllers\Stats\ActivitiesPerAgeGroup;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('stats')->name('stats.')->group(function () {
    Route::get('/activities-per-age-group', ActivitiesPerAgeGroup::class)->name('activities-per-age-group');
});
