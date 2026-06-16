<?php

declare(strict_types=1);

namespace Psr\Messages\Headers;

/**
 * Credentials carried in an Authorization header, regardless of scheme
 * (Bearer, Basic, ...). Implementations parse their own scheme's format.
 */
interface CredentialsInterface
{
    /**
     * The authorization scheme, e.g. "Bearer" or "Basic".
     */
    public function scheme(): AuthorizationScheme;
}
