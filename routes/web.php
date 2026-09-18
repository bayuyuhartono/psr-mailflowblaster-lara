<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactImportController;
use App\Http\Controllers\ContactTemplateController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\EmailSettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/campaigns');

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('campaigns', [EmailCampaignController::class, 'index'])->name('campaigns.index');
    Route::post('campaigns', [EmailCampaignController::class, 'store'])->middleware('throttle:10,1')->name('campaigns.store');
    Route::get('email-settings', [EmailSettingController::class, 'edit'])->name('email-settings.edit');
    Route::put('email-settings', [EmailSettingController::class, 'update'])->name('email-settings.update');
    Route::post('contacts/import', [ContactImportController::class, 'store'])->name('contacts.import');
    Route::get('contacts/import-template', ContactTemplateController::class)->name('contacts.import-template');
    Route::resource('contacts', ContactController::class);

    Route::middleware('super')->group(function (): void {
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'destroy']);
    });
});
