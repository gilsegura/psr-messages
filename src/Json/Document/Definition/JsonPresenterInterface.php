<?php

declare(strict_types=1);

namespace Psr\Messages\Json\Document\Definition;

use Serializer\SerializableInterface;

/**
 * Presents a serializable read model as the payload of a plain-JSON document.
 * This mirrors the JSON:API presenter for the output side, without any JSON:API
 * concepts (no resource type, relationships or sparse fieldsets): a plain-JSON
 * response is just a payload, so the presenter maps a read model (a query bus
 * result) to the serializable payload the document renders.
 *
 * @template TModel of SerializableInterface<array<string, mixed>>
 *
 * @template-covariant TPayload of SerializableInterface<array<string, mixed>>
 */
interface JsonPresenterInterface
{
    /**
     * @param TModel $model
     *
     * @return TPayload
     */
    public function present(SerializableInterface $model): SerializableInterface;
}
