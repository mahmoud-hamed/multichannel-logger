<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Support;

use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessage;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessenger;
use MahmoudHamed\MultichannelLogger\Exceptions\MissingWebhookException;

abstract class WebhookChannel
{
    abstract protected function messageMethod(): string;

    /**
     * @return class-string<WebhookMessage>
     */
    abstract protected function messageClass(): string;

    abstract protected function routeKey(): string;

    abstract protected function makeMessenger(string $webhookUrl): WebhookMessenger;

    public function send(mixed $notifiable, Notification $notification): void
    {
        $message = $this->extractMessage($notification, $notifiable);

        $this->makeMessenger($this->extractWebhook($notifiable, $notification))->send($message);
    }

    private function extractMessage(Notification $notification, mixed $notifiable): WebhookMessage
    {
        $method = $this->messageMethod();
        $expected = $this->messageClass();

        // The notification methods (toSlack, toDiscord, ...) are resolved dynamically.
        // @phpstan-ignore-next-line method.dynamicName
        $message = $notification->{$method}($notifiable);

        if (! $message instanceof WebhookMessage || ! $message instanceof $expected) {
            throw new InvalidArgumentException(sprintf(
                'The [%s] notification method must return an instance of [%s].',
                $method,
                $expected
            ));
        }

        return $message;
    }

    private function extractWebhook(mixed $notifiable, Notification $notification): string
    {
        $webhookUrl = $notifiable->routeNotificationFor($this->routeKey(), $notification);

        if (! is_string($webhookUrl) || trim($webhookUrl) === '') {
            throw MissingWebhookException::for($this->routeKey());
        }

        return $webhookUrl;
    }
}
