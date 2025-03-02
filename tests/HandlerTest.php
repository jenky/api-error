<?php

declare(strict_types=1);

namespace Jenky\ApiError\Tests;

use Jenky\ApiError\Formatter\GenericErrorFormatter;
use Jenky\ApiError\Handler\JsonResponseHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class HandlerTest extends TestCase
{
    public function test_symfony_json_renderer(): void
    {
        $request = Request::create('/', server: ['HTTP_ACCEPT' => 'application/json']);
        $renderer = new JsonResponseHandler(new GenericErrorFormatter());

        $response = $renderer->render(new \RuntimeException('noop'), $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJson($content = $response->getContent());
        $this->assertSame('application/problem+json', $response->headers->get('Content-Type'));

        $data = \json_decode($content, true);

        $this->assertSame('noop', $data['message'] ?? null);
        $this->assertSame(500, $data['status'] ?? null);
    }

    public function test_symfony_json_renderer_with_debug(): void
    {
        $request = Request::create('/', server: ['HTTP_ACCEPT' => 'application/json']);
        $renderer = new JsonResponseHandler(new GenericErrorFormatter(true), 'application/debug_problem+json');

        $response = $renderer->render(new \RuntimeException('noop'), $request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJson($content = $response->getContent());
        $this->assertSame('application/debug_problem+json', $response->headers->get('Content-Type'));

        $data = \json_decode($content, true);

        $this->assertSame('noop', $data['message'] ?? null);
        $this->assertSame(500, $data['status'] ?? null);
        $this->assertArrayHasKey('debug', $data);
        $this->assertSame(\RuntimeException::class, $data['debug']['class'] ?? null);
    }

    public function test_symfony_json_renderer_empty_response(): void
    {
        $request = Request::create('/');
        $renderer = new JsonResponseHandler(new GenericErrorFormatter());

        $response = $renderer->render(new \RuntimeException('noop'), $request);

        $this->assertNull($response);
    }
}
