<?php

declare(strict_types=1);

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use OpenCode\MultichannelLogger\Discord\DiscordConnector;
use OpenCode\MultichannelLogger\Discord\DiscordMessageData;
use OpenCode\MultichannelLogger\Discord\DiscordWebhookChannel;
use OpenCode\MultichannelLogger\Discord\SendMessageRequest as DiscordSendMessageRequest;
use OpenCode\MultichannelLogger\Exceptions\MissingWebhookException;
use OpenCode\MultichannelLogger\Slack\SendMessageRequest as SlackSendMessageRequest;
use OpenCode\MultichannelLogger\Slack\SlackConnector;
use OpenCode\MultichannelLogger\Slack\SlackMessageData;
use OpenCode\MultichannelLogger\Slack\SlackWebhookChannel;
use OpenCode\MultichannelLogger\Zoom\SendMessageRequest as ZoomSendMessageRequest;
use OpenCode\MultichannelLogger\Zoom\ZoomConnector;
use OpenCode\MultichannelLogger\Zoom\ZoomMessageData;
use OpenCode\MultichannelLogger\Zoom\ZoomWebhookChannel;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('sends notifications through the slack channel', function () {
    Saloon::fake([
        SlackConnector::class => MockResponse::make(['ok' => true], 200),
    ]);

    $notifiable = new class
    {
        use Notifiable;

        public function routeNotificationFor($driver, $notification = null): string
        {
            return 'https://hooks.slack.com/services/T00000/B00000/XXXXX';
        }
    };

    $notification = new class extends Notification
    {
        public function via($notifiable): array
        {
            return [SlackWebhookChannel::class];
        }

        public function toSlack($notifiable): SlackMessageData
        {
            return new SlackMessageData(text: 'Notification test');
        }
    };

    $notifiable->notify($notification);

    Saloon::assertSent(function ($request) {
        return $request instanceof SlackSendMessageRequest
            && $request->body()->all()['text'] === 'Notification test';
    });
});

it('sends notifications through the discord channel', function () {
    Saloon::fake([
        DiscordConnector::class => MockResponse::make([], 204),
    ]);

    $notifiable = new class
    {
        use Notifiable;

        public function routeNotificationFor($driver, $notification = null): string
        {
            return 'https://discord.com/api/webhooks/123/abc';
        }
    };

    $notification = new class extends Notification
    {
        public function via($notifiable): array
        {
            return [DiscordWebhookChannel::class];
        }

        public function toDiscord($notifiable): DiscordMessageData
        {
            return new DiscordMessageData(content: 'Discord notification');
        }
    };

    $notifiable->notify($notification);

    Saloon::assertSent(function ($request) {
        return $request instanceof DiscordSendMessageRequest
            && $request->body()->all()['content'] === 'Discord notification';
    });
});

it('sends notifications through the zoom channel', function () {
    Saloon::fake([
        ZoomConnector::class => MockResponse::make(['status' => 200], 200),
    ]);

    $notifiable = new class
    {
        use Notifiable;

        public function routeNotificationFor($driver, $notification = null): string
        {
            return 'https://webhooks.zoom.us/api/chat/v1/message?token=abc';
        }
    };

    $notification = new class extends Notification
    {
        public function via($notifiable): array
        {
            return [ZoomWebhookChannel::class];
        }

        public function toZoom($notifiable): ZoomMessageData
        {
            return new ZoomMessageData(to: 'channel-123', message: 'Zoom notification');
        }
    };

    $notifiable->notify($notification);

    Saloon::assertSent(function ($request) {
        return $request instanceof ZoomSendMessageRequest
            && $request->body()->all()['body']['message']['message'] === 'Zoom notification';
    });
});

it('throws when the notifiable has no webhook route', function () {
    $notifiable = new class
    {
        use Notifiable;
    };

    $notification = new class extends Notification
    {
        public function via($notifiable): array
        {
            return [SlackWebhookChannel::class];
        }

        public function toSlack($notifiable): SlackMessageData
        {
            return new SlackMessageData(text: 'No route');
        }
    };

    $notifiable->notify($notification);
})->throws(MissingWebhookException::class);
