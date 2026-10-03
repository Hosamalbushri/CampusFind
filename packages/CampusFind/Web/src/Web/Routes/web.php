<?php

use Illuminate\Support\Facades\Route;
use CampusFind\Web\Web\Http\Controllers\AccountController;
use CampusFind\Web\Web\Http\Controllers\HomeController;
use CampusFind\Web\Web\Http\Controllers\ItemController;
use CampusFind\Web\Web\Http\Controllers\LoginController;
use CampusFind\Web\Web\Http\Controllers\PageController;

Route::name('campusfind_web.web.')->group(function () {
    Route::get('branding.css', [HomeController::class, 'brandingCss'])->name('branding.css');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('items', [ItemController::class, 'index'])->name('items.index');
    Route::get('items/{reference}', [ItemController::class, 'show'])->name('items.show');
    Route::get('pages/{page?}', [PageController::class, 'show'])->name('pages.show');

    // Authentication Routes
    Route::middleware('guest:student')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');
    });

    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    // Protected routes are only registered when authentication is explicitly enabled
    if ((bool) config('campusfind_web_web.auth.enabled', false)) {
        Route::middleware('campusfind_web_auth')->group(function () {
            Route::get('account/dashboard', [AccountController::class, 'dashboard'])->name('account.dashboard');
        });
    }
});
