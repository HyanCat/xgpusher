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
        'ios_key'        => env('XGPUSH_IOS_ACCESS_ID'),
        'ios_secret'     => env('XGPUSH_IOS_SECRET_KEY'),
        'android_key'    => env('XGPUSH_ANDROID_ACCESS_ID'),
        'android_secret' => env('XGPUSH_ANDROID_SECRET_KEY'),
        'environment'    => env('XGPUSH_ENVIRONMENT', env('APP_ENV')),
        'custom_key'     => 'custom',
        'account_prefix' => 'user',
    ],

];
