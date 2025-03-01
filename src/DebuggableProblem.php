<?php

declare(strict_types=1);

namespace Jenky\ApiError;

interface DebuggableProblem extends Problem
{
    /**
     * Get the debug context of the object.
     *
     * @return array<string, mixed>
     */
    public function debugContext(): array;
}
