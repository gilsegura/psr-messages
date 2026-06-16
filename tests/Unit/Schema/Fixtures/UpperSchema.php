<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Schema\Fixtures;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Schema\SchemaInterface;

/**
 * @implements SchemaInterface<array<string, mixed>>
 */
final readonly class UpperSchema implements SchemaInterface
{
    public function __construct(public string $value)
    {
    }

    #[\Override]
    public static function supports(array $data): bool
    {
        return 'upper' === ($data['kind'] ?? null);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        $value = \is_string($attributes['value'] ?? null) ? $attributes['value'] : '';

        return new self(strtoupper($value));
    }

    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('Upper schema');
    }
}
