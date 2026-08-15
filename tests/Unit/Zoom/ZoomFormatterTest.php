<?php

declare(strict_types=1);

use MahmoudHamed\MultichannelLogger\Zoom\ZoomFormatter;
use MahmoudHamed\MultichannelLogger\Zoom\ZoomMessageData;
use Monolog\Level;
use Monolog\LogRecord;

it('formats a log record into a zoom message', function () {
    $record = new LogRecord(
        datetime: new DateTimeImmutable('2026-08-15 12:00:00'),
        channel: 'stack',
        level: Level::Error,
        message: 'Backup failed',
        context: [],
        extra: [],
    );

    $message = (new ZoomFormatter(to: 'channel-123'))->format($record);

    expect($message)->toBeInstanceOf(ZoomMessageData::class)
        ->and($message->to)->toBe('channel-123')
        ->and($message->message)->toBe('[2026-08-15 12:00:00] stack.ERROR: Backup failed');
});
