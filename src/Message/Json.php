<?php

declare(strict_types=1);

namespace Psr\Messages\Message;

use Psr\Messages\Exception\MalformedContentException;

/**
 * JSON helpers: decode a string to an array or to nested objects, and convert
 * an array to nested objects. The array form feeds schema resolution; the
 * object form feeds JSON Schema validators, which require objects at every
 * level.
 */
final class Json
{
    /**
     * @return array<array-key, mixed>
     *
     * @throws MalformedContentException
     */
    public static function toArray(string $json): array
    {
        if ('' === $json) {
            return [];
        }

        try {
            /** @var array<array-key, mixed> $decoded */
            $decoded = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);

            return $decoded;
        } catch (\JsonException $e) {
            throw MalformedContentException::fromThrowable($e);
        }
    }

    /**
     * @throws MalformedContentException
     */
    public static function toObject(string $json): object
    {
        if ('' === $json) {
            return new \stdClass();
        }

        try {
            $decoded = json_decode($json, false, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw MalformedContentException::fromThrowable($e);
        }

        if (!\is_object($decoded)) {
            return new \stdClass();
        }

        return $decoded;
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @throws MalformedContentException
     */
    public static function objectFromArray(array $data): object
    {
        try {
            $json = json_encode($data, \JSON_THROW_ON_ERROR);
            $object = json_decode($json, false, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw MalformedContentException::fromThrowable($e);
        }

        if (!\is_object($object)) {
            return new \stdClass();
        }

        return $object;
    }
}
