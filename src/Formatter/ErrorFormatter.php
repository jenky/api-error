<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

use Jenky\ApiError\Problem;

interface ErrorFormatter
{
    public function format(Problem $problem): mixed;
}
