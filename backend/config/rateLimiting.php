<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Here you may configure the rate limiters for your application. These
    | limits are used to prevent abuse of your application's API.
    |
    */

    'limiters' => [
        'api' => [
            'decay_minutes' => 1,
            'max_attempts' => 120, // Increased from default 60
        ],
    ],

];
