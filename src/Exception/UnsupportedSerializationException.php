<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

/**
 * Thrown when serialize() is called on an input-only value object that is not
 * meant to be serialized (e.g. a query value object). Calling it is a misuse of
 * the API, hence a logic error.
 */
final class UnsupportedSerializationException extends \BadMethodCallException implements LogicExceptionInterface
{
    public static function for(string $type): self
    {
        return new self(\sprintf('%s is input only and cannot be serialized.', $type));
    }
}
