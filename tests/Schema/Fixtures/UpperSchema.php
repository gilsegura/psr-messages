<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Schema\Fixtures;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Schema\SchemaInterface;

/**
 * @implements SchemaInterface<array{kind: string, value: string}>
 */
final readonly class UpperSchema implements SchemaInterface
{
    public function __construct(public string $value)
    {
    }

    #[\Override]
    public static function supports(array $data): bool
    {
        return 'upper' === $data['kind'];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        return new self(strtoupper($attributes['value']));
    }

    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('Upper schema');
    }
}
