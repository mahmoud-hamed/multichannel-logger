<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Support;

use MahmoudHamed\MultichannelLogger\Exceptions\InvalidWebhookException;
use MahmoudHamed\MultichannelLogger\Exceptions\MissingWebhookException;

final class WebhookUrl
{
    private function __construct(
        public readonly string $scheme,
        public readonly string $host,
        public readonly string $path,
        public readonly ?string $query,
    ) {}

    public static function from(string $url): self
    {
        if (trim($url) === '') {
            throw MissingWebhookException::for('webhook');
        }

        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            throw InvalidWebhookException::for('webhook', $url);
        }

        return new self(
            scheme: $parts['scheme'],
            host: $parts['host'],
            path: $parts['path'] ?? '',
            query: $parts['query'] ?? null,
        );
    }

    public function baseUrl(): string
    {
        return $this->scheme.'://'.$this->host;
    }

    public function endpoint(): string
    {
        $endpoint = $this->path;

        if ($this->query !== null) {
            $endpoint .= '?'.$this->query;
        }

        return $endpoint;
    }

    public function full(): string
    {
        return $this->baseUrl().$this->endpoint();
    }
}
