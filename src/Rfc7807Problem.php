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
        if (\filter_var($type, \FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException(sprintf('Type should be a valid URL. `%s` given', $type));
        }

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
    public function context(): array
    {
        $context = [
            'type' => $this->type,
        ];

        if (\count($this->invalidParams) > 0) {
            $context['invalid_params'] = $this->invalidParams;
        }

        if ($message = $this->getMessage()) {
            $context['detail'] = $message;
        }

        return \array_merge(parent::context(), $context);
    }
}
