<?php

use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeClaimController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeClaimReadController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeCustodyController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeFoundItemController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeFoundResponseController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeHandoverController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeItemReadController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeMatchController;
use CampusFind\LostAndFound\Http\Controllers\Employee\EmployeeReportReadController;
use CampusFind\LostAndFound\Http\Controllers\EmployeeCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('app.admin_path').'/lost-found')
    ->middleware(['web', 'admin_locale', 'user'])
    ->group(function () {
        // Lost Reports Management
        Route::get('reports', [EmployeeReportReadController::class, 'index'])
            ->name('admin.lost_found.reports.index');

        Route::post('reports/{id}/approve', [EmployeeReportReadController::class, 'approve'])
            ->name('admin.lost_found.reports.approve');

        Route::post('reports/{id}/reject', [EmployeeReportReadController::class, 'reject'])
            ->name('admin.lost_found.reports.reject');

        Route::get('responses', [EmployeeFoundResponseController::class, 'index'])
            ->name('admin.lost_found.responses.index');
        Route::get('responses/{id}', [EmployeeFoundResponseController::class, 'show'])
            ->name('admin.lost_found.responses.show');
        Route::get('responses/{id}/images/{imageId}', [EmployeeFoundResponseController::class, 'image'])
            ->name('admin.lost_found.responses.images.show');
        Route::post('responses/{id}/review', [EmployeeFoundResponseController::class, 'review'])
            ->name('admin.lost_found.responses.review');
        Route::post('responses/{id}/reject', [EmployeeFoundResponseController::class, 'reject'])
            ->name('admin.lost_found.responses.reject');
        Route::post('responses/{id}/verify', [EmployeeFoundResponseController::class, 'verify'])
            ->name('admin.lost_found.responses.verify');

        Route::get('matches', [EmployeeMatchController::class, 'index'])
            ->name('admin.lost_found.matches.index');
        Route::post('reports/{id}/matches/generate', [EmployeeMatchController::class, 'generateForReport'])
            ->name('admin.lost_found.matches.generate_report');
        Route::post('items/{id}/matches/generate', [EmployeeMatchController::class, 'generateForItem'])
            ->name('admin.lost_found.matches.generate_item');
        Route::post('matches/{id}/review', [EmployeeMatchController::class, 'review'])
            ->name('admin.lost_found.matches.review');
        Route::post('matches/{id}/verify', [EmployeeMatchController::class, 'verify'])
            ->name('admin.lost_found.matches.verify');

        // Found Items Management
        Route::get('items', [EmployeeItemReadController::class, 'index'])
            ->name('admin.lost_found.items.index');

        Route::post('items', [EmployeeFoundItemController::class, 'store'])
            ->name('admin.lost_found.items.store');

        Route::put('items/{id}', [EmployeeFoundItemController::class, 'update'])
            ->name('admin.lost_found.items.update');

        Route::post('items/{id}/approve', [EmployeeFoundItemController::class, 'approve'])
            ->name('admin.lost_found.items.approve');

        Route::post('items/{id}/images', [EmployeeFoundItemController::class, 'uploadImage'])
            ->name('admin.lost_found.items.images.store');

        // Custody Management
        Route::post('items/{id}/custody/receive', [EmployeeCustodyController::class, 'receive'])
            ->name('admin.lost_found.custody.receive');

        Route::post('items/{id}/custody/transfer', [EmployeeCustodyController::class, 'transfer'])
            ->name('admin.lost_found.custody.transfer');

        Route::post('items/{id}/custody/move-storage', [EmployeeCustodyController::class, 'moveStorage'])
            ->name('admin.lost_found.custody.move_storage');

        // Physical Handover
        Route::post('items/{id}/handover', [EmployeeHandoverController::class, 'complete'])
            ->name('admin.lost_found.handover.complete');

        // Claims Management
        Route::get('claims', [EmployeeClaimReadController::class, 'allClaims'])
            ->name('admin.lost_found.claims.index');

        Route::get('items/{id}/claims', [EmployeeClaimReadController::class, 'index'])
            ->name('admin.lost_found.items.claims.index');

        Route::get('claims/{id}', [EmployeeClaimReadController::class, 'show'])
            ->name('admin.lost_found.claims.show');

        Route::get('claims/{id}/evidence/{evidenceId}/file', [EmployeeClaimReadController::class, 'evidenceFile'])
            ->name('admin.lost_found.claims.evidence.file');

        Route::post('claims/{id}/review', [EmployeeClaimController::class, 'review'])
            ->name('admin.lost_found.claims.review');

        Route::post('claims/{id}/approve', [EmployeeClaimController::class, 'approve'])
            ->name('admin.lost_found.claims.approve');

        Route::post('claims/{id}/reject', [EmployeeClaimController::class, 'reject'])
            ->name('admin.lost_found.claims.reject');

        Route::post('claims/{id}/revoke', [EmployeeClaimController::class, 'revoke'])
            ->name('admin.lost_found.claims.revoke');

        // Categories Management
        Route::get('categories', [EmployeeCategoryController::class, 'index'])
            ->name('admin.lost_found.settings.categories.index');

        Route::post('categories', [EmployeeCategoryController::class, 'store'])
            ->name('admin.lost_found.settings.categories.store');

        Route::get('categories/{id}/edit', [EmployeeCategoryController::class, 'edit'])
            ->name('admin.lost_found.settings.categories.edit');

        Route::put('categories/{id}', [EmployeeCategoryController::class, 'update'])
            ->name('admin.lost_found.settings.categories.update');
    });
