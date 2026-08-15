<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Slack;

use InvalidArgumentException;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessage;
use MahmoudHamed\MultichannelLogger\Support\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\Support\WebhookRequest;

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
