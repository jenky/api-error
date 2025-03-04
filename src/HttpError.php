<?php

declare(strict_types=1);

namespace Jenky\ApiError;

final class HttpError implements \JsonSerializable
{
    /**
     * @param  array<string, mixed> $headers
     */
    public function __construct(
        public readonly mixed $data,
        public readonly int $statusCode = 500,
        public readonly array $headers = [],
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return $this->data;
    }
}
