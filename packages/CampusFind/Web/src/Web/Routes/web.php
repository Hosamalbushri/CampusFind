<?php

use Illuminate\Support\Facades\Route;
use CampusFind\Web\Web\Http\Controllers\AccountController;
use CampusFind\Web\Web\Http\Controllers\HomeController;
use CampusFind\Web\Web\Http\Controllers\ItemController;
use CampusFind\Web\Web\Http\Controllers\LoginController;
use CampusFind\Web\Web\Http\Controllers\LostReportResponseController;
use CampusFind\Web\Web\Http\Controllers\PageController;
use CampusFind\Web\Web\Http\Controllers\ReportController;

Route::name('campusfind_web.web.')->group(function () {
    Route::get('branding.css', [HomeController::class, 'brandingCss'])->name('branding.css');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('items', [ItemController::class, 'index'])->name('items.index');
    Route::get('items/{reference}', [ItemController::class, 'show'])->name('items.show');
    Route::post('items/{reference}/claim', [ItemController::class, 'storeClaim'])->middleware('throttle:20,1')->name('items.claim');
    Route::get('pages/{page?}', [PageController::class, 'show'])->name('pages.show');

    // Reporting Routes
    Route::get('reports/lost', [ReportController::class, 'createLost'])->name('reports.lost');
    Route::post('reports/lost', [ReportController::class, 'storeLost'])->middleware('throttle:30,1')->name('reports.lost.store');
    Route::get('reports/lost/{reference}/image', [ReportController::class, 'showLostImage'])->name('reports.lost.image');
    Route::get('reports/lost/{reference}', [LostReportResponseController::class, 'show'])->name('lost-reports.show');
    Route::post('reports/lost/{reference}/found-response', [LostReportResponseController::class, 'store'])->middleware('throttle:10,1')->name('lost-reports.responses.store');
    Route::post('reports/lost/{reference}/found-response/cancel', [LostReportResponseController::class, 'cancel'])->middleware('throttle:10,1')->name('lost-reports.responses.cancel');
    Route::get('reports/found', [ReportController::class, 'createFound'])->name('reports.found');
    Route::post('reports/found', [ReportController::class, 'storeFound'])->middleware('throttle:30,1')->name('reports.found.store');

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
