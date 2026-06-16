<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Support\PathNavigator;

/**
 * Where a single error points to: a source type (pointer, parameter, header)
 * and the concrete path within it (e.g. "/data/attributes/title" for a pointer,
 * "page[size]" for a parameter).
 */
final readonly class Source
{
    public function __construct(
        public SourceTypeInterface $type,
        public string $path,
    ) {
    }

    /**
     * A body source: a JSON Pointer into the request body.
     *
     * @param array<string, mixed> $error
     */
    public static function forPointer(array $error): self
    {
        return new self(SourceType::POINTER, self::field($error, 'pointer'));
    }

    /**
     * A query parameter source: the parameter name.
     *
     * @param array<string, mixed> $error
     */
    public static function forParameter(array $error): self
    {
        return new self(SourceType::PARAMETER, ltrim(self::field($error, 'property'), '/'));
    }

    /**
     * A header source: the header name.
     *
     * @param array<string, mixed> $error
     */
    public static function forHeader(array $error): self
    {
        return new self(SourceType::HEADER, ltrim(self::field($error, 'property'), '/'));
    }

    /**
     * Reads a string field from a raw validation error. The validator always
     * provides it, so a missing or non-string value is an impossible state.
     *
     * @param array<string, mixed> $error
     */
    private static function field(array $error, string $key): string
    {
        if (!isset($error[$key]) || !\is_string($error[$key])) {
            throw UnexpectedStateException::reason(\sprintf('the validation error has no string "%s".', $key));
        }

        return $error[$key];
    }

    /**
     * The path split into its segments, using the source type's separator.
     *
     * @return string[]
     */
    public function segments(): array
    {
        return PathNavigator::segments($this->path, $this->type->separator());
    }
}
