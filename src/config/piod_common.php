<?php
return [
    'resource_list' => [
        'db:pgsql',
        'db:mysql',
        'db:pio',
        'db:piod',
        'redis',
        'rabbitmq',
        'sentry'
    ],
    'rabbitmq' => [
        'host' => env("RABBITMQ_HOST"),
        'port' => env("RABBITMQ_PORT"),
        'user' => env("RABBITMQ_USER"),
        'password' => env("RABBITMQ_PASSWORD"),
        'vhost' => env("RABBITMQ_VHOST"),
    ],
    //optional: if using sending notification option
    'notifier' => [
        'notifier_publish_persistent' => env('NOTIFIER_PUBLISH_PERSISTENT',false),
        'critical_notifier_exchange_name' => env('CRITICAL_NOTIFIER_EXCHANGE_NAME','notifier_critical'),
        'error_notifier_exchange_name' => env('ERROR_NOTIFIER_EXCHANGE_NAME','notifier_error'),
        'warning_notifier_exchange_name' => env('WARNING_NOTIFIER_EXCHANGE_NAME','notifier_warning'),
        'info_notifier_exchange_name' => env('INFO_NOTIFIER_EXCHANGE_NAME','notifier_info'),
    ],
    //optional: if using event sync CLI
    'events' => [
        'source_name' => env('EVENT_SOURCE_NAME'),
        'persistent' => env('EVENT_PERSISTENT',true),
        'set_exchange_name' => env('EVENT_SET_EXCHANGE_NAME'),
        'get_queue_name' => env('EVENT_GET_QUEUE_NAME'),
        'get_events_list' => ['create_user'],
    ],
    'process' => [
        'enable' => env('PROCESS_ENABLE',1),
        'queue_name' => env('PROCESS_QUEUE_NAME','data_piod'),
    ],

];