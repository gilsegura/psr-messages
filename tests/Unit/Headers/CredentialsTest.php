<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Headers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Headers\AuthorizationScheme;
use Psr\Messages\Headers\BasicCredentials;
use Psr\Messages\Headers\BearerToken;

final class CredentialsTest extends TestCase
{
    #[Test]
    public function basic_credentials_expose_username_and_password(): void
    {
        $credentials = new BasicCredentials('alice', 's3cr3t');

        self::assertSame('alice', $credentials->username);
        self::assertSame('s3cr3t', $credentials->password);
    }

    #[Test]
    public function basic_credentials_report_the_basic_scheme(): void
    {
        $credentials = new BasicCredentials('alice', 's3cr3t');

        self::assertSame(AuthorizationScheme::BASIC, $credentials->scheme());
    }

    #[Test]
    public function bearer_token_exposes_its_token_and_scheme(): void
    {
        $bearer = new BearerToken('opaque-token');

        self::assertSame('opaque-token', $bearer->token);
        self::assertSame(AuthorizationScheme::BEARER, $bearer->scheme());
    }

    #[Test]
    public function the_scheme_values_match_the_http_tokens(): void
    {
        self::assertSame('Basic', AuthorizationScheme::BASIC->value);
        self::assertSame('Bearer', AuthorizationScheme::BEARER->value);
    }
}
