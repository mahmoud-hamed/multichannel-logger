<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Discord;

use MahmoudHamed\MultichannelLogger\Exceptions\MissingWebhookException;
use MahmoudHamed\MultichannelLogger\Logging\WebhookLoggerFactory;
use MahmoudHamed\MultichannelLogger\Logging\WebhookLogHandler;
use Monolog\Logger;

final class CreateDiscordLogger extends WebhookLoggerFactory
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __invoke(array $config): Logger
    {
        $webhookUrl = $this->stringValue($config, 'webhook_url');

        if ($webhookUrl === null || $webhookUrl === '') {
            throw MissingWebhookException::for('discord');
        }

        $messenger = new DiscordMessenger(
            webhookUrl: $webhookUrl,
            timeout: $this->intValue($config, 'timeout', 15),
            retryTimes: $this->intValue($config, 'retry_times', 2),
            retryInterval: $this->intValue($config, 'retry_interval_ms', 100),
        );

        $handler = new WebhookLogHandler(
            messenger: $messenger,
            messageFormatter: new DiscordFormatter(
                username: $this->stringValue($config, 'username') ?? 'Laravel',
                avatarUrl: $this->stringValue($config, 'avatar_url'),
            ),
            level: $this->level($config),
            ignoreExceptions: $this->boolValue($config, 'ignore_exceptions', true),
        );

        return new Logger('discord', [$handler]);
    }
}
