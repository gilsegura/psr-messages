<?php

declare(strict_types=1);

namespace Psr\Messages\Headers;

use Psr\Messages\Exception\UnexpectedStateException;

/**
 * Decodes the base64 credentials of a Basic Authorization header into username
 * and password.
 *
 * The header format is validated upstream by the headers schema, so a valid
 * Basic value is assumed here. If decoding fails despite that, it is an
 * impossible state, so a UnexpectedStateException is thrown rather than a
 * validation error.
 *
 * @throws UnexpectedStateException
 */
trait DecodesBasicCredentialsTrait
{
    /**
     * @return array{0: string, 1: string} username and password
     */
    private static function decodeBasic(string $credentials): array
    {
        $decoded = base64_decode($credentials, true);

        if (
            false === $decoded
            || !str_contains($decoded, ':')
        ) {
            throw UnexpectedStateException::reason('the Basic credentials are not decodable.');
        }

        /** @var array{0: string, 1: string} $parts */
        $parts = explode(':', $decoded, 2);

        return $parts;
    }
}
