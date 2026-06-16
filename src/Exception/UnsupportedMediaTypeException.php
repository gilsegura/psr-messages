<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

final class UnsupportedMediaTypeException extends \RuntimeException implements MediaTypeExceptionInterface
{
    public static function unsupported(string $mediaType): self
    {
        return new self(\sprintf('Unsupported media type "%s".', $mediaType));
    }
}
