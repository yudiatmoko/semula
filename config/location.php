<?php

return [
    'login_google' => [
        'target_lat' => (float) env('LOGIN_GOOGLE_TARGET_LAT', -6.2531466),
        'target_lng' => (float) env('LOGIN_GOOGLE_TARGET_LNG', 107.1710803),
        'max_radius_meters' => (int) env('LOGIN_GOOGLE_MAX_RADIUS_METERS', 10),
    ],
];
