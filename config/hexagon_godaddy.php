<?php

return [
    // Personal Access Token with only domains.domain:read permission.
    // NEVER commit this token; set GODADDY_PAT in your local .env.
    'pat' => env('GODADDY_PAT', ''),
    'tlds' => ['com', 'net'],
    'cache_seconds' => 300,
    'ca_bundle' => env('GODADDY_CA_BUNDLE', ''),
    // auto: GoDaddy khi có PAT, mock khi không; godaddy/mock có thể cấu hình tường minh.
    'availability_source' => env('HEXAGON_DOMAIN_AVAILABILITY_SOURCE', 'auto'),
];
