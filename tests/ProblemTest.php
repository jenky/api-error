<?php

declare(strict_types=1);

namespace Jenky\ApiError\Tests;

use Jenky\ApiError\GenericProblem;
use Jenky\ApiError\Rfc7807Problem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

final class ProblemTest extends TestCase
{
    #[DataProvider('provideGenericProblemExceptions')]
    public function test_generic_problem(\Throwable $e, int $status, array $data): void
    {
        $problem = GenericProblem::createFromThrowable($e);

        $this->assertSame($status, $problem->getStatusCode());
        $this->assertEquals($data, $problem->toRepresentation());
        $this->assertArrayHasKey('debug', $problem->toDebugRepresentation());
    }

    public static function provideGenericProblemExceptions(): iterable
    {
        yield [
            new \RuntimeException('This is a runtime exception'),
            500,
            [
                'message' => 'This is a runtime exception',
                'status' => 500,
                'code' => 0,
            ],
        ];

        yield [
            new \LogicException(),
            500,
            [
                'message' => Response::$statusTexts[500],
                'status' => 500,
                'code' => 0,
            ],
        ];

        yield [
            new BadRequestException(),
            400,
            [
                'message' => Response::$statusTexts[400],
                'status' => 400,
                'code' => 0,
            ],
        ];
    }

    #[DataProvider('provideRfc7807ProblemExceptions')]
    public function test_rfc7870_problem(\Throwable $e, int $status, array $data): void
    {
        $problem = Rfc7807Problem::createFromThrowable($e);

        $this->assertSame($status, $problem->getStatusCode());
        $this->assertEquals($data, $problem->toRepresentation());
        $this->assertArrayHasKey('debug', $problem->toDebugRepresentation());
    }

    public static function provideRfc7807ProblemExceptions(): iterable
    {
        yield [
            new \RuntimeException('This is a runtime exception'),
            500,
            [
                'type' => 'about:blank',
                'title' => Response::$statusTexts[500],
                'detail' => 'This is a runtime exception',
                'status' => 500,
            ],
        ];

        yield [
            new BadRequestException(),
            400,
            [
                'type' => 'about:blank',
                'title' => Response::$statusTexts[400],
                'status' => 400,
            ],
        ];
    }
}
