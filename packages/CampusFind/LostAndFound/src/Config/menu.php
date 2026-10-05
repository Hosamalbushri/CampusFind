<?php

return [
    [
        'key' => 'lost_found',
        'name' => 'lost_found::app.acl.management',
        'route' => 'admin.lost_found.items.index',
        'sort' => 6,
        'icon-class' => 'icon-search',
    ],
    [
        'key' => 'lost_found.items',
        'name' => 'lost_found::app.employee.items.title',
        'route' => 'admin.lost_found.items.index',
        'sort' => 1,
        'icon-class' => '',
    ],
    [
        'key' => 'lost_found.reports',
        'name' => 'lost_found::app.employee.reports.title',
        'route' => 'admin.lost_found.reports.index',
        'sort' => 2,
        'icon-class' => '',
    ],
//    [
//        'key' => 'lost_found.responses',
//        'name' => 'lost_found::app.employee.responses.title',
//        'route' => 'admin.lost_found.responses.index',
//        'sort' => 3,
//        'icon-class' => '',
//    ],
//    [
//        'key' => 'lost_found.matches',
//        'name' => 'lost_found::app.employee.matches.title',
//        'route' => 'admin.lost_found.matches.index',
//        'sort' => 4,
//        'icon-class' => '',
//    ],
    [
        'key' => 'lost_found.claims',
        'name' => 'lost_found::app.employee.claims.title',
        'route' => 'admin.lost_found.claims.index',
        'sort' => 5,
        'icon-class' => '',
    ],
    [
        'key' => 'lost_found.categories',
        'name' => 'lost_found::app.employee.categories.title',
        'route' => 'admin.lost_found.settings.categories.index',
        'sort' => 6,
        'icon-class' => '',
    ],
];
