<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Exceptions;

class MissingWebhookException extends MultichannelLoggerException
{
    public static function for(string $channel): self
    {
        return new self("No webhook URL configured for the [{$channel}] channel.");
    }
}
