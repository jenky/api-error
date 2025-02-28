<?php

declare(strict_types=1);

namespace Jenky\ApiError;

interface Problem
{
    /**
     * Get the data representation of the object.
     */
    public function toRepresentation(): mixed;
}
