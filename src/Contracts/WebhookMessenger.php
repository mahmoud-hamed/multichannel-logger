<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Contracts;

interface WebhookMessenger
{
    public function send(WebhookMessage $message): void;
}
