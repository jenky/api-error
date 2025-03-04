<?php

declare(strict_types=1);

namespace Jenky\ApiError\Handler\Symfony;

use Jenky\ApiError\Formatter\ErrorFormatter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (! class_exists(Request::class) || ! class_exists(Response::class)) {
    throw new \LogicException('You cannot use the "Jenky\ApiError\Handler\Symfony\JsonResponseHandler" as the "symfony/http-foundation" package is not installed. Try running "composer require symfony/http-foundation".');
}

final class JsonResponseHandler implements ResponseHandler
{
    public function __construct(
        private readonly ErrorFormatter $formatter,
        private readonly string $contentType = 'application/problem+json',
    ) {
    }

    /**
     * Render a HTTP response for given problem.
     */
    public function render(\Throwable $exception, Request $request): ?JsonResponse
    {
        if (! $this->expectsJson($request)) {
            return null;
        }

        $data = $this->formatter->format($exception);

        return new JsonResponse(
            $data,
            $data->statusCode,
            \array_merge(['Content-Type' => $this->contentType], $data->headers)
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
