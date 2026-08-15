<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Contracts;

interface WebhookMessage
{
    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array;
}
