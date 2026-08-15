<?php

declare(strict_types=1);

use MahmoudHamed\MultichannelLogger\Discord\DiscordConnector;
use MahmoudHamed\MultichannelLogger\Discord\DiscordMessageData;
use MahmoudHamed\MultichannelLogger\Facades\MultichannelLogger;
use MahmoudHamed\MultichannelLogger\MultichannelLogger as MultichannelLoggerService;
use MahmoudHamed\MultichannelLogger\Slack\SendMessageRequest;
use MahmoudHamed\MultichannelLogger\Slack\SlackConnector;
use MahmoudHamed\MultichannelLogger\Slack\SlackMessageData;
use MahmoudHamed\MultichannelLogger\Zoom\ZoomConnector;
use MahmoudHamed\MultichannelLogger\Zoom\ZoomMessageData;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('is resolvable from the container', function () {
    expect(app(MultichannelLoggerService::class))->toBeInstanceOf(MultichannelLoggerService::class);
});

it('sends through the facade using the configured slack webhook', function () {
    Saloon::fake([
        SlackConnector::class => MockResponse::make(['ok' => true], 200),
    ]);

    MultichannelLogger::slack()->send(new SlackMessageData(text: 'via facade'));

    Saloon::assertSent(fn ($request) => $request instanceof SendMessageRequest);
});

it('sends through an explicit webhook override', function () {
    Saloon::fake([
        SlackConnector::class => MockResponse::make(['ok' => true], 200),
    ]);

    MultichannelLogger::slack('https://hooks.slack.com/services/T/B/XXXX')->send(
        new SlackMessageData(text: 'explicit webhook')
    );

    Saloon::assertSent(function ($request) {
        return $request instanceof SendMessageRequest
            && $request->body()->all()['text'] === 'explicit webhook';
    });
});

it('exposes discord and zoom messengers', function () {
    Saloon::fake([
        DiscordConnector::class => MockResponse::make([], 204),
        ZoomConnector::class => MockResponse::make(['status' => 200], 200),
    ]);

    MultichannelLogger::discord()->send(new DiscordMessageData(content: 'discord'));
    MultichannelLogger::zoom()->send(new ZoomMessageData(to: 'channel-123', message: 'zoom'));

    Saloon::assertSentCount(2);
});
