<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Zoom;

use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\MultichannelLogger;
use MahmoudHamed\MultichannelLogger\Support\WebhookChannel;

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
