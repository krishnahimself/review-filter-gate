<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Review Alert Recipient
    |--------------------------------------------------------------------------
    |
    | Detractor reviews (scores 1-3) are emailed to this address. Promoter
    | reviews (scores 4-5) are redirected to the Google review page instead.
    |
    */

    'admin_email' => env('REVIEW_ADMIN_EMAIL', 'hello@example.com'),

    'google_review_url' => env('REVIEW_GOOGLE_URL', 'https://g.page/r/CbsVQiyssFlMEAE/review'),

];
