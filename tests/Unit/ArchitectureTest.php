<?php

declare(strict_types=1);
use OpenCode\MultichannelLogger\Contracts\WebhookMessenger;
use OpenCode\MultichannelLogger\Support\WebhookChannel;

arch('strict types are declared everywhere')
    ->expect('OpenCode\MultichannelLogger')
    ->toUseStrictTypes();

arch('messengers implement the webhook messenger contract')
    ->expect(['OpenCode\MultichannelLogger\Slack\SlackMessenger'])
    ->toImplement(WebhookMessenger::class);

arch('channels extend the shared webhook channel base')
    ->expect([
        'OpenCode\MultichannelLogger\Slack\SlackWebhookChannel',
        'OpenCode\MultichannelLogger\Discord\DiscordWebhookChannel',
        'OpenCode\MultichannelLogger\Zoom\ZoomWebhookChannel',
    ])
    ->toExtend(WebhookChannel::class);
