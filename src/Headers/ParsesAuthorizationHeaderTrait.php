<?php

declare(strict_types=1);

namespace Psr\Messages\Headers;

use Psr\Messages\Exception\UnexpectedStateException;

/**
 * Extracts the credentials of an Authorization header value for an expected
 * scheme.
 *
 * The header format is validated upstream by the headers schema, so a valid
 * value carrying the expected scheme is assumed here. If that does not hold
 * (empty, malformed, or a different scheme), it is an impossible state, so a
 * UnexpectedStateException is thrown rather than returning an empty string.
 *
 * @throws UnexpectedStateException
 */
trait ParsesAuthorizationHeaderTrait
{
    private static function authorizationCredentialsFor(string $authorization, AuthorizationScheme $expected): string
    {
        if ('' === $authorization) {
            throw UnexpectedStateException::reason('the Authorization header is empty.');
        }

        if (1 !== preg_match('#^(?<scheme>\S+)\s+(?<credentials>.+)$#', $authorization, $matches)) {
            throw UnexpectedStateException::reason('the Authorization header is malformed.');
        }

        if ($expected !== AuthorizationScheme::tryFrom($matches['scheme'])) {
            throw UnexpectedStateException::reason(\sprintf('the Authorization header does not carry the expected %s scheme.', $expected->value));
        }

        return $matches['credentials'];
    }
}
