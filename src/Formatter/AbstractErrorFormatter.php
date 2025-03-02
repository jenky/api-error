<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

use Jenky\ApiError\DebuggableProblem;
use Jenky\ApiError\Problem;
use Jenky\ApiError\Transformer\ExceptionTransformer;

/**
 * @template T of array-key
 */
abstract class AbstractErrorFormatter implements ErrorFormatter
{
    public function __construct(
        protected readonly bool $debug = false,
        protected readonly ?ExceptionTransformer $transformer = null,
    ) {
    }

    /**
     * @return array<T, mixed> $format
     */
    abstract protected function getFormat(): array;

    abstract protected function createProblem(\Throwable $exception): Problem;

    /**
     * @return array<T, mixed>
     */
    public function format(\Throwable $exception): array
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

        $context = $problem instanceof DebuggableProblem
            ? $problem->debugContext()
            : $problem->context();

        \array_walk_recursive($format, $this->replacePlaceHolder(...), $context);

        return $this->removeEmptyPlaceHolders($format);
    }

    /**
     * @param  array<string, mixed> $context
     */
    private function replacePlaceHolder(mixed &$value, string $key, array $context): void
    {
        if (! \is_string($value)) {
            return;
        }

        $placeholder = \trim($value, '{}');

        if (isset($context[$placeholder])) {
            $value = $context[$placeholder];

            return;
        }

        $cache = [];

        /** @var string */
        $value = \preg_replace_callback(
            '/{\s*([A-Za-z_\-\.0-9]+)\s*}/',
            function (array $matches) use ($context, &$cache) {
                if (isset($cache[$matches[1]])) {
                    return $cache[$matches[1]];
                }

                $result = $context[$matches[1]] ?? '<NULL>';

                $cache[$matches[1]] = $result;

                return $result;
            },
            $value
        );
    }

    /**
     * @param  array<array-key, mixed> $input
     *
     * @return array<T, mixed>
     */
    private function removeEmptyPlaceHolders(array $input): array
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->removeEmptyPlaceHolders($value);
            }
        }

        return \array_filter($input, static function ($value) {
            if (is_string($value)) {
                return ! str_contains($value, '<NULL>');
            }

            return true;
        });
    }
}
