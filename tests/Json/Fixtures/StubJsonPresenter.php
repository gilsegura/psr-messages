<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Json\Fixtures;

use Psr\Messages\Json\Document\Definition\JsonPresenterInterface;
use Serializer\SerializableInterface;

/**
 * A concrete plain-JSON presenter, as a consuming library would write one per
 * resource: it maps a read model to the payload the JSON document renders.
 *
 * @implements JsonPresenterInterface<StubReadModel, StubReadModel>
 */
final readonly class StubJsonPresenter implements JsonPresenterInterface
{
    /**
     * @param StubReadModel $model
     */
    #[\Override]
    public function present(SerializableInterface $model): SerializableInterface
    {
        return $model;
    }
}
