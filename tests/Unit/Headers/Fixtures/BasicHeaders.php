<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Headers\Fixtures;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Headers\AuthorizationScheme;
use Psr\Messages\Headers\BasicCredentials;
use Psr\Messages\Headers\DecodesBasicCredentialsTrait;
use Psr\Messages\Headers\ParsesAuthorizationHeaderTrait;
use Psr\Messages\Schema\SchemaInterface;

/**
 * A headers schema parsing the Authorization header into BasicCredentials,
 * combining ParsesAuthorizationHeaderTrait (to read the scheme) and
 * DecodesBasicCredentialsTrait (to decode the base64 username:password).
 *
 * @implements SchemaInterface<array{authorization?: string}>
 */
final readonly class BasicHeaders implements SchemaInterface
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
        return isset($data['authorization']);
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        $authorization = $attributes['authorization'] ?? '';

        $credentials = self::authorizationCredentialsFor($authorization, AuthorizationScheme::BASIC);

        [$username, $password] = self::decodeBasic($credentials);

        return new self(new BasicCredentials($username, $password));
    }

    /**
     * @throws UnsupportedSerializationException always; a headers schema is input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A basic headers schema');
    }
}
