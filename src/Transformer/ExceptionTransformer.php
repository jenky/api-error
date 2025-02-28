<?php

declare(strict_types=1);

namespace Jenky\ApiError\Transformer;

use Jenky\ApiError\Problem;

interface ExceptionTransformer
{
    public function transform(\Throwable $exception): Problem;
}
