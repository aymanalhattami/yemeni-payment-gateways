<?php

return [
    'floosak' => [
        'base_url' => env('FLOOSAK_BASE_URL'),
        'phone' => env('FLOOSAK_PHONE'),
        'short_code' => env('FLOOSAK_SHORT_CODE'),
        'wallet_id' => env('FLOOSAK_WALLET_ID'),
        'key' => env('FLOOSAK_KEY'),
        'verify_request_id' => env('FLOOSAK_VERIFY_REQUEST_ID')
    ],

    'jawali' => [
        'base_url' => env('JAWALI_BASE_URL'),
        'login_token' => env('JAWALI_LOGIN_TOKEN'),
        'access_token' => env('JAWALI_ACCESS_TOKEN'),
        'org_id' => env('JAWALI_ORG_ID'),
        'username' => env('JAWALI_USERNAME'),
        'password' => env('JAWALI_PASSWORD'),
        'agent_wallet' => env('JAWALI_AGENT_WALLET'),
        'agent_wallet_pwd' => env('JAWALI_AGENT_WALLET_PWD')
    ],
];