<?php

use Cone\Root\Http\Middleware\Authenticate;
use Cone\Root\Http\Middleware\HandleRootRequests;

return [

    /*
    |--------------------------------------------------------------------------
    | Root Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Root will be accessible from. If this
    | setting is null, Root will reside under the same domain as the
    | application.
    |
    */

    'domain' => env('ROOT_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Root Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Root will be accessible from. Feel free
    | to change this path to anything you like.
    |
    */

    'path' => env('ROOT_PATH', 'root'),

    /*
    |--------------------------------------------------------------------------
    | Root Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Root route, giving you
    | the chance to add your own middleware to this list or change any
    | of the existing middleware.
    |
    */

    'middleware' => [
        'web',
        Authenticate::class,
        'verified',
        'can:viewRoot',
        HandleRootRequests::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | You can specify the logo and the base colors. If you wish to customize
    | the layout deeper, you might publish the assets and modify anything
    | you need to.
    |
    */

    'branding' => [
        'logo' => '/vendor/root/root-logo-dark.svg',
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

    /*
    |--------------------------------------------------------------------------
    | TipTap Editor Settings
    |--------------------------------------------------------------------------
    |
    | You can specify the default TipTap editor config here. For more info visit
    | the official documentation: https://tiptap.dev/introduction
    |
    */

    'editor' => [
        'link' => [
            'openOnClick' => false,
        ],
        'highlight' => [],
        'strike' => [],
        'image' => [],
        'textAlign' => [
            'types' => ['heading', 'paragraph', 'image'],
        ],
    ],

];
