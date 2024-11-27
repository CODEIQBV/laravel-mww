<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MyOnlineStore API Configuration
    |--------------------------------------------------------------------------
    */

    // Default API key for single-tenant setup
    'api_key' => env('MYONLINESTORE_API_KEY'),

    // Default API URL
    'api_url' => env('MYONLINESTORE_API_URL', 'https://api.myonlinestore.com'),

    // API Version
    'api_version' => env('MYONLINESTORE_API_VERSION', '1'),

    // Enable multi-tenant mode
    'multi_tenant' => env('MYONLINESTORE_MULTI_TENANT', false),

    // Store URL (if required)
    'store_url' => env('MYONLINESTORE_STORE_URL'),

    // Default timeout for API requests in seconds
    'timeout' => env('MYONLINESTORE_TIMEOUT', 30),

    // Default language
    'language' => env('MYONLINESTORE_LANGUAGE', 'nl_NL'),
]; 