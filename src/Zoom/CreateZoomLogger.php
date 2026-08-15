<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Zoom;

use MahmoudHamed\MultichannelLogger\Exceptions\MissingConfigurationException;
use MahmoudHamed\MultichannelLogger\Exceptions\MissingWebhookException;
use MahmoudHamed\MultichannelLogger\Logging\WebhookLoggerFactory;
use MahmoudHamed\MultichannelLogger\Logging\WebhookLogHandler;
use Monolog\Logger;

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
