<?php

use App\Http\Controllers\CsvExport\ItemCsvExportController;
use App\Http\Controllers\CsvExport\TagCsvExportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Stats\ActivitiesPerAgeGroupController;
use App\Http\Controllers\Stats\DashboardController;
use App\Http\Controllers\Stats\ItemControlController;
use App\Http\Controllers\Stats\ThemeUsageController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/item/{hash}/{slug?}', ItemController::class)->name('item');

Route::prefix('stats')->middleware(['auth', 'verified'])->name('stats.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/activities-per-age-group', ActivitiesPerAgeGroupController::class)->name('activities-per-age-group');
    Route::get('/item-control', ItemControlController::class)->name('item-control');

    Route::get('/tag/export', [TagCsvExportController::class, 'export'])->name('tag.export');
    Route::get('/item/export', [ItemCsvExportController::class, 'export'])->name('item.export');

    Route::get('/theme-usage', ThemeUsageController::class)->name('theme-usage');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
