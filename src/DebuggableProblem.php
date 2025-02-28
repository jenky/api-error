<?php

declare(strict_types=1);

namespace Jenky\ApiError;

interface DebuggableProblem extends Problem
{
    /**
     * Get the debug array representation of the object.
     *
     * @return array<array-key, mixed>
     */
    public function toDebugArray(): array;
}
