<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Query\Definition;

/**
 * A sparse fieldset for a single resource type: the type and the list of field
 * names requested for it, e.g. type "articles" with ["title", "body"].
 */
final readonly class Field
{
    /** @var string[] */
    public array $fields;

    public function __construct(
        public string $type,
        string ...$fields,
    ) {
        $this->fields = $fields;
    }
}
