<?php

return [
    [
        'key'        => 'lost_found',
        'name'       => 'lost_found::app.acl.management',
        'route'      => 'admin.lost_found.items.index',
        'sort'       => 6,
        'icon-class' => 'icon-search',
    ],
    [
        'key'        => 'lost_found.items',
        'name'       => 'lost_found::app.employee.items.title',
        'route'      => 'admin.lost_found.items.index',
        'sort'       => 1,
        'icon-class' => '',
    ],
    [
        'key'        => 'lost_found.claims',
        'name'       => 'lost_found::app.employee.claims.title',
        'route'      => 'admin.lost_found.claims.index',
        'sort'       => 2,
        'icon-class' => '',
    ],
    [
        'key'        => 'lost_found.categories',
        'name'       => 'lost_found::app.employee.categories.title',
        'route'      => 'admin.lost_found.settings.categories.index',
        'sort'       => 3,
        'icon-class' => '',
    ],
];
