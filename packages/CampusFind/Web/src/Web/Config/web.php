<?php

return [
    /**
     * Web Starter Template
     */
    'template' => 'starter',

    /**
     * Route prefix for Web capability.
     * Set to empty string '' to claim root domain route [/].
     */
    'prefix' => 'campus-find-web',

    /**
     * Middleware stack applied to all Web capability routes.
     */
    'middleware' => ['web'],

    /**
     * Branding configuration.
     * Customize the visual identity and primary brand color.
     */
    'branding' => [
        'name'  => 'CampusFind',
        'color' => '#0E90D9',
        'logo'  => null,
    ],

    /**
     * Web Authentication Configuration.
     */
    'auth' => [
        'enabled'          => true,
        'guard'            => 'student',
        'provider_package' => 'student',
        'routes'           => [
            'login'  => 'campusfind_web.web.login',
            'logout' => 'campusfind_web.web.logout',
        ],
    ],

    /**
     * Navigation links configuration.
     */
    'navigation' => [
        'home' => [
            'name'  => 'campusfind_web_web::app.web.home',
            'route' => 'campusfind_web.web.home',
            'sort'  => 1,
        ],
        'items' => [
            'name'  => 'campusfind_web_web::app.web.browse_items',
            'route' => 'campusfind_web.web.items.index',
            'sort'  => 2,
        ],
        'report_lost' => [
            'name'  => 'campusfind_web_web::app.web.reports.report_lost',
            'route' => 'campusfind_web.web.reports.lost',
            'sort'  => 3,
        ],
        'report_found' => [
            'name'  => 'campusfind_web_web::app.web.reports.report_found',
            'route' => 'campusfind_web.web.reports.found',
            'sort'  => 4,
        ],
        'how_it_works' => [
            'name'   => 'campusfind_web_web::app.web.how_it_works',
            'route'  => 'campusfind_web.web.pages.show',
            'params' => ['page' => 'how-it-works'],
            'sort'   => 5,
        ],
        'about' => [
            'name'   => 'campusfind_web_web::app.web.about',
            'route'  => 'campusfind_web.web.pages.show',
            'params' => ['page' => 'about'],
            'sort'   => 6,
        ],
        'contact' => [
            'name'   => 'campusfind_web_web::app.web.contact',
            'route'  => 'campusfind_web.web.pages.show',
            'params' => ['page' => 'contact'],
            'sort'   => 7,
        ],
    ],
];
