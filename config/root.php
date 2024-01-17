<?php

use Cone\Root\Http\Middleware\Authenticate;

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
        'chunk_size' => 2 * 1024 * 1024,
        'tmp_dir' => storage_path('app/root-tmp'),
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

    /*
    |--------------------------------------------------------------------------
    | Widget Chart Settings
    |--------------------------------------------------------------------------
    |
    | You can specify the default ApexCharts config for each chart type here.
    | For more info visit the official documentation: https://apexcharts.com
    |
    */

    'widgets' => [
        'trend' => [
            'chart' => [
                'type' => 'area',
                'height' => 80,
                'width' => '100%',
                'sparkline' => ['enabled' => true],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 2,
            ],
            'fill' => [
                'type' => 'gradient',
                'opacity' => 0.75,
                'gradient' => [
                    'shadeIntensity' => 1,
                    'opacityFrom' => 0.75,
                    'opacityTo' => 0,
                    'stops' => [0, 100],
                ],
            ],
            'xaxis' => ['type' => 'datetime'],
            'yaxis' => ['min' => 0],
            'colors' => ['var(--root-base-color-heading)'],
            'tooltip' => [
                'enabled' => true,
                'marker' => ['show' => false],
            ],
            'grid' => [
                'padding' => ['top' => 10, 'bottom' => 10],
            ],
        ],
    ],

];
