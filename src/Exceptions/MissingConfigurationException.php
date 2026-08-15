<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Exceptions;

class MissingConfigurationException extends MultichannelLoggerException
{
    public static function for(string $channel, string $key): self
    {
        return new self("Missing [{$key}] configuration for the [{$channel}] channel.");
    }
}
