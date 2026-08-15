<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Support;

use OpenCode\MultichannelLogger\Contracts\WebhookMessage;
use OpenCode\MultichannelLogger\Contracts\WebhookMessenger as WebhookMessengerContract;
use OpenCode\MultichannelLogger\Exceptions\FailedRequestException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;

abstract class WebhookMessenger implements WebhookMessengerContract
{
    private ?WebhookConnector $connector = null;

    public function __construct(
        private readonly string|WebhookUrl $webhookUrl,
        private readonly int $timeout = 15,
        private readonly int $retryTimes = 2,
        private readonly int $retryInterval = 100,
    ) {}

    /**
     * @return class-string<WebhookConnector>
     */
    abstract protected function connectorClass(): string;

    abstract protected function makeRequest(WebhookMessage $message): WebhookRequest;

    abstract protected function channel(): string;

    public function send(WebhookMessage $message): void
    {
        $request = $this->makeRequest($message);

        $request->tries = $this->retryTimes > 0 ? $this->retryTimes + 1 : 1;
        $request->retryInterval = $this->retryInterval;
        $request->throwOnMaxTries = false;

        try {
            $response = $this->connector()->send($request);
        } catch (FatalRequestException|RequestException $exception) {
            throw FailedRequestException::forException($this->channel(), $exception);
        }

        if ($response->failed()) {
            throw FailedRequestException::for($this->channel(), $response);
        }
    }

    private function connector(): WebhookConnector
    {
        return $this->connector ??= new ($this->connectorClass())($this->webhookUrl(), $this->timeout);
    }

    private function webhookUrl(): WebhookUrl
    {
        if ($this->webhookUrl instanceof WebhookUrl) {
            return $this->webhookUrl;
        }

        return WebhookUrl::from($this->webhookUrl);
    }
}
