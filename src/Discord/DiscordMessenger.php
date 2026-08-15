<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Discord;

use InvalidArgumentException;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessage;
use MahmoudHamed\MultichannelLogger\Support\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\Support\WebhookRequest;

final class DiscordMessenger extends WebhookMessenger
{
    protected function connectorClass(): string
    {
        return DiscordConnector::class;
    }

    protected function channel(): string
    {
        return 'discord';
    }

    protected function makeRequest(WebhookMessage $message): WebhookRequest
    {
        if (! $message instanceof DiscordMessageData) {
            throw new InvalidArgumentException('The [discord] messenger expects a DiscordMessageData instance.');
        }

        return new SendMessageRequest($message);
    }
}
