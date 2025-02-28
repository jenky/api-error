<?php

declare(strict_types=1);

namespace Jenky\ApiError;

use Symfony\Component\ErrorHandler\Exception\FlattenException;

class GenericProblem implements DebuggableProblem, HttpProblem, \JsonSerializable
{
    protected FlattenException $e;

    public function __construct(
        protected readonly \Throwable $exception,
    ) {
        $this->e = FlattenException::createFromThrowable($exception);
    }

    public function statusCode(): int
    {
        return $this->e->getStatusCode();
    }

    public function headers(): array
    {
        return $this->e->getHeaders();
    }

    public function toArray(): array
    {
        return [
            'message' => $this->e->getMessage() ?: $this->e->getStatusText(),
            'code' => $this->e->getCode(),
            'status' => $this->e->getStatusCode(),
        ];
    }

    public function toDebugArray(): array
    {
        return [
            'line' => $this->e->getLine(),
            'file' => $this->e->getFile(),
            'class' => $this->e->getClass(),
            'trace' => $this->e->getTrace(),
            'previous' => $this->e->getPrevious()?->getTrace(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
