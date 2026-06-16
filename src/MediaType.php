<?php

declare(strict_types=1);

namespace Psr\Messages;

use Psr\Messages\Exception\UnsupportedMediaTypeException;

enum MediaType: string
{
    case JSON_API = 'application/vnd.api+json';
    case JSON = 'application/json';

    /**
     * @throws UnsupportedMediaTypeException
     */
    public static function fromHeaderLine(string $headerLine): self
    {
        return array_find(
            self::cases(),
            static fn (self $case): bool => str_contains($headerLine, $case->value),
        ) ?? throw UnsupportedMediaTypeException::unsupported($headerLine);
    }

    public function equals(self $mediaType): bool
    {
        return $this === $mediaType;
    }
}
