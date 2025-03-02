<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

use Jenky\ApiError\GenericProblem;
use Jenky\ApiError\Problem;

/**
 * @extends AbstractErrorFormatter<string>
 */
final class GenericErrorFormatter extends AbstractErrorFormatter
{
    protected function createProblem(\Throwable $exception): Problem
    {
        return GenericProblem::createFromThrowable($exception);
    }

    protected function getFormat(): array
    {
        $format = [
            'message' => '{title}',
            'status' => '{status_code}',
            'code' => '{code}',
        ];

        if ($this->debug) {
            $format['debug'] = '{debug}';
        }

        return $format;
    }
}
