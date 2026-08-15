<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Zoom;

use Monolog\Logger;
use OpenCode\MultichannelLogger\Exceptions\MissingConfigurationException;
use OpenCode\MultichannelLogger\Exceptions\MissingWebhookException;
use OpenCode\MultichannelLogger\Logging\WebhookLoggerFactory;
use OpenCode\MultichannelLogger\Logging\WebhookLogHandler;

final class CreateZoomLogger extends WebhookLoggerFactory
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __invoke(array $config): Logger
    {
        $webhookUrl = $this->stringValue($config, 'webhook_url');
        $to = $this->stringValue($config, 'to');

        if ($webhookUrl === null || $webhookUrl === '') {
            throw MissingWebhookException::for('zoom');
        }

        if ($to === null || $to === '') {
            throw MissingConfigurationException::for('zoom', 'to');
        }

        $messenger = new ZoomMessenger(
            webhookUrl: $webhookUrl,
            timeout: $this->intValue($config, 'timeout', 15),
            retryTimes: $this->intValue($config, 'retry_times', 2),
            retryInterval: $this->intValue($config, 'retry_interval_ms', 100),
        );

        $handler = new WebhookLogHandler(
            messenger: $messenger,
            messageFormatter: new ZoomFormatter(to: $to),
            level: $this->level($config),
            ignoreExceptions: $this->boolValue($config, 'ignore_exceptions', true),
        );

        return new Logger('zoom', [$handler]);
    }
}
