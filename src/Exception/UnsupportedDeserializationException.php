<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

/**
 * Thrown when deserialize() is called on an output-only document that does not
 * support being reconstructed (e.g. an error document). Calling it is a misuse
 * of the API, hence a logic error.
 */
final class UnsupportedDeserializationException extends \BadMethodCallException implements LogicExceptionInterface
{
    public static function for(string $type): self
    {
        return new self(\sprintf('%s is output only and cannot be deserialized.', $type));
    }
}
