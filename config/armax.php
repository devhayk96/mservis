<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dealer ID
    |--------------------------------------------------------------------------
    */
    'dealer' => env('ARMAX_DEALER', ''),

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */
    'login' => env('ARMAX_LOGIN', ''),

    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    */
    'password' => env('ARMAX_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | Terminal ID
    |--------------------------------------------------------------------------
    */
    'terminal' => env('ARMAX_TERMINAL', ''),

    /*
    |--------------------------------------------------------------------------
    | Provider ID
    |--------------------------------------------------------------------------
    */
    'provider' => env('ARMAX_PROVIDER', ''),

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
    'test_mode' => env('ARMAX_TEST_MODE', 'true'),

    /*
    |--------------------------------------------------------------------------
    | Delays between failed jobs
    |--------------------------------------------------------------------------
    |
    | If status check requests fails, we will send another request later.
    | This option allows us to configure amount of those subsequent requests
    | and delays befor them.
    | This is a string of integers. Amount of integers represents amount
    | of subsequent requests. Integers itself represent delays in minutes.
    |
    */
   'failed_jobs_delays' => env('ARMAX_FAILED_JOBS_DELAYS', '1 15 30 60 360'),

    /*
    |--------------------------------------------------------------------------
    | Delays between status check requests
    |--------------------------------------------------------------------------
    |
    | If status check request shows us that the payment (transaction) is still
    | This option allows us to configure amount of those subsequent requests
    | and delays befor them.
    | This is a string of integers. Amount of integers represents amount
    | of subsequent requests. Integers itself represent delays in minutes.
    |
    */
   'check_request_delays' => env('ARMAX_CHECK_REQUEST_DELAYS', '10 30 60 180 1440'),
];
