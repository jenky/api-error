<?php

declare(strict_types=1);

namespace Jenky\ApiError\Handler\Symfony;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ResponseHandler
{
    /**
     * Render a HTTP response for given exception.
     */
    public function render(\Throwable $exception, Request $request): ?Response;
}
