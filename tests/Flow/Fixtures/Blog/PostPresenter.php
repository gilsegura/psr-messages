<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

use Psr\Messages\JsonApi\Document\Definition\ResourceIdentifier;
use Psr\Messages\JsonApi\Document\Definition\ResourceObject;
use Psr\Messages\JsonApi\Document\Definition\ToManyRelationship;
use Psr\Messages\JsonApi\Document\Definition\ToOneRelationship;
use Psr\Messages\Link\Definition\Href;
use Psr\Messages\Link\Definition\Link;
use Psr\Messages\Link\Definition\LinkType;
use Psr\Messages\Tests\Flow\Fixtures\ResourceType;

/**
 * Turns the in-memory blog domain into JSON:API resource objects: a post with
 * an "author" to-one relationship and a "comments" to-many relationship, each
 * relationship carrying its own self/related links, plus the related resources
 * for the document's "included" section. Every resource carries a self link.
 */
final readonly class PostPresenter
{
    public function __construct(
        private string $baseUri = 'https://api.example.com',
    ) {
    }

    public function resource(Post $post): ResourceObject
    {
        $commentIdentifiers = array_map(
            static fn (array $comment): ResourceIdentifier => new ResourceIdentifier(ResourceType::COMMENT, $comment['id']),
            $post->comments,
        );

        $author = new ToOneRelationship(new ResourceIdentifier(ResourceType::AUTHOR, $post->author->id))
            ->withLinks(
                new Link(LinkType::SELF, new Href($this->baseUri.'/posts/'.$post->id.'/relationships/author')),
                new Link(LinkType::RELATED, new Href($this->baseUri.'/posts/'.$post->id.'/author')),
            );

        $comments = new ToManyRelationship($commentIdentifiers)
            ->withLinks(new Link(LinkType::RELATED, new Href($this->baseUri.'/posts/'.$post->id.'/comments')))
            ->withMeta(['count' => \count($post->comments)]);

        return new ResourceObject(
            ResourceType::POST,
            $post->id,
            new PostAttributes($post->title, $post->body),
            ['author' => $author],
            ['comments' => $comments],
        )->withLinks(new Link(LinkType::SELF, new Href($this->baseUri.'/posts/'.$post->id)));
    }

    /**
     * The author and comments of a post, as included resource objects.
     *
     * @return ResourceObject[]
     */
    public function included(Post $post): array
    {
        $author = new ResourceObject(
            ResourceType::AUTHOR,
            $post->author->id,
            new AuthorAttributes($post->author->name),
        )->withLinks(new Link(LinkType::SELF, new Href($this->baseUri.'/authors/'.$post->author->id)));

        $comments = array_map(
            fn (array $comment): ResourceObject => new ResourceObject(
                ResourceType::COMMENT,
                $comment['id'],
                new CommentAttributes($comment['body']),
                ['author' => new ToOneRelationship(new ResourceIdentifier(ResourceType::AUTHOR, $comment['authorId']))],
            )->withLinks(new Link(LinkType::SELF, new Href($this->baseUri.'/comments/'.$comment['id']))),
            $post->comments,
        );

        return [$author, ...$comments];
    }
}
