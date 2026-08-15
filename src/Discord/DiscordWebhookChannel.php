<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Discord;

use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\MultichannelLogger;
use MahmoudHamed\MultichannelLogger\Support\WebhookChannel;

final class DiscordWebhookChannel extends WebhookChannel
{
    public function __construct(private readonly MultichannelLogger $logger) {}

    protected function messageMethod(): string
    {
        return 'toDiscord';
    }

    protected function messageClass(): string
    {
        return DiscordMessageData::class;
    }

    protected function routeKey(): string
    {
        return 'discord';
    }

    protected function makeMessenger(string $webhookUrl): WebhookMessenger
    {
        return $this->logger->discord($webhookUrl);
    }
}
