<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\OAuth;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * An OAuth2 access token response payload (RFC 6749 §5.1), serialized as the
 * body of a plain JSON document. Output only.
 *
 * @implements SerializableInterface<array{access_token: string, token_type: string, expires_in: int, scope: string}>
 */
final readonly class AccessToken implements SerializableInterface
{
    public function __construct(
        public string $accessToken,
        public int $expiresIn,
        public string $scope,
        public string $tokenType = 'Bearer',
    ) {
    }

    /**
     * @return array{access_token: string, token_type: string, expires_in: int, scope: string}
     */
    #[\Override]
    public function serialize(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'expires_in' => $this->expiresIn,
            'scope' => $this->scope,
        ];
    }

    /**
     * @throws UnsupportedDeserializationException always; output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('An access token');
    }
}
