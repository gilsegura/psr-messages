<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

final class MalformedContentException extends \RuntimeException implements MediaTypeExceptionInterface
{
    public static function malformed(string $reason): self
    {
        return new self(\sprintf('Malformed content: %s', $reason));
    }

    public static function fromThrowable(\Throwable $previous): self
    {
        return new self('Malformed content.', previous: $previous);
    }
}
