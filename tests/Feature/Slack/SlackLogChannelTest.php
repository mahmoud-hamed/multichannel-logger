<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Log;
use MahmoudHamed\MultichannelLogger\Exceptions\MissingWebhookException;
use MahmoudHamed\MultichannelLogger\Slack\CreateSlackLogger;
use MahmoudHamed\MultichannelLogger\Slack\SendMessageRequest;
use MahmoudHamed\MultichannelLogger\Slack\SlackConnector;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('sends log records to slack through a custom log channel', function () {
    Saloon::fake([
        SlackConnector::class => MockResponse::make(['ok' => true], 200),
    ]);

    config()->set('logging.channels.slack', [
        'driver' => 'custom',
        'via' => CreateSlackLogger::class,
        'webhook_url' => 'https://hooks.slack.com/services/T00000/B00000/XXXXX',
        'level' => 'debug',
    ]);

    Log::channel('slack')->error('User did a thing', ['user_id' => 1]);

    Saloon::assertSentCount(1);

    Saloon::assertSent(function ($request) {
        return $request instanceof SendMessageRequest
            && str_contains((string) $request->body()->all()['text'], 'User did a thing');
    });
});

it('does not send records below the configured level', function () {
    Saloon::fake([
        SlackConnector::class => MockResponse::make(['ok' => true], 200),
    ]);

    config()->set('logging.channels.slack', [
        'driver' => 'custom',
        'via' => CreateSlackLogger::class,
        'webhook_url' => 'https://hooks.slack.com/services/T00000/B00000/XXXXX',
        'level' => 'error',
    ]);

    Log::channel('slack')->info('Too quiet to send');

    Saloon::assertNothingSent();
});

it('throws when no webhook url is configured', function () {
    (new CreateSlackLogger)->__invoke(['level' => 'error']);
})->throws(MissingWebhookException::class);
