<?php

return [
    // Personal Access Token with only domains.domain:read permission.
    // NEVER commit this token; set GODADDY_PAT in your local .env.
    'pat' => env('GODADDY_PAT', ''),
    'tlds' => ['com', 'net'],
    'cache_seconds' => 60,
    'ca_bundle' => env('GODADDY_CA_BUNDLE', ''),
];
