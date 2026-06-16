<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

/**
 * Thrown when source() is called on an error that has no source (e.g. a domain
 * error). Callers should check hasSource() first, so reaching this is a logic
 * error.
 */
final class MissingErrorSourceException extends \LogicException implements LogicExceptionInterface
{
    public static function create(): self
    {
        return new self('This error has no source; check hasSource() before calling source().');
    }
}
