<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

use Psr\Messages\JsonApi\Document\Definition\ResourceTypeInterface;

/**
 * A sparse fieldset for a single resource type: the type and the list of field
 * names requested for it, e.g. type "articles" with ["title", "body"].
 */
final readonly class Field
{
    /** @var string[] */
    public array $fields;

    /** @var array<string, true> the field names as a lookup, built once for reuse */
    private array $lookup;

    public function __construct(
        public ResourceTypeInterface $type,
        string ...$fields,
    ) {
        $this->fields = $fields;
        $this->lookup = array_fill_keys($fields, true);
    }

    /**
     * Whether the given field name was requested in this fieldset.
     */
    public function has(string $field): bool
    {
        return isset($this->lookup[$field]);
    }

    /**
     * Keeps only the requested fields from an attributes map. The field lookup is
     * built once on construction, so applying the same fieldset across a whole
     * collection does not re-flip the field list per resource.
     *
     * @param array<string, mixed> $attributes
     *
     * @return array<string, mixed>
     */
    public function keep(array $attributes): array
    {
        return array_intersect_key($attributes, $this->lookup);
    }
}