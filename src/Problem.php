<?php

declare(strict_types=1);

namespace Jenky\ApiError;

interface Problem
{
    /**
     * Get the array representation of the object.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
