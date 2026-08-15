<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Tests;

use OpenCode\MultichannelLogger\MultichannelLoggerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Laravel\SaloonServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MultichannelLoggerServiceProvider::class,
            SaloonServiceProvider::class,
            LaravelDataServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('multichannel-logger.webhooks.slack', 'https://hooks.slack.com/services/xxxxx/xxxxx/xxxxx');
        $app['config']->set('multichannel-logger.webhooks.discord', 'https://discord.com/api/webhooks/xxxxx/xxxxx');
        $app['config']->set('multichannel-logger.webhooks.zoom', 'https://webhooks.zoom.us/api/chat/v1/message?token=test-token');
    }
}
