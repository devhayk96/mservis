<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Test mode
    |--------------------------------------------------------------------------
    |
    | In test mode all requests are sent wrapped around <check-payment> XML
    | tag. So no payments are really created. Everything else works as usual:
    | logging, jobs queuing, etc.
    |
    */
    'test_mode' => env('POMPAY_TEST_MODE', 'true'),

    /*
    |--------------------------------------------------------------------------
    | Test mode
    |--------------------------------------------------------------------------
    |
    | In test mode all requests are sent wrapped around <check-payment> XML
    | tag. So no payments are really created. Everything else works as usual:
    | logging, jobs queuing, etc.
    |
    */
    'service' => env('POMPAY_SERVICE', '24'),

    /*
    |--------------------------------------------------------------------------
    | Delays between failed jobs
    |--------------------------------------------------------------------------
    |
    | If status check requests fails, we will send another request later.
    | This option allows us to configure amount of those subsequent requests
    | and delays before them.
    | This is a string of integers. Amount of integers represents amount
    | of subsequent requests. Integers itself represent delays in minutes.
    |
    */
    'failed_jobs_delays' => env('POMPAY_FAILED_JOBS_DELAYS', '1 15 30 60 360'),

    /*
    |--------------------------------------------------------------------------
    | Delays between status check requests
    |--------------------------------------------------------------------------
    |
    | If status check request shows us that the payment (transaction) is still
    | This option allows us to configure amount of those subsequent requests
    | and delays before them.
    | This is a string of integers. Amount of integers represents amount
    | of subsequent requests. Integers itself represent delays in minutes.
    |
    */
    'check_request_delays' => env('POMPAY_CHECK_REQUEST_DELAYS', '10 30 60 180 1440'),

    /*
    |--------------------------------------------------------------------------
    | Openssl private key
    |--------------------------------------------------------------------------
    */
    'private_key' => env('POMPAY_PRIVATE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Openssl private key password
    |--------------------------------------------------------------------------
    */
    'private_key_password' => env('POMPAY_PRIVATE_KEY_PASSWORD', ''),
];
