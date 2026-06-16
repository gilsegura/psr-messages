<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Json\Document\JsonDocument;
use Psr\Messages\Json\JsonResponseFactory;
use Psr\Messages\Message\HeadersValidator;
use Psr\Messages\Message\SchemaValidator;
use Psr\Messages\Schema\SchemaResolver;
use Psr\Messages\Tests\Flow\Fixtures\OAuth\AccessToken;
use Psr\Messages\Tests\Flow\Fixtures\OAuth\BasicAuthCredentials;
use Psr\Messages\Tests\Flow\Fixtures\OAuth\ClientCredentialsRequest;
use Psr\Server\ResponseFactory\ResponseFactory;
use Psr\Server\ResponseFactory\Status;
use Psr\Validator\Schema\SchemaValidator as OpisSchemaValidator;
use Psr\Validator\SchemaFactory\RawFactory;

/**
 * End-to-end OAuth2 client_credentials token flow: a POST /token request with
 * HTTP Basic client authentication and a form-like JSON body, validated against
 * JSON Schemas, parsed into typed objects, handled, and rendered as a plain
 * JSON access token response.
 */
final class OAuthClientCredentialsFlowTest extends TestCase
{
    private const string HEADERS_SCHEMA = <<<'JSON'
        {
            "type": "object",
            "properties": {
                "authorization": {
                    "type": "string",
                    "pattern": "^Basic [A-Za-z0-9+/=]+$"
                }
            },
            "required": ["authorization"]
        }
        JSON;

    private const string BODY_SCHEMA = <<<'JSON'
        {
            "type": "object",
            "properties": {
                "grant_type": { "type": "string", "enum": ["client_credentials"] },
                "scope": { "type": "string" }
            },
            "required": ["grant_type"]
        }
        JSON;

    #[Test]
    public function it_issues_an_access_token_from_a_valid_client_credentials_request(): void
    {
        // --- INPUT: a real PSR-7 request, as it would arrive on the wire ---
        $clientId = 'service-worker';
        $clientSecret = 's3cr3t';
        $authorization = 'Basic '.base64_encode($clientId.':'.$clientSecret);

        $request = (new ServerRequest(
            'POST',
            'https://api.example.com/token',
            ['Content-Type' => 'application/json', 'Authorization' => $authorization],
            '{"grant_type":"client_credentials","scope":"posts:read posts:write"}',
        ));

        // --- VALIDATION: headers, then body, against their JSON Schemas ---
        $opis = new OpisSchemaValidator();

        $headersValidator = new HeadersValidator($opis, new RawFactory(self::HEADERS_SCHEMA));
        $request = $headersValidator($request);

        $bodyValidator = new SchemaValidator($opis, new RawFactory(self::BODY_SCHEMA));
        $request = $bodyValidator($request);

        // --- PARSING: typed objects from the validated input ---
        $credentialsSchema = new SchemaResolver(BasicAuthCredentials::class)->resolve([
            'authorization' => $request->getHeaderLine('Authorization'),
        ]);
        self::assertInstanceOf(BasicAuthCredentials::class, $credentialsSchema);
        self::assertSame($clientId, $credentialsSchema->credentials->username);
        self::assertSame($clientSecret, $credentialsSchema->credentials->password);

        /** @var array<string, mixed> $body */
        $body = json_decode((string) $request->getBody(), true);
        $tokenRequest = new SchemaResolver(ClientCredentialsRequest::class)->resolve($body);
        self::assertInstanceOf(ClientCredentialsRequest::class, $tokenRequest);
        self::assertSame('client_credentials', $tokenRequest->grantType);
        self::assertSame(['posts:read', 'posts:write'], $tokenRequest->scopes);

        // --- HANDLER: the application issues a token (trivial stand-in) ---
        $token = new AccessToken(
            accessToken: 'opaque-token-value',
            expiresIn: 3600,
            scope: implode(' ', $tokenRequest->scopes),
        );

        // --- OUTPUT: rendered as a plain JSON response ---
        $factory = new JsonResponseFactory($this->responseFactory());
        $response = $factory->document(new JsonDocument($token), Status::OK);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/json', $response->getHeaderLine('content-type'));

        /** @var array<string, mixed> $payload */
        $payload = json_decode((string) $response->getBody(), true);
        self::assertSame([
            'access_token' => 'opaque-token-value',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'scope' => 'posts:read posts:write',
        ], $payload);
    }

    private function responseFactory(): ResponseFactory
    {
        $psr17 = new Psr17Factory();

        return new ResponseFactory($psr17, $psr17);
    }
}
