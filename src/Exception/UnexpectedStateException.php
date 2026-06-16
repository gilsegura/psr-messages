<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

/**
 * Thrown when a precondition guaranteed by an earlier validation step does not
 * hold (an impossible state). For example, an Authorization header that passed
 * the headers schema but does not carry the expected scheme, or Basic
 * credentials that are not decodable. This is a bug or a skipped validation,
 * not bad user input.
 */
final class UnexpectedStateException extends \LogicException implements LogicExceptionInterface
{
    public static function reason(string $reason): self
    {
        return new self(\sprintf('Unexpected state after validation: %s', $reason));
    }
}
