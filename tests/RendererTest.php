<?php

declare(strict_types=1);

namespace Jenky\ApiError\Tests;

use Jenky\ApiError\GenericProblem;
use Jenky\ApiError\Renderer\SymfonyJsonRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class RendererTest extends TestCase
{
    public function test_symfony_json_renderer(): void
    {
        $request = Request::create('/', server: ['HTTP_ACCEPT' => 'application/json']);
        $renderer = new SymfonyJsonRenderer();

        $response = $renderer->render(new GenericProblem(new \RuntimeException('noop')), $request);
        $error = [
            'message' => 'noop',
            'status' => 500,
        ];

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJsonStringNotEqualsJsonString(\json_encode($error), $response->getContent());
        $this->assertSame('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_symfony_json_renderer_with_debug(): void
    {
        $request = Request::create('/', server: ['HTTP_ACCEPT' => 'application/json']);
        $renderer = new SymfonyJsonRenderer(true, 'application/debug_problem+json');

        $response = $renderer->render(new GenericProblem(new \RuntimeException('noop')), $request);
        $error = [
            'message' => 'noop',
            'status' => 500,
            'debug' => [
                'file' => \RuntimeException::class,
            ],
        ];

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJsonStringNotEqualsJsonString(\json_encode($error), $response->getContent());
        $this->assertSame('application/debug_problem+json', $response->headers->get('Content-Type'));
    }

    public function test_symfony_json_renderer_empty_response(): void
    {
        $request = Request::create('/');
        $renderer = new SymfonyJsonRenderer();

        $response = $renderer->render(new GenericProblem(new \RuntimeException('noop')), $request);

        $this->assertNull($response);
    }
}
