<?php

return [
    'app_name' => env('CRM_APP_NAME', env('APP_NAME', 'Lara Wire CRM')),
    'default_currency' => [
        'code' => env('CRM_CURRENCY_CODE', 'USD'),
        'symbol' => env('CRM_CURRENCY_SYMBOL', '$'),
    ],
    'default_currency_code' => env('CRM_CURRENCY_CODE', 'USD'),
    'timezone' => env('CRM_TIMEZONE', env('APP_TIMEZONE', 'UTC')),
    'date_format' => env('CRM_DATE_FORMAT', 'Y-m-d'),
    'pagination_size' => (int) env('CRM_PAGINATION_SIZE', 15),
    'company' => [
        'name' => env('CRM_COMPANY_NAME', env('CRM_APP_NAME', env('APP_NAME', 'Lara Wire CRM'))),
        'address' => env('CRM_COMPANY_ADDRESS', ''),
        'phone' => env('CRM_COMPANY_PHONE', ''),
        'email' => env('CRM_COMPANY_EMAIL', ''),
        'vat' => env('CRM_COMPANY_VAT', ''),
        'vat_number' => env('CRM_COMPANY_VAT', ''),
        'logo' => env('CRM_COMPANY_LOGO', ''),
        'bank_details' => [
            'account_name' => env('CRM_BANK_ACCOUNT_NAME', ''),
            'iban' => env('CRM_BANK_IBAN', ''),
            'swift' => env('CRM_BANK_SWIFT', ''),
        ],
    ],
    'demo_login_password' => env('CRM_DEMO_LOGIN_PASSWORD', 'password123'),
    'demo_login_users' => [
        [
            'full_name' => 'Demo User 1',
            'email' => 'user1@example.com',
        ],
        [
            'full_name' => 'Demo User 2',
            'email' => 'user2@example.com',
        ],
        [
            'full_name' => 'Demo User 3',
            'email' => 'user3@example.com',
        ],
        [
            'full_name' => 'Demo User 4',
            'email' => 'user4@example.com',
        ],
        [
            'full_name' => 'Demo User 5',
            'email' => 'user5@example.com',
        ],
    ],
];
