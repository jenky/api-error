<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

use Jenky\ApiError\DebuggableProblem;
use Jenky\ApiError\HttpError;
use Jenky\ApiError\Problem;
use Jenky\ApiError\Transformer\ExceptionTransformer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

abstract class AbstractErrorFormatter implements ErrorFormatter
{
    use PlaceholderTrait;

    public function __construct(
        protected readonly bool $debug = false,
        protected readonly ?ExceptionTransformer $transformer = null,
    ) {
    }

    /**
     * @return array<string, mixed> $format
     */
    abstract protected function getFormat(): array;

    abstract protected function createProblem(\Throwable $exception): Problem;

    public function format(\Throwable $exception): HttpError
    {
        $format = $this->getFormat();

        if ($this->transformer !== null) {
            try {
                $problem = $this->transformer->transform($exception);
            } catch (\Throwable) {
                $problem = $this->createProblem($exception);
            }
        } else {
            $problem = $this->createProblem($exception);
        }

        return $this->createHttpError($problem);
    }

    protected function createHttpError(Problem $problem): HttpError
    {
        $context = $this->debug && $problem instanceof DebuggableProblem
            ? $problem->debugContext()
            : $problem->context();

        $status = 500;
        $headers = [];

        if ($problem instanceof FlattenException) {
            $status = $problem->getStatusCode();
            $headers = $problem->getHeaders();
        }

        return new HttpError($this->replacePlaceholders($this->getFormat(), $context), $status, $headers);
    }
}
