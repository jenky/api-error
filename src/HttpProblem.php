<?php

declare(strict_types=1);

namespace Jenky\ApiError;

interface HttpProblem
{
    /**
     * Returns the status code.
     */
    public function getStatusCode(): int;

    /**
     * Returns response headers.
     *
     * @return array<string, mixed>
     */
    public function getHeaders(): array;
}
