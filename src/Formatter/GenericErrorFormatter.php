<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

use Jenky\ApiError\GenericProblem;
use Jenky\ApiError\Problem;

final class GenericErrorFormatter extends AbstractErrorFormatter
{
    /**
     * @var array<string, mixed>
     */
    private array $format = [
        'message' => '{title}',
        'status' => '{status_code}',
        'code' => '{code}',
    ];

    protected function createProblem(\Throwable $exception): Problem
    {
        return GenericProblem::createFromThrowable($exception);
    }

    protected function getFormat(): array
    {
        $format = $this->format;

        if ($this->debug) {
            $format['debug'] = '{debug}';
        }

        return $format;
    }

    /**
     * @param  array<string, mixed> $format
     */
    public function setFormat(array $format): void
    {
        $this->format = $format;
    }
}
