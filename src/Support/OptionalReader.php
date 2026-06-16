<?php

declare(strict_types=1);

namespace Psr\Messages\Support;

use Psr\Messages\Exception\UnexpectedStateException;

/**
 * Reads optional fields from an array whose values are mixed (typically the
 * decoded body of a partial update). A field that is absent yields an absent
 * Optional; a field that is present is returned as a present Optional, with its
 * type checked. Because the structure was validated upstream, a present value
 * of the wrong type is an impossible state and throws rather than being coerced.
 */
final class OptionalReader
{
    private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return Optional<string>
     *
     * @throws UnexpectedStateException
     */
    public static function string(array $data, string $key): Optional
    {
        if (!\array_key_exists($key, $data)) {
            return Optional::absent();
        }

        $value = $data[$key];

        if (!\is_string($value)) {
            throw UnexpectedStateException::reason(\sprintf('the %s field must be a string after validation.', $key));
        }

        return Optional::of($value);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return Optional<int>
     *
     * @throws UnexpectedStateException
     */
    public static function int(array $data, string $key): Optional
    {
        if (!\array_key_exists($key, $data)) {
            return Optional::absent();
        }

        $value = $data[$key];

        if (!\is_int($value)) {
            throw UnexpectedStateException::reason(\sprintf('the %s field must be an integer after validation.', $key));
        }

        return Optional::of($value);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return Optional<bool>
     *
     * @throws UnexpectedStateException
     */
    public static function bool(array $data, string $key): Optional
    {
        if (!\array_key_exists($key, $data)) {
            return Optional::absent();
        }

        $value = $data[$key];

        if (!\is_bool($value)) {
            throw UnexpectedStateException::reason(\sprintf('the %s field must be a boolean after validation.', $key));
        }

        return Optional::of($value);
    }

    /**
     * Reads a nested array (e.g. a relationship object) for further reading. An
     * absent or non-array value yields an empty array.
     *
     * @param array<string, mixed> $data
     *
     * @return array<array-key, mixed>
     *
     * @throws UnexpectedStateException
     */
    public static function nested(array $data, string $key): array
    {
        if (!isset($data[$key])) {
            return [];
        }

        if (!\is_array($data[$key])) {
            throw UnexpectedStateException::reason(\sprintf('the %s field must be a present object after validation.', $key));
        }

        return $data[$key];
    }
}
