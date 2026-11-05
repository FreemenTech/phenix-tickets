<?php

return [
    'secret_key' => env('FEDAPAY_SECRET_KEY'),
    'environment' => env('FEDAPAY_ENV', 'sandbox'),
    'webhook_secret' => env('FEDAPAY_WEBHOOK_SECRET'),
];
