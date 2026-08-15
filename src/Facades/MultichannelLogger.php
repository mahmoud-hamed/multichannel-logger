<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Facades;

use Illuminate\Support\Facades\Facade;
use OpenCode\MultichannelLogger\Discord\DiscordMessenger;
use OpenCode\MultichannelLogger\MultichannelLogger as MultichannelLoggerService;
use OpenCode\MultichannelLogger\Slack\SlackMessenger;
use OpenCode\MultichannelLogger\Zoom\ZoomMessenger;

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
