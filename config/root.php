<?php

return [

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
