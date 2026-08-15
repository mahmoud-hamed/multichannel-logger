<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Slack;

use Illuminate\Support\Arr;
use MahmoudHamed\MultichannelLogger\Contracts\LogMessageFormatter;
use Monolog\Level;
use Monolog\LogRecord;
use Throwable;

final class SlackFormatter implements LogMessageFormatter
{
    public function __construct(
        private readonly string $username = 'Laravel',
        private readonly string $emoji = ':boom:',
        private readonly ?string $channel = null,
    ) {}

    public function format(LogRecord $record): SlackMessageData
    {
        return new SlackMessageData(
            text: $this->summary($record),
            username: $this->username,
            iconEmoji: $this->emoji,
            channel: $this->channel,
            attachments: collect([new SlackAttachmentData(
                color: $this->colorFor($record->level),
                fallback: $record->message,
                text: $this->details($record),
                fields: $this->contextFields($record),
                ts: $record->datetime->getTimestamp(),
            )]),
        );
    }

    private function summary(LogRecord $record): string
    {
        return sprintf(
            '[%s] %s.%s: %s',
            $record->datetime->format('Y-m-d H:i:s'),
            $record->channel,
            $record->level->getName(),
            $record->message
        );
    }

    private function details(LogRecord $record): ?string
    {
        $exception = $record->context['exception'] ?? null;

        if ($exception instanceof Throwable) {
            return (string) $exception;
        }

        $context = Arr::except($record->context, ['exception']);

        if ($context === []) {
            return null;
        }

        $encoded = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return $encoded === false ? null : $encoded;
    }

    /**
     * @return list<array{title: string, value: string, short: bool}>
     */
    private function contextFields(LogRecord $record): array
    {
        $fields = [];

        foreach (Arr::except($record->context, ['exception']) as $key => $value) {
            $fields[] = [
                'title' => (string) $key,
                'value' => is_scalar($value)
                    ? (string) $value
                    : $this->encode($value),
                'short' => is_scalar($value),
            ];
        }

        return $fields;
    }

    private function encode(mixed $value): string
    {
        $encoded = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return $encoded === false ? '' : $encoded;
    }

    private function colorFor(Level $level): string
    {
        return match ($level) {
            Level::Emergency, Level::Alert, Level::Critical, Level::Error => 'danger',
            Level::Warning, Level::Notice => '#ffc300',
            Level::Info => 'good',
            default => '#808080',
        };
    }
}
