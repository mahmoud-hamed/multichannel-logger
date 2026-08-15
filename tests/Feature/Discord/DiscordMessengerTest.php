<?php

declare(strict_types=1);

use OpenCode\MultichannelLogger\Discord\DiscordConnector;
use OpenCode\MultichannelLogger\Discord\DiscordMessageData;
use OpenCode\MultichannelLogger\Discord\DiscordMessenger;
use OpenCode\MultichannelLogger\Discord\SendMessageRequest;
use OpenCode\MultichannelLogger\Exceptions\FailedRequestException;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('sends a message to the discord webhook', function () {
    Saloon::fake([
        DiscordConnector::class => MockResponse::make([], 204),
    ]);

    (new DiscordMessenger('https://discord.com/api/webhooks/123/abc'))
        ->send(new DiscordMessageData(content: 'Hello world'));

    Saloon::assertSent(function ($request) {
        return $request instanceof SendMessageRequest
            && $request->body()->all()['content'] === 'Hello world';
    });
});

it('throws when the webhook responds with an error', function () {
    Saloon::fake([
        DiscordConnector::class => MockResponse::make(['message' => 'Invalid Webhook Token'], 401),
    ]);

    (new DiscordMessenger('https://discord.com/api/webhooks/123/abc', retryTimes: 0))
        ->send(new DiscordMessageData(content: 'x'));
})->throws(FailedRequestException::class);
