<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger;

use Illuminate\Contracts\Config\Repository;
use MahmoudHamed\MultichannelLogger\Discord\DiscordMessenger;
use MahmoudHamed\MultichannelLogger\Slack\SlackMessenger;
use MahmoudHamed\MultichannelLogger\Zoom\ZoomMessenger;

final class MultichannelLogger
{
    public function __construct(private readonly Repository $config) {}

    public function slack(?string $webhookUrl = null): SlackMessenger
    {
        return new SlackMessenger(
            webhookUrl: $webhookUrl ?? (string) $this->config->get('multichannel-logger.webhooks.slack', ''),
            timeout: $this->clientOption('timeout'),
            retryTimes: $this->clientOption('retry_times'),
            retryInterval: $this->clientOption('retry_interval_ms'),
        );
    }

    public function discord(?string $webhookUrl = null): DiscordMessenger
    {
        return new DiscordMessenger(
            webhookUrl: $webhookUrl ?? (string) $this->config->get('multichannel-logger.webhooks.discord', ''),
            timeout: $this->clientOption('timeout'),
            retryTimes: $this->clientOption('retry_times'),
            retryInterval: $this->clientOption('retry_interval_ms'),
        );
    }

    public function zoom(?string $webhookUrl = null): ZoomMessenger
    {
        return new ZoomMessenger(
            webhookUrl: $webhookUrl ?? (string) $this->config->get('multichannel-logger.webhooks.zoom', ''),
            timeout: $this->clientOption('timeout'),
            retryTimes: $this->clientOption('retry_times'),
            retryInterval: $this->clientOption('retry_interval_ms'),
        );
    }

    private function clientOption(string $key): int
    {
        return (int) $this->config->get("multichannel-logger.defaults.client.{$key}", 0);
    }
}
