<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Facades;

use Illuminate\Support\Facades\Facade;
use MahmoudHamed\MultichannelLogger\Discord\DiscordMessenger;
use MahmoudHamed\MultichannelLogger\MultichannelLogger as MultichannelLoggerService;
use MahmoudHamed\MultichannelLogger\Slack\SlackMessenger;
use MahmoudHamed\MultichannelLogger\Zoom\ZoomMessenger;

/**
 * @method static SlackMessenger slack(?string $webhookUrl = null)
 * @method static DiscordMessenger discord(?string $webhookUrl = null)
 * @method static ZoomMessenger zoom(?string $webhookUrl = null)
 *
 * @see MultichannelLoggerService
 */
class MultichannelLogger extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MultichannelLoggerService::class;
    }
}
