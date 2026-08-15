<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Contracts;

use Monolog\LogRecord;

interface LogMessageFormatter
{
    public function format(LogRecord $record): WebhookMessage;
}
