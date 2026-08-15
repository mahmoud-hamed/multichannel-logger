<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Discord;

use Illuminate\Support\Collection;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessage;
use Spatie\LaravelData\Data;

final class DiscordMessageData extends Data implements WebhookMessage
{
    public function __construct(
        public ?string $content = null,
        public ?string $username = null,
        public ?string $avatarUrl = null,
        public ?bool $tts = null,
        /** @var Collection<int, DiscordEmbedData>|null */
        public ?Collection $embeds = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return array_filter([
            'content' => $this->content,
            'username' => $this->username,
            'avatar_url' => $this->avatarUrl,
            'tts' => $this->tts,
            'embeds' => $this->embeds?->map(
                static fn (DiscordEmbedData $embed): array => $embed->toPayload()
            )->all(),
        ], static fn ($value) => $value !== null);
    }
}
