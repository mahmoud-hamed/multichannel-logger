<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Discord;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

final class DiscordEmbedData extends Data
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $url = null,
        public ?int $color = null,
        public ?int $timestamp = null,
        /** @var list<array{name: string, value: string, inline?: bool}>|null */
        public ?array $fields = null,
        public ?string $footerText = null,
        public ?string $footerIcon = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $footer = $this->footerText !== null || $this->footerIcon !== null
            ? array_filter([
                'text' => $this->footerText,
                'icon_url' => $this->footerIcon,
            ], static fn ($value) => $value !== null)
            : null;

        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'color' => $this->color,
            'timestamp' => $this->timestamp !== null
                ? CarbonImmutable::createFromTimestamp($this->timestamp)->toIso8601String()
                : null,
            'fields' => $this->fields,
            'footer' => $footer,
        ], static fn ($value) => $value !== null);
    }
}
