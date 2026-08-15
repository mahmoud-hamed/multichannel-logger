<?php

declare(strict_types=1);

namespace OpenCode\MultichannelLogger\Zoom;

use Illuminate\Support\Str;
use OpenCode\MultichannelLogger\Contracts\WebhookMessage;
use Spatie\LaravelData\Data;

final class ZoomMessageData extends Data implements WebhookMessage
{
    public function __construct(
        public string $to,
        public string $message,
        public ?string $id = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'head' => [
                'type' => 'SEND_MESSAGE',
                'id' => $this->id ?? Str::uuid()->toString(),
            ],
            'body' => [
                'to' => $this->to,
                'message' => [
                    'message' => $this->message,
                ],
            ],
        ];
    }
}
