<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Slack;

use MahmoudHamed\MultichannelLogger\Exceptions\MissingWebhookException;
use MahmoudHamed\MultichannelLogger\Logging\WebhookLoggerFactory;
use MahmoudHamed\MultichannelLogger\Logging\WebhookLogHandler;
use Monolog\Logger;

final class CreateSlackLogger extends WebhookLoggerFactory
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __invoke(array $config): Logger
    {
        $webhookUrl = $this->stringValue($config, 'webhook_url');

        if ($webhookUrl === null || $webhookUrl === '') {
            throw MissingWebhookException::for('slack');
        }

        $messenger = new SlackMessenger(
            webhookUrl: $webhookUrl,
            timeout: $this->intValue($config, 'timeout', 15),
            retryTimes: $this->intValue($config, 'retry_times', 2),
            retryInterval: $this->intValue($config, 'retry_interval_ms', 100),
        );

        $handler = new WebhookLogHandler(
            messenger: $messenger,
            messageFormatter: new SlackFormatter(
                username: $this->stringValue($config, 'username') ?? 'Laravel',
                emoji: $this->stringValue($config, 'emoji') ?? ':boom:',
                channel: $this->stringValue($config, 'channel'),
            ),
            level: $this->level($config),
            ignoreExceptions: $this->boolValue($config, 'ignore_exceptions', true),
        );

        return new Logger('slack', [$handler]);
    }
}
