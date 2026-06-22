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
     * The zero-based offset of the first record on this page, derived from the
     * page number and size, e.g. page 3 of size 20 starts at offset 40. Keeps the
     * offset arithmetic in the value object that owns number and size.
     */
    public function offset(): int
    {
        return ($this->number - 1) * $this->size;
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        $page = $attributes['page'] ?? [];

        if (!\is_array($page)) {
            throw UnexpectedStateException::reason('the page query parameter must be an array.');
        }

        ['number' => $number, 'size' => $size] = $page + ['number' => '1', 'size' => '20'];

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
