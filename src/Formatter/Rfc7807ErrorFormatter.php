<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

use Jenky\ApiError\Problem;
use Jenky\ApiError\Rfc7807Problem;

final class Rfc7807ErrorFormatter extends AbstractErrorFormatter
{
    protected function createProblem(\Throwable $exception): Problem
    {
        return Rfc7807Problem::createFromThrowable($exception);
    }

    protected function getFormat(): array
    {
        $format = [
            'type' => '{type}',
            'title' => '{status_text}',
            'detail' => '{message}',
            'status' => '{status_code}',
            'invalid-params' => '{invalid_params}',
        ];

        if ($this->debug) {
            $format['debug'] = '{debug}';
        }

        return $format;
    }
}
