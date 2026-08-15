<?php

declare(strict_types=1);

use OpenCode\MultichannelLogger\Exceptions\FailedRequestException;
use OpenCode\MultichannelLogger\Zoom\SendMessageRequest;
use OpenCode\MultichannelLogger\Zoom\ZoomConnector;
use OpenCode\MultichannelLogger\Zoom\ZoomMessageData;
use OpenCode\MultichannelLogger\Zoom\ZoomMessenger;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('sends a message to the zoom webhook', function () {
    Saloon::fake([
        ZoomConnector::class => MockResponse::make(['status' => 200], 200),
    ]);

    (new ZoomMessenger('https://webhooks.zoom.us/api/chat/v1/message?token=abc'))
        ->send(new ZoomMessageData(to: 'channel-123', message: 'Hello world'));

    Saloon::assertSent(function ($request) {
        if (! $request instanceof SendMessageRequest) {
            return false;
        }

        $payload = $request->body()->all();

        return $payload['head']['type'] === 'SEND_MESSAGE'
            && $payload['body']['to'] === 'channel-123'
            && $payload['body']['message']['message'] === 'Hello world';
    });
});

it('throws when the webhook responds with an error', function () {
    Saloon::fake([
        ZoomConnector::class => MockResponse::make(['error' => 'unauthorized'], 401),
    ]);

    (new ZoomMessenger('https://webhooks.zoom.us/api/chat/v1/message?token=abc', retryTimes: 0))
        ->send(new ZoomMessageData(to: 'channel-123', message: 'x'));
})->throws(FailedRequestException::class);
