<?php
    return [
        /*
        |--------------------------------------------------------------------------
        | Cross origin urls
        |--------------------------------------------------------------------------
        |
        | Allowed cross origin url hosts separated by comma(,)
        | @see :: Don't put slash after url (Correct Example :: "http://localhost", Wrong example :: "http://localhost/")
        */

        'allowedOrigins' => [
            "http://localhost"
        ],

        /*
        |--------------------------------------------------------------------------
        | Public and private keys of the application. generate by hitting the
        | route "/push-notification/keys" and put the respective value here and
        | set the public keys on client side also.
        |--------------------------------------------------------------------------
        |
        */

        'publicKey' => env("PUSH_NOTIFICATION_PUBLIC_KEY"),
        'privateKey'=> env("PUSH_NOTIFICATION_PRIVATE_KEY"),

        /*
        |--------------------------------------------------------------------------
        | Redis credential
        | We are currently supporting only redis to save browser specific details
        |--------------------------------------------------------------------------
        |
       */

        'redis' => [
            'host'          => env('PUSH_REDIS_HOST', env('REDIS_HOST')),
            'port'          => env('PUSH_REDIS_PORT', env('REDIS_PORT')),
            'password'      => env('PUSH_REDIS_PASSWORD', ''),
            'database'      => env('PUSH_REDIS_DATABASE', env('REDIS_DATABASE')),
            'key'           => env('PUSH_REDIS_KEY', 'push-notification')
        ],

        /*
        |-------------------------------------------------------------------------------
        | If you want to send notification to all the user who subscribed, the set true
        | or if you want to delete user after sending notification one time, then false
        |-------------------------------------------------------------------------------
        |
       */

        'preserveUser' => env('REDIS_PRESERVE_USER', false)

    ]
?>