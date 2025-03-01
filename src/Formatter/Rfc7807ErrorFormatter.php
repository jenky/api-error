<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

/**
 * @extends AbstractErrorFormatter<string>
 */
final class Rfc7807ErrorFormatter extends AbstractErrorFormatter
{
    public function __construct(bool $debug = false)
    {
        $format = [
            'type' => '{type}',
            'title' => '{status_text}',
            'detail' => '{message}',
            'status' => '{status_code}',
            'invalid-params' => '{invalid_params}',
        ];

        if ($debug) {
            $format['debug'] = '{debug}';
        }

        parent::__construct($format);
    }
}
