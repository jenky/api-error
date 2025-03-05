<?php

declare(strict_types=1);

namespace Jenky\ApiError\Tests;

use Jenky\ApiError\Formatter\GenericErrorFormatter;
use Jenky\ApiError\Formatter\Rfc7807ErrorFormatter;
use Jenky\ApiError\GenericProblem;
use Jenky\ApiError\Problem;
use Jenky\ApiError\Rfc7807Problem;
use Jenky\ApiError\Transformer\ExceptionTransformer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ErrorFormatterTest extends TestCase
{
    #[DataProvider('provideExceptions')]
    public function test_generic_error_formatter(\Throwable $exception, bool $debug): void
    {
        $formatter = new GenericErrorFormatter($debug);

        $data = $formatter->format($exception)->data;

        $this->assertIsArray($data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('code', $data);

        if ($debug) {
            $this->assertArrayHasKey('debug', $data);
        } else {
            $this->assertArrayNotHasKey('debug', $data);
        }

        $this->assertSame($data['message'], $exception->getMessage() ?: 'Internal Server Error');
        $this->assertSame($data['status'], 500);
        $this->assertSame($data['code'], $exception->getCode());
    }

    #[DataProvider('provideExceptions')]
    public function test_rfc7808_error_formatter(\Throwable $exception, bool $debug): void
    {
        $formatter = new Rfc7807ErrorFormatter($debug, new Rfc7807Transformer());

        $data = $formatter->format($exception)->data;

        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('invalid-params', $data);

        if ($debug) {
            $this->assertArrayHasKey('debug', $data);
        } else {
            $this->assertArrayNotHasKey('debug', $data);
        }

        if ($exception->getMessage()) {
            $this->assertArrayHasKey('detail', $data);
            $this->assertSame($data['detail'], $exception->getMessage());
        }

        $this->assertSame($data['type'], 'http://localhost');
        $this->assertSame($data['title'], 'Internal Server Error');
        $this->assertSame($data['status'], 500);
    }

    #[DataProvider('provideExceptions')]
    public function test_custom_formatter(\Throwable $exception, bool $debug): void
    {
        $formatter = new GenericErrorFormatter(transformer: new ExceptionClassTransformer());
        $formatter->setFormat([
            'message' => '{title}',
            'status' => '{status_code}',
            'class' => '{class}',
        ]);

        $data = $formatter->format($exception)->data;

        $this->assertIsArray($data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('class', $data);

        $this->assertSame($data['message'], $exception->getMessage() ?: 'Internal Server Error');
        $this->assertSame($data['status'], 500);
        $this->assertSame($data['class'], \get_class($exception));
    }

    public static function provideExceptions(): iterable
    {
        yield [new \RuntimeException(), false];
        yield [new \RuntimeException(), true];

        yield [new \LogicException('foo'), false];
        yield [new \LogicException('foo'), true];

        yield [new \InvalidArgumentException('', 100), false];
        yield [new \InvalidArgumentException('', 100), true];
    }
}

final class ExceptionClassTransformer implements ExceptionTransformer
{
    public function transform(\Throwable $exception): Problem
    {
        $problem = GenericProblem::createFromThrowable($exception);

        $problem->set('class', $problem->getClass());

        return $problem;
    }
}

final class Rfc7807Transformer implements ExceptionTransformer
{
    public function transform(\Throwable $exception): Problem
    {
        $problem = Rfc7807Problem::createFromThrowable($exception);

        $problem->setType('http://localhost')
            ->addInvalidParam('foo', 'bar')
            ->addInvalidParam('quiz', 'qux');

        return $problem;
    }
}
