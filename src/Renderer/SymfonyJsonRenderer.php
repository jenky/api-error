<?php

declare(strict_types=1);

namespace Jenky\ApiError\Renderer;

use Jenky\ApiError\DebuggableProblem;
use Jenky\ApiError\HttpProblem;
use Jenky\ApiError\Problem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (! class_exists(Request::class) || ! class_exists(Response::class)) {
    throw new \LogicException('You cannot use the "Jenky\ApiError\Renderer\SymfonyJsonRenderer" as the "symfony/http-foundation" package is not installed. Try running "composer require symfony/http-foundation".');
}

final class SymfonyJsonRenderer
{
    public function __construct(
        // Enable debug mode will append the debug param to the JSON response.
        private readonly bool $debug = false,
        private readonly string $contentType = 'application/problem+json',
    ) {
    }

    /**
     * Render a HTTP response for given problem.
     */
    public function render(Problem $problem, Request $request): ?Response
    {
        if (! $this->expectsJson($request)) {
            return null;
        }

        $data = $this->debug && $problem instanceof DebuggableProblem
            ? $problem->toDebugRepresentation()
            : $problem->toRepresentation();

        $status = 500;
        $headers = [];

        if ($problem instanceof HttpProblem) {
            $status = $problem->getStatusCode();
            $headers = $problem->getHeaders();
        }

        return new JsonResponse(
            $data,
            $status,
            \array_merge(['Content-Type' => $this->contentType], $headers)
        );
    }

    /**
     * Determine whether the client is asking for JSON response.
     */
    private function expectsJson(Request $request): bool
    {
        $acceptable = $request->getAcceptableContentTypes();

        $acceptsAnyContentType = 0 === \count($acceptable) || (
            isset($acceptable[0]) && ('*/*' === $acceptable[0] || '*' === $acceptable[0])
        );

        return ($request->isXmlHttpRequest() && $acceptsAnyContentType)
            || str_contains($acceptable[0] ?? '', 'json')
            || str_contains($request->getPreferredFormat() ?? '', 'json')
            || str_contains($request->getContentTypeFormat() ?? '', 'json');
    }
}
