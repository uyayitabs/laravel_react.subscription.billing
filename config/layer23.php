<?php

return [
    'url' => env('L23_URL', 'https://eosdev.layer23.nl/endpoints/'),
    'auth' => env('L23_AUTH', 'DwHh3QPSWJcp6KtVpMsp9MuSwFs5eZ7PGtUwycyQCRf32gKNmGqhGDyt5TrT2WYr'),
    'productId' => env('L23_PID', 3),
    'profilesOrder' => env('L23ProfilesO', [
        [
            "EOSProfile" => "XS-BBR-1000-12",
            "Type" => 1
        ],
        [
            "EOSProfile" => "BBR-FTTH-L1-TOESLAG",
            "Type" => 1
        ]
    ]),
    'profilesMigration' => env('L23ProfilesM', [
        "eosprofile" => "XS-BBR-1000-12"
    ]),
    'resellerID' => env('L23_RESELLERID', 1)
];
