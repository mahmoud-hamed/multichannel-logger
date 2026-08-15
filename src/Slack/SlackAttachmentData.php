<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Slack;

use Spatie\LaravelData\Data;

final class SlackAttachmentData extends Data
{
    public function __construct(
        public ?string $color = null,
        public ?string $fallback = null,
        public ?string $pretext = null,
        public ?string $title = null,
        public ?string $titleLink = null,
        public ?string $text = null,
        public ?string $authorName = null,
        public ?string $authorLink = null,
        public ?string $authorIcon = null,
        public ?string $footer = null,
        public ?string $footerIcon = null,
        public ?int $ts = null,
        /** @var list<array{title: string, value: string, short?: bool}>|null */
        public ?array $fields = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return array_filter([
            'color' => $this->color,
            'fallback' => $this->fallback,
            'pretext' => $this->pretext,
            'title' => $this->title,
            'title_link' => $this->titleLink,
            'text' => $this->text,
            'author_name' => $this->authorName,
            'author_link' => $this->authorLink,
            'author_icon' => $this->authorIcon,
            'footer' => $this->footer,
            'footer_icon' => $this->footerIcon,
            'ts' => $this->ts,
            'fields' => $this->fields,
        ], static fn ($value) => $value !== null);
    }
}
