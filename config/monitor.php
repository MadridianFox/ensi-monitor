<?php

return [
    'enabled' => env('MONITOR_ENABLED', false),
    'topics' => [
        'http-in' => env('MONITOR_TOPIC_HTTP_IN', 'http-in'),
        'http-out' => env('MONITOR_TOPIC_HTTP_IN', 'http-out'),
        'db-queries' => env('MONITOR_TOPIC_HTTP_IN', 'db-queries'),
    ]
];