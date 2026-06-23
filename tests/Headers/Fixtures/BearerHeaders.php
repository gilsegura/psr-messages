<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Headers\Fixtures;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Headers\AuthorizationScheme;
use Psr\Messages\Headers\BearerToken;
use Psr\Messages\Headers\ParsesAuthorizationHeaderTrait;
use Psr\Messages\Schema\SchemaInterface;

/**
 * A headers schema, as a consuming library would define one: it parses the
 * Authorization header into a BearerToken using ParsesAuthorizationHeaderTrait.
 *
 * @implements SchemaInterface<array{authorization: string}>
 */
final readonly class BearerHeaders implements SchemaInterface
{
    use ParsesAuthorizationHeaderTrait;

    public function __construct(
        public BearerToken $bearer,
    ) {
    }

    #[\Override]
    public static function supports(array $data): bool
    {
        return AuthorizationScheme::tryFrom($data['authorization'])?->equals(AuthorizationScheme::BEARER)
            ?? false;
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        $token = self::authorizationCredentialsFor($attributes['authorization'], AuthorizationScheme::BEARER);

        return new self(new BearerToken($token));
    }

    /**
     * @throws UnsupportedSerializationException always; a headers schema is input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A bearer headers schema');
    }
}
