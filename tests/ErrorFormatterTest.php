<?php

declare(strict_types=1);

namespace Jenky\ApiError\Tests;

use Jenky\ApiError\Formatter\GenericErrorFormatter;
use Jenky\ApiError\Formatter\Rfc7807ErrorFormatter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ErrorFormatterTest extends TestCase
{
    #[DataProvider('provideExceptions')]
    public function test_generic_error_formatter(\Throwable $exception, bool $debug): void
    {
        $formatter = new GenericErrorFormatter($debug);

        $data = $formatter->format($exception);

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
        $formatter = new Rfc7807ErrorFormatter($debug);

        $data = $formatter->format($exception);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('detail', $data);
        $this->assertArrayHasKey('status', $data);

        if ($debug) {
            $this->assertArrayHasKey('debug', $data);
        } else {
            $this->assertArrayNotHasKey('debug', $data);
        }

        $this->assertSame($data['type'], 'about:blank');
        $this->assertSame($data['title'], 'Internal Server Error');
        $this->assertSame($data['detail'], $exception->getMessage());
        $this->assertSame($data['status'], 500);
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
