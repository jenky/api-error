<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

use Jenky\ApiError\HttpError;

interface ErrorFormatter
{
    public function format(\Throwable $exception): HttpError;
}
