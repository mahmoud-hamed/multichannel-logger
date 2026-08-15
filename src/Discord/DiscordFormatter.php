<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Discord;

use Illuminate\Support\Arr;
use Monolog\Level;
use Monolog\LogRecord;
use OpenCode\MultichannelLogger\Contracts\LogMessageFormatter;
use Throwable;

final class DiscordFormatter implements LogMessageFormatter
{
    public function __construct(
        private readonly string $username = 'Laravel',
        private readonly ?string $avatarUrl = null,
    ) {}

    public function format(LogRecord $record): DiscordMessageData
    {
        return new DiscordMessageData(
            content: $this->summary($record),
            username: $this->username,
            avatarUrl: $this->avatarUrl,
            embeds: collect([new DiscordEmbedData(
                title: $record->level->getName(),
                description: $record->message,
                color: $this->colorFor($record->level),
                fields: $this->fields($record),
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

    /**
     * @return list<array{name: string, value: string, inline: bool}>
     */
    private function fields(LogRecord $record): array
    {
        $fields = [];

        foreach (Arr::except($record->context, ['exception']) as $key => $value) {
            $fields[] = [
                'name' => (string) $key,
                'value' => is_scalar($value)
                    ? (string) $value
                    : $this->encode($value),
                'inline' => is_scalar($value),
            ];
        }

        $exception = $record->context['exception'] ?? null;

        if ($exception instanceof Throwable) {
            $fields[] = [
                'name' => 'exception',
                'value' => substr((string) $exception, 0, 1024),
                'inline' => false,
            ];
        }

        return $fields;
    }

    private function encode(mixed $value): string
    {
        $encoded = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return $encoded === false ? '' : $encoded;
    }

    private function colorFor(Level $level): int
    {
        return match ($level) {
            Level::Emergency, Level::Alert, Level::Critical, Level::Error => 0xE01E5A,
            Level::Warning, Level::Notice => 0xFFC300,
            Level::Info => 0x36A64F,
            default => 0x808080,
        };
    }
}
