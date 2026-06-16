<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

/**
 * Thrown when a link href is not a valid URL. Links are produced by the
 * application, so an invalid href is a programming error, not bad input.
 */
final class InvalidHrefException extends \LogicException implements LogicExceptionInterface
{
    public static function forValue(string $value): self
    {
        return new self(\sprintf('"%s" is not a valid link href.', $value));
    }
}
