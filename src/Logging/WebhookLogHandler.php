<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Logging;

use MahmoudHamed\MultichannelLogger\Contracts\LogMessageFormatter;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\Exceptions\MultichannelLoggerException;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

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
