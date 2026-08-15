<?php

declare(strict_types=1);

use MahmoudHamed\MultichannelLogger\Zoom\ZoomMessageData;

it('builds a zoom chat payload', function () {
    $message = new ZoomMessageData(
        to: 'channel-123',
        message: 'Deploy finished',
        id: 'fixed-id',
    );

    expect($message->toPayload())->toBe([
        'head' => [
            'type' => 'SEND_MESSAGE',
            'id' => 'fixed-id',
        ],
        'body' => [
            'to' => 'channel-123',
            'message' => [
                'message' => 'Deploy finished',
            ],
        ],
    ]);
});

it('generates an id when none is provided', function () {
    $message = new ZoomMessageData(to: 'channel-123', message: 'Hello');

    $payload = $message->toPayload();

    expect($payload['head']['id'])->toBeString()
        ->not->toBeEmpty();
});
