<?php

return [
    'enabled' => env('MONITOR_ENABLED', false),
    'tenant' => env('MONITOR_TENANT', '-'),
    'instance' => env('MONITOR_INSTANCE', '-'),
    'topics' => [
        'http-in' => [
            'name' => env('MONITOR_HTTP_IN_TOPIC', 'monitor-http-in'),
            'enabled' => env('MONITOR_HTTP_IN_ENABLED', true),
            'with-body' => env('MONITOR_HTTP_IN_NO_BODY', true),
        ],
        'http-out' => [
            'name' => env('MONITOR_HTTP_OUT_TOPIC', 'monitor-http-out'),
            'enabled' => env('MONITOR_HTTP_OUT_ENABLED', true),
            'with-body' => env('MONITOR_HTTP_OUT_NO_BODY', true),
        ],
        'db-query' => [
            'name' => env('MONITOR_HTTP_DB_QUERY_TOPIC', 'monitor-db-query'),
            'enabled' => env('MONITOR_HTTP_DB_QUERY_ENABLED', true),
        ],
    ]
];