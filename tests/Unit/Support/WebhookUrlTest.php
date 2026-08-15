<?php

declare(strict_types=1);

use OpenCode\MultichannelLogger\Exceptions\InvalidWebhookException;
use OpenCode\MultichannelLogger\Exceptions\MissingWebhookException;
use OpenCode\MultichannelLogger\Support\WebhookUrl;

it('parses a webhook url into base and endpoint parts', function () {
    $url = WebhookUrl::from('https://hooks.slack.com/services/T00000/B00000/XXXXX');

    expect($url)
        ->baseUrl()->toBe('https://hooks.slack.com')
        ->endpoint()->toBe('/services/T00000/B00000/XXXXX')
        ->full()->toBe('https://hooks.slack.com/services/T00000/B00000/XXXXX');
});

it('preserves the query string of a webhook url', function () {
    $url = WebhookUrl::from('https://webhooks.zoom.us/api/chat/v1/message?token=abc123');

    expect($url)
        ->baseUrl()->toBe('https://webhooks.zoom.us')
        ->endpoint()->toBe('/api/chat/v1/message?token=abc123');
});

it('throws when the webhook url is empty', function () {
    WebhookUrl::from('');
})->throws(MissingWebhookException::class);

it('throws when the webhook url is malformed', function () {
    WebhookUrl::from('not-a-valid-url');
})->throws(InvalidWebhookException::class);
