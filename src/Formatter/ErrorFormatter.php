<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

interface ErrorFormatter
{
    public function format(\Throwable $exception): mixed;
}
