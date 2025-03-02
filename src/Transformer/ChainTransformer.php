<?php

declare(strict_types=1);

namespace Jenky\ApiError\Transformer;

use Jenky\ApiError\Exception\TransformerException;
use Jenky\ApiError\Problem;

final class ChainTransformer implements ExceptionTransformer
{
    /**
     * @param iterable<ExceptionTransformer> $transformers
     */
    public function __construct(
        private readonly iterable $transformers
    ) {
    }

    public function transform(\Throwable $exception): Problem
    {
        foreach ($this->transformers as $transformer) {
            try {
                return $transformer->transform($exception);
            } catch (\Throwable) {
                continue;
            }
        }

        throw new TransformerException(sprintf('Unable to transform given exception %s', get_debug_type($exception)));
    }
}
