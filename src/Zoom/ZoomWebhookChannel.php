<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Zoom;

use OpenCode\MultichannelLogger\Contracts\WebhookMessenger;
use OpenCode\MultichannelLogger\MultichannelLogger;
use OpenCode\MultichannelLogger\Support\WebhookChannel;

final class ZoomWebhookChannel extends WebhookChannel
{
    public function __construct(private readonly MultichannelLogger $logger) {}

    protected function messageMethod(): string
    {
        return 'toZoom';
    }

    protected function messageClass(): string
    {
        return ZoomMessageData::class;
    }

    protected function routeKey(): string
    {
        return 'zoom';
    }

    protected function makeMessenger(string $webhookUrl): WebhookMessenger
    {
        return $this->logger->zoom($webhookUrl);
    }
}
