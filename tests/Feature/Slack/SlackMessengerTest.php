<?php

declare(strict_types=1);

use OpenCode\MultichannelLogger\Discord\DiscordMessageData;
use OpenCode\MultichannelLogger\Exceptions\FailedRequestException;
use OpenCode\MultichannelLogger\Exceptions\MissingWebhookException;
use OpenCode\MultichannelLogger\Slack\SendMessageRequest;
use OpenCode\MultichannelLogger\Slack\SlackConnector;
use OpenCode\MultichannelLogger\Slack\SlackMessageData;
use OpenCode\MultichannelLogger\Slack\SlackMessenger;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('sends a message to the slack webhook', function () {
    Saloon::fake([
        SlackConnector::class => MockResponse::make(['ok' => true], 200),
    ]);

    (new SlackMessenger('https://hooks.slack.com/services/T00000/B00000/XXXXX'))
        ->send(new SlackMessageData(text: 'Hello world'));

    Saloon::assertSent(function ($request) {
        return $request instanceof SendMessageRequest
            && $request->body()->all()['text'] === 'Hello world';
    });
});

it('sends to the full webhook url', function () {
    $webhookUrl = 'https://hooks.slack.com/services/T00000/B00000/XXXXX';

    Saloon::fake([
        SlackConnector::class => MockResponse::make(['ok' => true], 200),
    ]);

    (new SlackMessenger($webhookUrl))->send(new SlackMessageData(text: 'Hi'));

    Saloon::assertSent(function ($request, $response) use ($webhookUrl) {
        $connector = $response->getPendingRequest()->getConnector();

        return $request instanceof SendMessageRequest
            && $connector instanceof SlackConnector
            && $connector->webhookUrl()->full() === $webhookUrl;
    });
});

it('throws when the webhook responds with an error', function () {
    Saloon::fake([
        SlackConnector::class => MockResponse::make(['error' => 'invalid_payload'], 400),
    ]);

    (new SlackMessenger('https://hooks.slack.com/services/T/B/X', retryTimes: 0))
        ->send(new SlackMessageData(text: 'x'));
})->throws(FailedRequestException::class);

it('throws when no webhook url is provided', function () {
    (new SlackMessenger(''))->send(new SlackMessageData(text: 'x'));
})->throws(MissingWebhookException::class);

it('rejects messages of the wrong type', function () {
    (new SlackMessenger('https://hooks.slack.com/services/T/B/X'))
        ->send(new DiscordMessageData(content: 'hi'));
})->throws(InvalidArgumentException::class);
