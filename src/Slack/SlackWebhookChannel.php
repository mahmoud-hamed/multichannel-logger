<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Slack;

use OpenCode\MultichannelLogger\Contracts\WebhookMessenger;
use OpenCode\MultichannelLogger\MultichannelLogger;
use OpenCode\MultichannelLogger\Support\WebhookChannel;

final class SlackWebhookChannel extends WebhookChannel
{
    public function __construct(private readonly MultichannelLogger $logger) {}

    protected function messageMethod(): string
    {
        return 'toSlack';
    }

    protected function messageClass(): string
    {
        return SlackMessageData::class;
    }

    protected function routeKey(): string
    {
        return 'slack';
    }

    protected function makeMessenger(string $webhookUrl): WebhookMessenger
    {
        return $this->logger->slack($webhookUrl);
    }
}
