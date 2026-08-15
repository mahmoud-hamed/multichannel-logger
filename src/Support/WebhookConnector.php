<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Support;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\HasTimeout;

abstract class WebhookConnector extends Connector
{
    use HasTimeout;

    protected float $requestTimeout = 15;

    protected float $connectTimeout = 10;

    public function __construct(
        private readonly WebhookUrl $webhookUrl,
        int $timeout = 15,
    ) {
        $this->requestTimeout = (float) $timeout;
    }

    public function resolveBaseUrl(): string
    {
        return $this->webhookUrl->full();
    }

    public function webhookUrl(): WebhookUrl
    {
        return $this->webhookUrl;
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }
}
