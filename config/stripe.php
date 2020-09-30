<?php
return [
    'publishable_key' => env('STRIPE_PUBLISHABLE_ID', ''),
    'secret'          => env('STRIPE_SECRET', ''),
    'settings'        => array(
        'http.ConnectionTimeOut' => 5,
        'set.timeout'            => 10,
        'log.LogEnabled'         => true,
        'log.FileName'           => storage_path() . '/logs/paypal.log',
        'log.LogLevel'           => 'ERROR'
    ),
];