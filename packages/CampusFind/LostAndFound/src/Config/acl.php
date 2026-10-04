<?php

return [
    [
        'key' => 'lost_found',
        'name' => 'lost_found::app.acl.management',
        'route' => 'admin.lost_found.index',
        'sort' => 6,
    ], [
        'key' => 'lost_found.items',
        'name' => 'lost_found::app.acl.items',
        'route' => 'admin.lost_found.items.index',
        'sort' => 1,
    ], [
        'key' => 'lost_found.items.view',
        'name' => 'lost_found::app.acl.items_view',
        'route' => ['admin.lost_found.items.index', 'admin.lost_found.reports.index'],
        'sort' => 1,
    ], [
        'key' => 'lost_found.items.create',
        'name' => 'lost_found::app.acl.items_create',
        'route' => ['admin.lost_found.items.create', 'admin.lost_found.items.store'],
        'sort' => 2,
    ], [
        'key' => 'lost_found.items.edit',
        'name' => 'lost_found::app.acl.items_edit',
        'route' => [
            'admin.lost_found.items.edit',
            'admin.lost_found.items.update',
            'admin.lost_found.items.approve',
            'admin.lost_found.items.images.store',
            'admin.lost_found.reports.approve',
            'admin.lost_found.reports.reject',
        ],
        'sort' => 3,
    ], [
        'key' => 'lost_found.claims',
        'name' => 'lost_found::app.acl.claims',
        'route' => 'admin.lost_found.claims.index',
        'sort' => 2,
    ], [
        'key' => 'lost_found.claims.view',
        'name' => 'lost_found::app.acl.claims_view',
        'route' => ['admin.lost_found.claims.index', 'admin.lost_found.items.claims.index', 'admin.lost_found.claims.show', 'admin.lost_found.claims.evidence.file'],
        'sort' => 1,
    ], [
        'key' => 'lost_found.claims.review',
        'name' => 'lost_found::app.acl.claims_review',
        'route' => ['admin.lost_found.claims.review', 'admin.lost_found.claims.update_review'],
        'sort' => 2,
    ], [
        'key' => 'lost_found.claims.approve',
        'name' => 'lost_found::app.acl.claims_approve',
        'route' => ['admin.lost_found.claims.approve', 'admin.lost_found.claims.revoke'],
        'sort' => 3,
    ], [
        'key' => 'lost_found.claims.reject',
        'name' => 'lost_found::app.acl.claims_reject',
        'route' => 'admin.lost_found.claims.reject',
        'sort' => 4,
    ], [
        'key' => 'lost_found.responses',
        'name' => 'lost_found::app.acl.responses',
        'route' => 'admin.lost_found.responses.index',
        'sort' => 3,
    ], [
        'key' => 'lost_found.responses.view',
        'name' => 'lost_found::app.acl.responses_view',
        'route' => ['admin.lost_found.responses.index', 'admin.lost_found.responses.show', 'admin.lost_found.responses.images.show'],
        'sort' => 1,
    ], [
        'key' => 'lost_found.responses.review',
        'name' => 'lost_found::app.acl.responses_review',
        'route' => ['admin.lost_found.responses.review', 'admin.lost_found.responses.reject'],
        'sort' => 2,
    ], [
        'key' => 'lost_found.responses.verify',
        'name' => 'lost_found::app.acl.responses_verify',
        'route' => 'admin.lost_found.responses.verify',
        'sort' => 3,
    ], [
        'key' => 'lost_found.matches',
        'name' => 'lost_found::app.acl.matches',
        'route' => 'admin.lost_found.matches.index',
        'sort' => 4,
    ], [
        'key' => 'lost_found.matches.view',
        'name' => 'lost_found::app.acl.matches_view',
        'route' => ['admin.lost_found.matches.index', 'admin.lost_found.matches.verify'],
        'sort' => 1,
    ], [
        'key' => 'lost_found.matches.generate',
        'name' => 'lost_found::app.acl.matches_generate',
        'route' => ['admin.lost_found.matches.generate_report', 'admin.lost_found.matches.generate_item'],
        'sort' => 2,
    ], [
        'key' => 'lost_found.matches.review',
        'name' => 'lost_found::app.acl.matches_review',
        'route' => 'admin.lost_found.matches.review',
        'sort' => 3,
    ], [
        'key' => 'lost_found.custody',
        'name' => 'lost_found::app.acl.custody',
        'route' => 'admin.lost_found.custody.index',
        'sort' => 5,
    ], [
        'key' => 'lost_found.custody.manage',
        'name' => 'lost_found::app.acl.custody_manage',
        'route' => ['admin.lost_found.custody.receive', 'admin.lost_found.custody.transfer', 'admin.lost_found.custody.move_storage'],
        'sort' => 1,
    ], [
        'key' => 'lost_found.handover',
        'name' => 'lost_found::app.acl.handover',
        'route' => 'admin.lost_found.handover.index',
        'sort' => 6,
    ], [
        'key' => 'lost_found.handover.complete',
        'name' => 'lost_found::app.acl.handover_complete',
        'route' => 'admin.lost_found.handover.complete',
        'sort' => 1,
    ], [
        'key' => 'lost_found.settings',
        'name' => 'lost_found::app.acl.settings',
        'route' => 'admin.lost_found.settings.index',
        'sort' => 7,
    ], [
        'key' => 'lost_found.settings.categories',
        'name' => 'lost_found::app.acl.settings_categories',
        'route' => ['admin.lost_found.settings.categories.index', 'admin.lost_found.settings.categories.store', 'admin.lost_found.settings.categories.edit', 'admin.lost_found.settings.categories.update'],
        'sort' => 1,
    ],
];
