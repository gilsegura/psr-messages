<?php

declare(strict_types=1);

namespace Psr\Messages\Support;

use Psr\Messages\Exception\UnexpectedStateException;

/**
 * Reads required fields from an array whose values are mixed (typically the
 * decoded body of a creation). Every field is guaranteed present and of the
 * right type by the upstream JSON Schema, so a missing or wrongly-typed value
 * is an impossible state and throws rather than yielding a fallback.
 */
final class RequiredReader
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedStateException
     */
    public static function string(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (!\is_string($value)) {
            throw UnexpectedStateException::reason(\sprintf('the %s field must be a present string after validation.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedStateException
     */
    public static function int(array $data, string $key): int
    {
        $value = $data[$key] ?? null;

        if (!\is_int($value)) {
            throw UnexpectedStateException::reason(\sprintf('the %s field must be a present integer after validation.', $key));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     *
     * @throws UnexpectedStateException
     */
    public static function nested(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        if (!\is_array($value)) {
            throw UnexpectedStateException::reason(\sprintf('the %s field must be a present object after validation.', $key));
        }

        /* @var array<string, mixed> $value */
        return $value;
    }
}
