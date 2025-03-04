<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

trait PlaceholderTrait
{
    /**
     * @param  array<string, mixed> $format
     * @param  array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    private function replacePlaceholders(array $format, array $context): array
    {
        \array_walk_recursive($format, $this->replacePlaceholder(...), $context);

        return $this->removeEmptyPlaceholders($format);
    }

    /**
     * @param  array<string, mixed> $context
     */
    private function replacePlaceholder(mixed &$value, string $key, array $context): void
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
     * @return array<string, mixed>
     */
    private function removeEmptyPlaceholders(array $input): array
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = $this->removeEmptyPlaceholders($value);
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
