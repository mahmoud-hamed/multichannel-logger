# Multichannel Logger

Send logs and notifications to **Slack**, **Discord** and **Zoom** webhooks from Laravel 11+.

Built on top of [Monolog](https://github.com/Seldaek/monolog), [Saloon](https://saloon.dev) and [Spatie Laravel Data](https://spatie.be/docs/laravel-data), this package exposes every channel as:

- A **custom log channel** — `Log::channel('slack')->error('...')`
- A **notification channel** — `$user->notify(new DeployFailed())`
- A **first-class messenger** — `MultichannelLogger::slack()->send($message)`

## Requirements

- PHP 8.2+
- Laravel 11 or 12

## Installation

```bash
composer require mahmoud-hamed/multichannel-logger
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=multichannel-logger-config
```

## Configuration

Set your webhook URLs in `.env`:

```env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/xxxxx/xxxxx/xxxxx
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/xxxxx/xxxxx
ZOOM_WEBHOOK_URL=https://webhooks.zoom.us/api/chat/v1/message?token=xxxxx
```

Or publish and edit `config/multichannel-logger.php`:

```php
'webhooks' => [
    'slack' => env('SLACK_WEBHOOK_URL'),
    'discord' => env('DISCORD_WEBHOOK_URL'),
    'zoom' => env('ZOOM_WEBHOOK_URL'),
],

'defaults' => [
    'client' => [
        'timeout' => 15,        // request timeout in seconds
        'retry_times' => 2,     // retries on 429/5xx responses
        'retry_interval_ms' => 100,
    ],
],
```

## Usage

### Log channels

Register a channel in `config/logging.php`:

```php
'channels' => [
    'slack' => [
        'driver' => 'custom',
        'via' => \OpenCode\MultichannelLogger\Slack\CreateSlackLogger::class,
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
        'level' => 'debug',
        'username' => 'Laravel',              // optional
        'emoji' => ':boom:',                  // optional
        'channel' => '#ops',                  // optional, target channel
        'retry_times' => 2,                   // optional
        'retry_interval_ms' => 100,           // optional
        'ignore_exceptions' => true,          // optional, never break the app
    ],

    'discord' => [
        'driver' => 'custom',
        'via' => \OpenCode\MultichannelLogger\Discord\CreateDiscordLogger::class,
        'webhook_url' => env('DISCORD_WEBHOOK_URL'),
        'level' => 'debug',
        'username' => 'Laravel',              // optional
        'avatar_url' => 'https://...',        // optional
    ],

    'zoom' => [
        'driver' => 'custom',
        'via' => \OpenCode\MultichannelLogger\Zoom\CreateZoomLogger::class,
        'webhook_url' => env('ZOOM_WEBHOOK_URL'),
        'to' => 'chat@company.com',           // required, the chat target
        'level' => 'debug',
    ],
],
```

Log away:

```php
Log::channel('slack')->error('Payment failed', ['order_id' => $order->id]);
Log::channel('discord')->critical('Database down', ['exception' => $e]);
Log::channel('zoom')->info('Deploy finished');
```

By default a channel never throws — webhook failures are caught and ignored so they can't take your application down. Set `ignore_exceptions => false` on the channel config to let `OpenCode\MultichannelLogger\Exceptions\MultichannelLoggerException` bubble up.

### Notification channels

Add a `toSlack()`, `toDiscord()` or `toZoom()` method to your notification and return the matching message data:

```php
use Illuminate\Notifications\Notification;
use OpenCode\MultichannelLogger\Discord\DiscordMessageData;
use OpenCode\MultichannelLogger\Discord\DiscordWebhookChannel;
use OpenCode\MultichannelLogger\Slack\SlackMessageData;
use OpenCode\MultichannelLogger\Slack\SlackWebhookChannel;
use OpenCode\MultichannelLogger\Zoom\ZoomMessageData;
use OpenCode\MultichannelLogger\Zoom\ZoomWebhookChannel;

class DeployFailed extends Notification
{
    public function via(object $notifiable): array
    {
        return [SlackWebhookChannel::class, DiscordWebhookChannel::class];
    }

    public function toSlack(object $notifiable): SlackMessageData
    {
        return new SlackMessageData(text: 'Deploy failed', username: 'Deploy Bot');
    }

    public function toDiscord(object $notifiable): DiscordMessageData
    {
        return new DiscordMessageData(content: 'Deploy failed');
    }

    public function toZoom(object $notifiable): ZoomMessageData
    {
        return new ZoomMessageData(to: 'team@company.com', message: 'Deploy failed');
    }
}
```

The notifiable must implement `Illuminate\Notifications\RoutesNotifications` (the default `Notifiable` trait) and expose its webhook via `routeNotificationFor`:

```php
public function routeNotificationForSlack(): ?string // snake_case
{
    return $this->slack_webhook;
}

public function routeNotificationForDiscord(): ?string
{
    return $this->discord_webhook;
}

public function routeNotificationForZoom(): ?string
{
    return $this->zoom_webhook;
}
```

The method can be `routeNotificationForSlack`, `routeNotificationFor('slack', $notification)`, or `routeNotificationForSlack($notification)` — Laravel's usual notification routing applies. If no webhook is resolved, a `MissingWebhookException` is thrown.

### Facade & messenger

Send raw messages from anywhere using the facade (falls back to the configured default webhook):

```php
use OpenCode\MultichannelLogger\Facades\MultichannelLogger;
use OpenCode\MultichannelLogger\Slack\SlackMessageData;

MultichannelLogger::slack()->send(new SlackMessageData(text: 'Hello world'));

// Override the webhook for a single call
MultichannelLogger::discord('https://discord.com/api/webhooks/...')->send(new DiscordMessageData(content: 'Hi'));
```

## Extending

Every integration is composed of four small pieces, all behind contracts:

| Concern          | Contract                                                         | Slack default           |
|------------------|------------------------------------------------------------------|-------------------------|
| Message payload  | `OpenCode\MultichannelLogger\Contracts\WebhookMessage`           | `Slack\SlackMessageData` |
| HTTP transport   | `OpenCode\MultichannelLogger\Contracts\WebhookMessenger`         | `Slack\SlackMessenger`   |
| Log formatting   | `OpenCode\MultichannelLogger\Contracts\LogMessageFormatter`      | `Slack\SlackFormatter`   |
| Log channel      | `OpenCode\MultichannelLogger\Logging\WebhookLoggerFactory`       | `Slack\CreateSlackLogger` |

For example, a custom Slack formatter:

```php
use Monolog\LogRecord;
use OpenCode\MultichannelLogger\Contracts\LogMessageFormatter;
use OpenCode\MultichannelLogger\Slack\SlackMessageData;

class MySlackFormatter implements LogMessageFormatter
{
    public function format(LogRecord $record): SlackMessageData
    {
        return new SlackMessageData(text: "[{$record->level->getName()}] {$record->message}");
    }
}
```

Register it by building the channel manually:

```php
'channels' => [
    'slack' => [
        'driver' => 'custom',
        'via' => function (array $config) {
            $messenger = new \OpenCode\MultichannelLogger\Slack\SlackMessenger(
                webhookUrl: $config['webhook_url'],
            );

            $handler = new \OpenCode\MultichannelLogger\Logging\WebhookLogHandler(
                messenger: $messenger,
                messageFormatter: new MySlackFormatter(),
            );

            return new Monolog\Logger('slack', [$handler]);
        },
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
    ],
],
```

Adding a brand-new provider (e.g. **Teams**) means implementing the same four contracts plus a `Create*Logger` factory — see `src/Slack` for the reference implementation.

## Testing

```bash
composer test      # Pest
composer analyse   # PHPStan (level 7 + strict rules)
composer format    # Laravel Pint
composer quality   # analyse + test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
