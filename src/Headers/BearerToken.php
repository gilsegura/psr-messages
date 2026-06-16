<?php

declare(strict_types=1);

namespace Psr\Messages\Headers;

/**
 * Bearer credentials: the token carried in an "Authorization: Bearer ..." header.
 *
 * A value object: it holds the already-parsed token. Building it from a request
 * is the schema's job (see the README for how to assemble a Bearer headers
 * schema with ParsesAuthorizationHeaderTrait). Verifying the token is the
 * authentication layer's job, not this library's.
 */
final readonly class BearerToken implements CredentialsInterface
{
    public function __construct(
        public string $token,
    ) {
    }

    #[\Override]
    public function scheme(): AuthorizationScheme
    {
        return AuthorizationScheme::BEARER;
    }
}
