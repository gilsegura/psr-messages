<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

use Psr\Messages\Exception\InvalidHrefException;

/**
 * A link target URL. Validated on construction: links are produced by the
 * application, so an invalid href is a programming error.
 */
final readonly class Href
{
    public function __construct(
        public string $value,
    ) {
        if (false === filter_var($value, \FILTER_VALIDATE_URL)) {
            throw InvalidHrefException::forValue($value);
        }
    }
}
