<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Discord;

use InvalidArgumentException;
use OpenCode\MultichannelLogger\Contracts\WebhookMessage;
use OpenCode\MultichannelLogger\Support\WebhookMessenger;
use OpenCode\MultichannelLogger\Support\WebhookRequest;

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
