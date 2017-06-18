<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tencent Xinge Push Service
    |--------------------------------------------------------------------------
    |
    | http://xg.qq.com
    |
    */

    'xgpush' => [
        'key' => env('XGPUSH_KEY'),
        'secret' => env('XGPUSH_SECRET'),
        'environment' => env('XGPUSH_ENVIRONMENT', env('APP_ENV')),
        'custom_key' => 'my',
        'account_prefix' => 'user',
    ],

];
