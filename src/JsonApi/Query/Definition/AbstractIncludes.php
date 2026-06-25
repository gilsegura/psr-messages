<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\JsonApi\Document\Definition\RelationshipNameInterface;
use Psr\Messages\JsonApi\Include\IncludeInterface;
use Psr\Messages\Support\PathNavigator;
use Serializer\SerializableInterface;

/**
 * Base for JSON:API inclusion of related resources: a comma-separated list of
 * relationship paths, e.g. "include=author,comments.author". This base owns the
 * fixed JSON:API format (how the parameter is split into paths) and the queries
 * over the parsed paths; the concrete subclass in the consuming library names
 * the resource's allowed relationships by typing itself. Which relationships are
 * permitted is enforced by the endpoint's JSON Schema before parsing, so the
 * subclass only needs to type the result.
 *
 * @implements SerializableInterface<array{include?: string}>
 */
abstract readonly class AbstractIncludes implements SerializableInterface
{
    /** @var Path[] */
    public array $paths;

    final public function __construct(Path ...$paths)
    {
        $this->paths = $paths;
    }

    /**
     * Whether a relationship was requested at the top level of any include path,
     * e.g. has(ArticleRelationship::COMMENTS) matches both "comments" and the
     * nested "comments.author".
     */
    final public function has(RelationshipNameInterface $name): bool
    {
        return array_any($this->paths, static fn (Path $candidate): bool => $candidate->head() === $name->value);
    }

    /**
     * The requested relationship paths as strings.
     *
     * @return string[]
     */
    final public function names(): array
    {
        return array_map(static fn (Path $path): string => $path->value, $this->paths);
    }

    /**
     * The include path requested for a relationship at the top level, or null
     * when it was not requested. Useful to resolve a nested path level by level:
     * forName(ArticleRelationship::COMMENTS) returns the "comments.author" path,
     * whose tail() drives the next level.
     */
    final public function forName(RelationshipNameInterface $name): ?Path
    {
        return array_find(
            $this->paths,
            static fn (Path $candidate): bool => $candidate->head() === $name->value,
        );
    }

    /**
     * Whether any relationship was requested.
     */
    final public function isEmpty(): bool
    {
        return [] === $this->paths;
    }

    /**
     * Selects, from the available includes, those requested at the top level of
     * any include path. The pure counterpart of Fields::apply() on the include
     * side: it matches names without loading anything, so the caller resolves
     * only the includes that were actually asked for. Order follows availability.
     *
     * @template TPrimary of object
     *
     * @param IncludeInterface<TPrimary> ...$available
     *
     * @return IncludeInterface<TPrimary>[]
     */
    final public function select(IncludeInterface ...$available): array
    {
        $heads = array_fill_keys(
            array_map(
                static fn (Path $path) => $path->head(),
                $this->paths,
            ),
            true,
        );

        return array_values(
            array_filter(
                $available,
                static fn (IncludeInterface $include): bool => isset($heads[$include->name()->value]),
            )
        );
    }

    /**
     * Parses the standard JSON:API "include=a,b,c" parameter into paths. The
     * format is fixed here; the subclass decides how to type the result.
     *
     * @param array<array-key, mixed> $attributes
     *
     * @return Path[]
     */
    final protected static function parse(array $attributes): array
    {
        if (!isset($attributes['include']) || !\is_string($attributes['include'])) {
            return [];
        }

        return array_map(
            static fn (string $path): Path => new Path($path),
            PathNavigator::segments($attributes['include'], ','),
        );
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    #[\Override]
    abstract public static function deserialize(array $attributes): static;

    /**
     * @return array{include?: string}
     *
     * @throws UnsupportedSerializationException always; query value objects are input only
     */
    #[\Override]
    final public function serialize(): array
    {
        throw UnsupportedSerializationException::for('An includes value object');
    }
}
