<?php

use Cone\Root\Http\Middleware\Authenticate;
use Cone\Root\Http\Middleware\HandleRootRequests;

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | You can specify the middleware for the Root routes.
    |
    */

    'middleware' => [
        'web',
        Authenticate::class,
        'verified:root.verification.show',
        'can:viewRoot',
        HandleRootRequests::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Settings
    |--------------------------------------------------------------------------
    |
    | You can specify the media settings here. Set the default disk to store
    | the media items. Also, you can specify the expiration of the chunks.
    |
    | Supported conversion drivers: "gd"
    |
    */

    'media' => [
        'disk' => 'public',
        'chunk_expiration' => 1440,
        'conversion' => [
            'default' => 'gd',
            'drivers' => [
                'gd' => [
                    'quality' => 70,
                ],
            ],
        ],
    ],

];
