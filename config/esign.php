<?php
return [
    'zoho' => [
        'key' => env('ZOHO_KEY')
    ],
    'hellosign' => [
        'key' => env('HELLOSIGN_KEY'),
        'client_id' => env('HELLOSIGN_CLIENT_ID')
    ],
    'eversign' => [
        'key' => env('EVERSIGN_KEY'),
        'client_id' => env('EVERSIGN_CLIENT_ID'),
        'secret' => env('EVERSIGN_KEY_SECRET'),
        'business_id' => env('EVERSIGN_BUSINESS_ID')
    ]
];

?>
