<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Exception\UnsupportedSerializationException;
use Serializer\SerializableInterface;

/**
 * JSON:API pagination: page[number] and page[size].
 *
 * @implements SerializableInterface<array{page?: array{number?: string, size?: string}}>
 */
final readonly class Page implements SerializableInterface
{
    public function __construct(
        public int $number,
        public int $size,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        if (
            !isset($attributes['page'])
            || !\is_array($attributes['page'])
        ) {
            throw UnexpectedStateException::reason('the page query parameter must be an array.');
        }

        ['number' => $number, 'size' => $size] = $attributes['page'] + ['number' => '1', 'size' => '20'];

        if (
            !\is_numeric($number)
            || !\is_numeric($size)
        ) {
            throw UnexpectedStateException::reason('page[number] and page[size] must be numeric.');
        }

        return new self(
            (int) $number,
            (int) $size
        );
    }

    /**
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A page value object');
    }
}
