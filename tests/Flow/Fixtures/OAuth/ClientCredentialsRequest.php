<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\OAuth;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Schema\SchemaInterface;

/**
 * The OAuth2 client_credentials token request body, parsed into a typed object.
 * Structure is validated upstream by JSON Schema; here it is only typed.
 *
 * @implements SchemaInterface<array{grant_type: string, scope?: string}>
 */
final readonly class ClientCredentialsRequest implements SchemaInterface
{
    /** @var string[] */
    public array $scopes;

    public function __construct(
        public string $grantType,
        string ...$scopes,
    ) {
        $this->scopes = $scopes;
    }

    #[\Override]
    public static function supports(array $data): bool
    {
        return 'client_credentials' === ($data['grant_type'] ?? null);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        $grantType = \is_string($attributes['grant_type'] ?? null) ? $attributes['grant_type'] : '';
        $scope = \is_string($attributes['scope'] ?? null) ? $attributes['scope'] : '';

        $scopes = array_values(array_filter(
            array_map(trim(...), explode(' ', $scope)),
            static fn (string $s): bool => '' !== $s,
        ));

        return new self($grantType, ...$scopes);
    }

    /**
     * @throws UnsupportedSerializationException always; a request schema is input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A client credentials request');
    }
}
