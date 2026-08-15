<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Zoom;

use Monolog\LogRecord;
use OpenCode\MultichannelLogger\Contracts\LogMessageFormatter;

final class ZoomFormatter implements LogMessageFormatter
{
    public function __construct(private readonly string $to) {}

    public function format(LogRecord $record): ZoomMessageData
    {
        return new ZoomMessageData(
            to: $this->to,
            message: sprintf(
                '[%s] %s.%s: %s',
                $record->datetime->format('Y-m-d H:i:s'),
                $record->channel,
                $record->level->getName(),
                $record->message
            )
        );
    }
}
