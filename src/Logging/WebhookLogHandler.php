<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use OpenCode\MultichannelLogger\Contracts\LogMessageFormatter;
use OpenCode\MultichannelLogger\Contracts\WebhookMessenger;
use OpenCode\MultichannelLogger\Exceptions\MultichannelLoggerException;

final class WebhookLogHandler extends AbstractProcessingHandler
{
    public function __construct(
        private readonly WebhookMessenger $messenger,
        private readonly LogMessageFormatter $messageFormatter,
        Level $level = Level::Debug,
        bool $bubble = true,
        private readonly bool $ignoreExceptions = true,
    ) {
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        try {
            $this->messenger->send($this->messageFormatter->format($record));
        } catch (MultichannelLoggerException $exception) {
            if (! $this->ignoreExceptions) {
                throw $exception;
            }
        }
    }
}
