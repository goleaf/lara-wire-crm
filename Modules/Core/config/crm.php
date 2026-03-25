<?php

return [
    'app_name' => env('CRM_APP_NAME', env('APP_NAME', 'Lara Wire CRM')),
    'default_currency' => [
        'code' => env('CRM_CURRENCY_CODE', 'USD'),
        'symbol' => env('CRM_CURRENCY_SYMBOL', '$'),
    ],
    'timezone' => env('CRM_TIMEZONE', env('APP_TIMEZONE', 'UTC')),
    'pagination_size' => (int) env('CRM_PAGINATION_SIZE', 15),
];
