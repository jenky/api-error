<?php

declare(strict_types=1);

namespace Jenky\ApiError;

use Symfony\Component\ErrorHandler\Exception\FlattenException;

class GenericProblem extends FlattenException implements DebuggableProblem, HttpProblem
{
    /**
     * @return array<string, mixed>
     */
    public function toRepresentation(): array
    {
        return [
            'message' => $this->getMessage() ?: $this->getStatusText(),
            'code' => $this->getCode(),
            'status' => $this->getStatusCode(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDebugRepresentation(): array
    {
        $data = $this->toRepresentation();

        $data['debug'] = [
            'line' => $this->getLine(),
            'file' => $this->getFile(),
            'class' => $this->getClass(),
            'trace' => $this->getTrace(),
            'previous' => $this->getPrevious()?->getTrace(),
        ];

        return $data;
    }
}
