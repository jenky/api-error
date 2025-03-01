<?php

declare(strict_types=1);

namespace Jenky\ApiError\Formatter;

/**
 * @extends AbstractErrorFormatter<string>
 */
final class GenericErrorFormatter extends AbstractErrorFormatter
{
    public function __construct(bool $debug = false)
    {
        $format = [
            'message' => '{title}',
            'status' => '{status_code}',
            'code' => '{code}',
        ];

        if ($debug) {
            $format['debug'] = '{debug}';
        }

        parent::__construct($format);
    }
}
