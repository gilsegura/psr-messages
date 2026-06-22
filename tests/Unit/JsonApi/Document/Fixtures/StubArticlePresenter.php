<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\RelationshipInterface;
use Psr\Messages\JsonApi\Document\Definition\ResourceInterface;
use Psr\Messages\JsonApi\Document\Definition\ResourceObject;
use Psr\Messages\JsonApi\Document\Definition\ResourcePresenterInterface;
use Psr\Messages\JsonApi\Document\Definition\ToManyRelationship;
use Psr\Messages\JsonApi\Document\Definition\ToOneRelationship;
use Psr\Messages\JsonApi\Query\Definition\AbstractFields;
use Serializer\SerializableInterface;

/**
 * A concrete presenter, as a consuming library would write one per resource: it
 * applies the requested sparse fieldsets to the read model's serialized
 * attributes, and attaches the caller's relationships through the resource's
 * withXxx methods.
 *
 * @implements ResourcePresenterInterface<StubArticle>
 */
final readonly class StubArticlePresenter implements ResourcePresenterInterface
{
    /**
     * @param StubArticle                          $model
     * @param array<string, RelationshipInterface> $relationships
     */
    #[\Override]
    public function present(SerializableInterface $model, AbstractFields $fields, array $relationships = []): ResourceInterface
    {
        $resource = new ResourceObject(
            StubType::ARTICLE,
            $model->id,
            new StubAttributes($fields->apply(StubType::ARTICLE, $model->serialize())),
        );

        return array_reduce(
            array_keys($relationships),
            fn (ResourceObject $carry, string $name): ResourceObject => $this->attach($carry, $name, $relationships[$name]),
            $resource,
        );
    }

    private function attach(ResourceObject $resource, string $name, RelationshipInterface $relationship): ResourceObject
    {
        $relationshipName = StubRelationship::from($name);

        return match (true) {
            $relationship instanceof ToOneRelationship => $resource->withOneRelationship($relationshipName, $relationship),
            $relationship instanceof ToManyRelationship => $resource->withManyRelationship($relationshipName, $relationship),
            default => $resource,
        };
    }
}
