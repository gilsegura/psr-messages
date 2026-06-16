<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnexpectedStateException;
use Psr\Messages\Exception\UnsupportedSerializationException;
use Serializer\SerializableInterface;

/**
 * JSON:API inclusion of related resources: a comma-separated list of
 * relationship paths, e.g. "include=author,comments.author".
 *
 * @implements SerializableInterface<array{include?: string}>
 */
final readonly class Includes implements SerializableInterface
{
    /** @var Path[] */
    public array $paths;

    public function __construct(Path ...$paths)
    {
        $this->paths = $paths;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        if (!isset($attributes['include'])) {
            return new self();
        }

        if (!\is_string($attributes['include']) || '' === $attributes['include']) {
            throw UnexpectedStateException::reason('the include query parameter must be a non-empty string.');
        }

        $paths = array_values(array_filter(
            array_map(trim(...), explode(',', $attributes['include'])),
            static fn (string $path): bool => '' !== $path,
        ));

        return new self(...array_map(static fn (string $path): Path => new Path($path), $paths));
    }

    /**
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('An includes value object');
    }
}
