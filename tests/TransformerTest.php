<?php

declare(strict_types=1);

namespace Jenky\ApiError\Tests;

use Jenky\ApiError\GenericProblem;
use Jenky\ApiError\Rfc7807Problem;
use Jenky\ApiError\Transformer\ChainTransformer;
use Jenky\ApiError\Transformer\ExceptionTransformer;
use PHPUnit\Framework\TestCase;

final class TransformerTest extends TestCase
{
    public function test_chain_transformer(): void
    {
        $exception = new \RuntimeException('TEST');

        $transformer = $this->createMock(ExceptionTransformer::class);
        $transformer->expects($this->once())
            ->method('transform')
            ->with($exception)
            ->willReturn(new Rfc7807Problem($exception));

        $chain = new ChainTransformer([$transformer]);

        $this->assertInstanceOf(Rfc7807Problem::class, $problem = $chain->transform($exception));
        $this->assertSame(500, $problem->statusCode());
    }

    public function test_chain_transformer_fallback(): void
    {
        $exception = new \RuntimeException('TEST');

        $transformer = $this->createMock(ExceptionTransformer::class);
        $transformer->expects($this->once())
            ->method('transform')
            ->with($exception)
            ->willThrowException(new \Exception('noop'));

        $chain = new ChainTransformer([$transformer]);

        $this->assertInstanceOf(GenericProblem::class, $chain->transform($exception));
    }
}
