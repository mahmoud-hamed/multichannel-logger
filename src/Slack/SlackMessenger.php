<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Slack;

use InvalidArgumentException;
use OpenCode\MultichannelLogger\Contracts\WebhookMessage;
use OpenCode\MultichannelLogger\Support\WebhookMessenger;
use OpenCode\MultichannelLogger\Support\WebhookRequest;

final class SlackMessenger extends WebhookMessenger
{
    protected function connectorClass(): string
    {
        return SlackConnector::class;
    }

    protected function channel(): string
    {
        return 'slack';
    }

    protected function makeRequest(WebhookMessage $message): WebhookRequest
    {
        if (! $message instanceof SlackMessageData) {
            throw new InvalidArgumentException('The [slack] messenger expects a SlackMessageData instance.');
        }

        return new SendMessageRequest($message);
    }
}
