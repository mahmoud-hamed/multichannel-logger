<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MultichannelLoggerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/multichannel-logger.php',
            'multichannel-logger'
        );

        $this->app->singleton(MultichannelLogger::class, static function (Application $app): MultichannelLogger {
            return new MultichannelLogger($app->make(Repository::class));
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/multichannel-logger.php' => config_path('multichannel-logger.php'),
        ], 'multichannel-logger-config');
    }
}
