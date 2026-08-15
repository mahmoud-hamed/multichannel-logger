<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Default Webhooks
    |--------------------------------------------------------------------------
    |
    | The default webhook URLs used by the MultichannelLogger facade and the
    | notification channels. Every value can be overridden at the call-site
    | by passing a webhook URL explicitly.
    |
    */
    'webhooks' => [
        'slack' => env('SLACK_WEBHOOK_URL'),
        'discord' => env('DISCORD_WEBHOOK_URL'),
        'zoom' => env('ZOOM_WEBHOOK_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Defaults
    |--------------------------------------------------------------------------
    |
    | Connection defaults applied when sending webhook requests.
    |
    */
    'defaults' => [
        'client' => [
            'timeout' => 15,
            'retry_times' => 2,
            'retry_interval_ms' => 100,
        ],
    ],
];
