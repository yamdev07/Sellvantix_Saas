<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | FedaPay Configuration (TEST/SANDBOX - sans webhook)
    |--------------------------------------------------------------------------
    */
    'fedapay' => [
        'api_key'    => env('FEDAPAY_API_KEY'),
        'public_key' => env('FEDAPAY_PUBLIC_KEY'),
        'secret_key' => env('FEDAPAY_SECRET_KEY'),

        // 'sandbox' or 'live'
        'mode' => env('FEDAPAY_MODE', 'sandbox'),

        'return_url' => env('FEDAPAY_RETURN_URL', env('APP_URL') . '/payment/callback'),

        // URLs switch automatiquement selon le mode
        'base_url'    => env('FEDAPAY_MODE', 'sandbox') === 'live'
            ? 'https://api.fedapay.com'
            : 'https://sandbox-api.fedapay.com',

        'payment_url' => env('FEDAPAY_MODE', 'sandbox') === 'live'
            ? 'https://payment.fedapay.com/pay/'
            : 'https://sandbox-payment.fedapay.com/pay/',

        'timeout' => env('FEDAPAY_TIMEOUT', 30),

        // Debug OFF en live pour ne pas exposer les détails d'erreur
        'debug' => env('FEDAPAY_DEBUG', env('FEDAPAY_MODE', 'sandbox') !== 'live'),
        
        // Devises supportées en test
        'currencies' => ['XOF', 'XAF', 'CDF', 'GNF'],
        
        // Méthodes de paiement disponibles en test
        'payment_methods' => [
            'card' => 'Carte bancaire',
            'mtn' => 'MTN Mobile Money',
            'moov' => 'Moov Money',
            'wave' => 'Wave',
        ],
        
        // Numéros de test (documentation FedaPay)
        'test_phones' => [
            'mtn' => '01010101',
            'moov' => '01010102',
            'wave' => '01010103',
        ],
        
        // Cartes de test
        'test_cards' => [
            'visa' => '4111111111111111',
            'mastercard' => '5555555555554444',
        ],
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'model'   => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'base_url' => 'https://api.groq.com/openai/v1',
    ],

];