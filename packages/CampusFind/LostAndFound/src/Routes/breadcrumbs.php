<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Lost & Found Items
Breadcrumbs::for('admin.lost_found.items.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('lost_found::app.employee.items.title'), route('admin.lost_found.items.index'));
});

// Dashboard > Lost & Found Claims
Breadcrumbs::for('admin.lost_found.claims.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('lost_found::app.employee.claims.title'), route('admin.lost_found.claims.index'));
});

// Dashboard > Lost & Found Reports
Breadcrumbs::for('admin.lost_found.reports.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('lost_found::app.employee.reports.title'), route('admin.lost_found.reports.index'));
});

Breadcrumbs::for('admin.lost_found.matches.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('lost_found::app.employee.matches.title'), route('admin.lost_found.matches.index'));
});

// Dashboard > Lost & Found Items > Claims
Breadcrumbs::for('admin.lost_found.items.claims.index', function (BreadcrumbTrail $trail, $itemId) {
    $trail->parent('admin.lost_found.items.index');
    $trail->push(trans('lost_found::app.employee.claims.title'), route('admin.lost_found.items.claims.index', $itemId));
});

// Dashboard > Lost & Found Items > Claim Detail
Breadcrumbs::for('admin.lost_found.claims.show', function (BreadcrumbTrail $trail, $claimId) {
    $trail->parent('admin.lost_found.claims.index');
    $trail->push(trans('lost_found::app.employee.claims.title'), route('admin.lost_found.claims.show', $claimId));
});

// Dashboard > Lost & Found Categories
Breadcrumbs::for('admin.lost_found.settings.categories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('lost_found::app.employee.categories.title'), route('admin.lost_found.settings.categories.index'));
});
