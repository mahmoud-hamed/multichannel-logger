<?php

declare(strict_types=1);
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\Support\WebhookChannel;

arch('strict types are declared everywhere')
    ->expect('MahmoudHamed\MultichannelLogger')
    ->toUseStrictTypes();

arch('messengers implement the webhook messenger contract')
    ->expect(['MahmoudHamed\MultichannelLogger\Slack\SlackMessenger'])
    ->toImplement(WebhookMessenger::class);

arch('channels extend the shared webhook channel base')
    ->expect([
        'MahmoudHamed\MultichannelLogger\Slack\SlackWebhookChannel',
        'MahmoudHamed\MultichannelLogger\Discord\DiscordWebhookChannel',
        'MahmoudHamed\MultichannelLogger\Zoom\ZoomWebhookChannel',
    ])
    ->toExtend(WebhookChannel::class);
