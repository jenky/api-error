<?php

declare(strict_types=1);

namespace Jenky\ApiError;

use Symfony\Component\ErrorHandler\Exception\FlattenException;

class GenericProblem extends FlattenException implements DebuggableProblem
{
    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'message' => $this->getMessage(),
            'title' => $this->getMessage() ?: $this->getStatusText(),
            'code' => $this->getCode(),
            'status_code' => $this->getStatusCode(),
            'status_text' => $this->getStatusText(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function debugContext(): array
    {
        $data = $this->context();

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
