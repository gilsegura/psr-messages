<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Document\Definition\ResourceIdentifier;
use Psr\Messages\JsonApi\Document\Definition\ToOneRelationship;
use Psr\Messages\Tests\JsonApi\Document\Fixtures\StubArticle;
use Psr\Messages\Tests\JsonApi\Document\Fixtures\StubArticlePresenter;
use Psr\Messages\Tests\JsonApi\Document\Fixtures\StubFields;
use Psr\Messages\Tests\JsonApi\Document\Fixtures\StubRelationship;
use Psr\Messages\Tests\JsonApi\Document\Fixtures\StubType;

final class ResourcePresenterTest extends TestCase
{
    private StubArticlePresenter $presenter;

    private StubArticle $article;

    protected function setUp(): void
    {
        $this->presenter = new StubArticlePresenter();
        $this->article = new StubArticle('1', 'Hello', 'The body', 'p-1');
    }

    #[Test]
    public function it_presents_a_model_as_a_resource_with_all_attributes(): void
    {
        $resource = $this->presenter->present($this->article, StubFields::deserialize([]));

        self::assertSame([
            'type' => 'articles',
            'id' => '1',
            'attributes' => ['title' => 'Hello', 'body' => 'The body'],
        ], $resource->serialize());
    }

    #[Test]
    public function it_applies_sparse_fieldsets_to_the_attributes(): void
    {
        $fields = StubFields::deserialize(['fields' => ['articles' => 'title']]);

        $resource = $this->presenter->present($this->article, $fields);

        self::assertSame(['title' => 'Hello'], $resource->serialize()['attributes']);
    }

    #[Test]
    public function it_attaches_the_relationships_it_is_given(): void
    {
        $resource = $this->presenter->present(
            $this->article,
            StubFields::deserialize([]),
            [
                StubRelationship::AUTHOR->value => new ToOneRelationship(
                    new ResourceIdentifier(StubType::PERSON, 'p-1'),
                ),
            ],
        );

        self::assertSame(
            ['data' => ['type' => 'people', 'id' => 'p-1']],
            $resource->serialize()['relationships']['author'],
        );
    }

    #[Test]
    public function it_returns_the_resource_type_and_id_of_the_model(): void
    {
        $resource = $this->presenter->present($this->article, StubFields::deserialize([]));

        self::assertSame(StubType::ARTICLE, $resource->type);
        self::assertSame('1', $resource->id);
    }
}
