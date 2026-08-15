<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Exceptions;

use Saloon\Http\Response;
use Throwable;

class FailedRequestException extends MultichannelLoggerException
{
    public static function for(string $channel, Response $response): self
    {
        return new self(
            "The [{$channel}] webhook request failed with status [{$response->status()}].",
            $response->status()
        );
    }

    public static function forException(string $channel, Throwable $exception): self
    {
        return new self(
            "The [{$channel}] webhook request failed: {$exception->getMessage()}",
            0,
            $exception
        );
    }
}
