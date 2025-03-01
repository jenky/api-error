<?php

declare(strict_types=1);

namespace Jenky\ApiError;

interface Problem
{
    /**
     * Get the context the object.
     *
     * @return array<string, mixed>
     */
    public function context(): array;
}
