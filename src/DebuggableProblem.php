<?php

declare(strict_types=1);

namespace Jenky\ApiError;

interface DebuggableProblem extends Problem
{
    /**
     * Get the debug representation of the object.
     */
    public function toDebugRepresentation(): mixed;
}
