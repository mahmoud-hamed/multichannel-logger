<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Log;
use OpenCode\MultichannelLogger\Discord\CreateDiscordLogger;
use OpenCode\MultichannelLogger\Discord\DiscordConnector;
use OpenCode\MultichannelLogger\Discord\SendMessageRequest;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('sends log records to discord through a custom log channel', function () {
    Saloon::fake([
        DiscordConnector::class => MockResponse::make([], 204),
    ]);

    config()->set('logging.channels.discord', [
        'driver' => 'custom',
        'via' => CreateDiscordLogger::class,
        'webhook_url' => 'https://discord.com/api/webhooks/123/abc',
        'level' => 'debug',
    ]);

    Log::channel('discord')->warning('Disk space is low', ['usage' => '95%']);

    Saloon::assertSentCount(1);

    Saloon::assertSent(function ($request) {
        return $request instanceof SendMessageRequest
            && str_contains((string) $request->body()->all()['content'], 'Disk space is low');
    });
});
