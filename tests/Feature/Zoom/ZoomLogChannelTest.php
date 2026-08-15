<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Log;
use OpenCode\MultichannelLogger\Exceptions\MissingConfigurationException;
use OpenCode\MultichannelLogger\Zoom\CreateZoomLogger;
use OpenCode\MultichannelLogger\Zoom\SendMessageRequest;
use OpenCode\MultichannelLogger\Zoom\ZoomConnector;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('sends log records to zoom through a custom log channel', function () {
    Saloon::fake([
        ZoomConnector::class => MockResponse::make(['status' => 200], 200),
    ]);

    config()->set('logging.channels.zoom', [
        'driver' => 'custom',
        'via' => CreateZoomLogger::class,
        'webhook_url' => 'https://webhooks.zoom.us/api/chat/v1/message?token=abc',
        'to' => 'channel-123',
        'level' => 'debug',
    ]);

    Log::channel('zoom')->error('Deploy failed');

    Saloon::assertSentCount(1);

    Saloon::assertSent(function ($request) {
        return $request instanceof SendMessageRequest
            && str_contains((string) $request->body()->all()['body']['message']['message'], 'Deploy failed');
    });
});

it('requires a chat target', function () {
    (new CreateZoomLogger)->__invoke([
        'webhook_url' => 'https://webhooks.zoom.us/api/chat/v1/message?token=abc',
        'level' => 'debug',
    ]);
})->throws(MissingConfigurationException::class);
