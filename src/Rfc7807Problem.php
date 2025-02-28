<?php

declare(strict_types=1);

namespace Jenky\ApiError;

final class Rfc7807Problem extends GenericProblem
{
    private string $type = 'about:blank';

    /**
     * @var list<array{name: string, reason: string}>
     */
    private array $invalidParams = [];

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param list<array{name: string, reason: string}> $invalidParams
     */
    public function setInvalidParams(array $invalidParams): static
    {
        foreach ($invalidParams as $invalidParam) {
            $this->addInvalidParam(...$invalidParam);
        }

        return $this;
    }

    public function addInvalidParam(string $name, string $reason): static
    {
        $this->invalidParams[] = compact('name', 'reason');

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'title' => $this->e->getStatusText(),
            'detail' => $this->e->getMessage(),
            'status' => $this->e->getStatusCode(),
            'invalid-params' => $this->invalidParams,
        ]);
    }
}
