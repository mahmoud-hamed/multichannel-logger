<?php

declare(strict_types=1);

use MahmoudHamed\MultichannelLogger\Slack\SlackFormatter;
use MahmoudHamed\MultichannelLogger\Slack\SlackMessageData;
use Monolog\Level;
use Monolog\LogRecord;

function logRecord(string $message = 'Something broke', Level $level = Level::Error, array $context = []): LogRecord
{
    return new LogRecord(
        datetime: new DateTimeImmutable('2026-08-15 12:00:00'),
        channel: 'stack',
        level: $level,
        message: $message,
        context: $context,
        extra: [],
    );
}

it('formats a log record into a slack message', function () {
    $message = (new SlackFormatter)->format(logRecord(context: ['user_id' => 5]));

    expect($message)->toBeInstanceOf(SlackMessageData::class)
        ->and($message->text)->toBe('[2026-08-15 12:00:00] stack.ERROR: Something broke')
        ->and($message->username)->toBe('Laravel')
        ->and($message->iconEmoji)->toBe(':boom:')
        ->and($message->attachments)->not->toBeNull()
        ->and($message->attachments->first()->color)->toBe('danger')
        ->and($message->attachments->first()->fields)->toBe([
            ['title' => 'user_id', 'value' => '5', 'short' => true],
        ]);
});

it('uses a warning colour for warnings', function () {
    $message = (new SlackFormatter)->format(logRecord(level: Level::Warning));

    expect($message->attachments->first()->color)->toBe('#ffc300');
});

it('appends the exception trace as attachment text', function () {
    $exception = new RuntimeException('Boom');

    $message = (new SlackFormatter)->format(logRecord(context: ['exception' => $exception]));

    expect($message->attachments->first()->text)->toContain('Boom');
});
