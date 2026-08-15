<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Zoom;

use OpenCode\MultichannelLogger\Support\WebhookRequest;

final class SendMessageRequest extends WebhookRequest
{
    public function __construct(private readonly ZoomMessageData $message) {}

    protected function defaultBody(): array
    {
        return $this->message->toPayload();
    }
}
