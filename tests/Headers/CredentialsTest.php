<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Headers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Headers\AuthorizationScheme;
use Psr\Messages\Headers\BasicCredentials;
use Psr\Messages\Headers\BearerToken;
use Psr\Messages\Tests\Headers\Fixtures\BasicHeaders;
use Psr\Messages\Tests\Headers\Fixtures\BearerHeaders;

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

    #[Test]
    public function a_bearer_headers_schema_parses_the_authorization_header(): void
    {
        $headers = BearerHeaders::deserialize(['authorization' => 'Bearer opaque-token']);

        self::assertSame('opaque-token', $headers->bearer->token);
    }

    #[Test]
    public function a_basic_headers_schema_decodes_username_and_password(): void
    {
        $headers = BasicHeaders::deserialize(['authorization' => 'Basic '.base64_encode('alice:s3cr3t')]);

        self::assertSame('alice', $headers->credentials->username);
        self::assertSame('s3cr3t', $headers->credentials->password);
    }

    #[Test]
    public function a_bearer_headers_schema_rejects_a_mismatched_scheme(): void
    {
        $this->expectException(UnexpectedStateException::class);

        BearerHeaders::deserialize(['authorization' => 'Basic '.base64_encode('alice:s3cr3t')]);
    }
}
