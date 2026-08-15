<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Slack;

use Monolog\Logger;
use OpenCode\MultichannelLogger\Exceptions\MissingWebhookException;
use OpenCode\MultichannelLogger\Logging\WebhookLoggerFactory;
use OpenCode\MultichannelLogger\Logging\WebhookLogHandler;

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
