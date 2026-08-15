<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Zoom;

use InvalidArgumentException;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessage;
use MahmoudHamed\MultichannelLogger\Support\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\Support\WebhookRequest;

final class ZoomMessenger extends WebhookMessenger
{
    protected function connectorClass(): string
    {
        return ZoomConnector::class;
    }

    protected function channel(): string
    {
        return 'zoom';
    }

    protected function makeRequest(WebhookMessage $message): WebhookRequest
    {
        if (! $message instanceof ZoomMessageData) {
            throw new InvalidArgumentException('The [zoom] messenger expects a ZoomMessageData instance.');
        }

        return new SendMessageRequest($message);
    }
}
