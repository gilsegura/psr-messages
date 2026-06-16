<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\OAuth;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Headers\AuthorizationScheme;
use Psr\Messages\Headers\BasicCredentials;
use Psr\Messages\Headers\DecodesBasicCredentialsTrait;
use Psr\Messages\Headers\ParsesAuthorizationHeaderTrait;
use Psr\Messages\Schema\SchemaInterface;

/**
 * Builds BasicCredentials from the Authorization header of a token request.
 * The header format is validated upstream by JSON Schema; this schema extracts
 * and base64-decodes the credentials using the shared traits.
 *
 * @implements SchemaInterface<array{authorization: string}>
 */
final readonly class BasicAuthCredentials implements SchemaInterface
{
    use ParsesAuthorizationHeaderTrait;
    use DecodesBasicCredentialsTrait;

    public function __construct(
        public BasicCredentials $credentials,
    ) {
    }

    #[\Override]
    public static function supports(array $data): bool
    {
        $authorization = $data['authorization'] ?? null;

        return \is_string($authorization) && str_starts_with($authorization, 'Basic ');
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        $authorization = \is_string($attributes['authorization'] ?? null) ? $attributes['authorization'] : '';

        $encoded = self::authorizationCredentialsFor($authorization, AuthorizationScheme::BASIC);
        [$username, $password] = self::decodeBasic($encoded);

        return new self(new BasicCredentials($username, $password));
    }

    /**
     * @throws UnsupportedSerializationException always; a credentials schema is input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A basic auth credentials schema');
    }
}
