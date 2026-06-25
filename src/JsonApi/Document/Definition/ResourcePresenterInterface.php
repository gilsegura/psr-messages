<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

use Serializer\SerializableInterface;

/**
 * Presents a serializable read model as a JSON:API resource, honoring the
 * requested sparse fieldsets. This is the fixed shape of the output side: one
 * presenter per resource type in the consuming library turns a model into a
 * ResourceInterface, so building responses is uniform across resources.
 *
 * The model is always a serializable read model (a query bus result), so the
 * presenter obtains its attributes from $model->serialize() and applies the
 * sparse fieldsets to them. Relationships are passed in by the caller (e.g. a
 * document builder that resolves includes), keeping the presenter focused on the
 * primary resource and unaware of which relationships exist.
 *
 * @template TModel of SerializableInterface<array<string, mixed>>
 */
interface ResourcePresenterInterface
{
    /**
     * @param TModel                               $model
     * @param array<string, RelationshipInterface> $relationships
     */
    public function present(SerializableInterface $model, FieldsetInterface $fields, array $relationships = []): ResourceInterface;
}
