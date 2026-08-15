<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Exceptions;

class InvalidWebhookException extends MultichannelLoggerException
{
    public static function for(string $channel, string $url): self
    {
        return new self("The webhook URL for the [{$channel}] channel is invalid: {$url}");
    }
}
