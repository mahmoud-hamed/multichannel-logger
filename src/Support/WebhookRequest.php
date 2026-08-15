<?php

declare(strict_types=1);

namespace MahmoudHamed\MultichannelLogger\Support;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

abstract class WebhookRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '';
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if (! $exception instanceof RequestException) {
            return true;
        }

        $status = $exception->getResponse()->status();

        return $status === 429 || $status >= 500;
    }
}
