<?php

declare(strict_types=1);

use MahmoudHamed\MultichannelLogger\Discord\DiscordFormatter;
use MahmoudHamed\MultichannelLogger\Discord\DiscordMessageData;
use Monolog\Level;
use Monolog\LogRecord;

it('formats a log record into a discord message with an embed', function () {
    $record = new LogRecord(
        datetime: new DateTimeImmutable('2026-08-15 12:00:00'),
        channel: 'stack',
        level: Level::Error,
        message: 'Payment failed',
        context: ['order_id' => 42],
        extra: [],
    );

    $message = (new DiscordFormatter)->format($record);

    expect($message)->toBeInstanceOf(DiscordMessageData::class)
        ->and($message->content)->toBe('[2026-08-15 12:00:00] stack.ERROR: Payment failed')
        ->and($message->username)->toBe('Laravel')
        ->and($message->embeds)->not->toBeNull()
        ->and($message->embeds->first()->title)->toBe('ERROR')
        ->and($message->embeds->first()->color)->toBe(0xE01E5A)
        ->and($message->embeds->first()->fields)->toContain([
            'name' => 'order_id',
            'value' => '42',
            'inline' => true,
        ]);
});
