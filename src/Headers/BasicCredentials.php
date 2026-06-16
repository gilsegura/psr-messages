<?php

declare(strict_types=1);

namespace Psr\Messages\Headers;

/**
 * Basic credentials: the username and password from an "Authorization: Basic
 * ..." header.
 *
 * A value object: it holds the already-decoded username and password. Building
 * it from a request (extracting and base64-decoding) is the schema's job (see
 * the README). Verifying the credentials is the authentication layer's job.
 */
final readonly class BasicCredentials implements CredentialsInterface
{
    public function __construct(
        public string $username,
        public string $password,
    ) {
    }

    #[\Override]
    public function scheme(): AuthorizationScheme
    {
        return AuthorizationScheme::BASIC;
    }
}
