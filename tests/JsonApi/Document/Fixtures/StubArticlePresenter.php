<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Document\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\FieldsetInterface;
use Psr\Messages\JsonApi\Document\Definition\RelationshipInterface;
use Psr\Messages\JsonApi\Document\Definition\ResourceInterface;
use Psr\Messages\JsonApi\Document\Definition\ResourceObject;
use Psr\Messages\JsonApi\Document\Definition\ResourcePresenterInterface;
use Psr\Messages\JsonApi\Document\Definition\ToManyRelationship;
use Psr\Messages\JsonApi\Document\Definition\ToOneRelationship;
use Serializer\SerializableInterface;

/**
 * A concrete presenter, as a consuming library would write one per resource: it
 * builds the resource from the model with the requested sparse fieldset attached,
 * then attaches the caller's relationships. The type is written once, on the
 * ResourceObject, and the fieldset trims the attributes on output.
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
    public function present(SerializableInterface $model, FieldsetInterface $fields, array $relationships = []): ResourceInterface
    {
        $resource = new ResourceObject(
            StubType::ARTICLE,
            $model->id,
            new StubAttributes($model->serialize()),
        );

        return array_reduce(
            array_keys($relationships),
            fn (ResourceObject $carry, string $name): ResourceObject => $this->attach($carry, $name, $relationships[$name]),
            $resource
                ->withFieldset($fields),
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
