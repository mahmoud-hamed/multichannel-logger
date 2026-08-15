<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Slack;

use Illuminate\Support\Collection;
use MahmoudHamed\MultichannelLogger\Contracts\WebhookMessage;
use Spatie\LaravelData\Data;

final class SlackMessageData extends Data implements WebhookMessage
{
    public function __construct(
        public ?string $text = null,
        public ?string $channel = null,
        public ?string $username = null,
        public ?string $iconEmoji = null,
        public ?string $iconUrl = null,
        public ?string $threadTs = null,
        /** @var Collection<int, SlackAttachmentData>|null */
        public ?Collection $attachments = null,
        public bool $markdown = true,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return array_filter([
            'text' => $this->text,
            'channel' => $this->channel,
            'username' => $this->username,
            'icon_emoji' => $this->iconEmoji,
            'icon_url' => $this->iconUrl,
            'thread_ts' => $this->threadTs,
            'mrkdwn' => $this->markdown,
            'attachments' => $this->attachments?->map(
                static fn (SlackAttachmentData $attachment): array => $attachment->toPayload()
            )->all(),
        ], static fn ($value) => $value !== null);
    }
}
