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

    public function __construct(
        public ResourceTypeInterface $type,
        string ...$fields,
    ) {
        $this->fields = $fields;
    }

    /**
     * Whether the given field name was requested in this fieldset.
     */
    public function has(string $field): bool
    {
        return \in_array($field, $this->fields, true);
    }
}
