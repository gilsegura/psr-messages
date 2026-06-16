<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Schema\SchemaInterface;
use Psr\Messages\Support\RequiredReader;

/**
 * The JSON:API request body for creating a post, parsed into a typed object.
 * Structure (data.type === "posts", required attributes, required author
 * relationship) is validated upstream by JSON Schema; here it is only read into
 * typed values, assuming that guaranteed structure via RequiredReader.
 *
 * @implements SchemaInterface<array{data: array{type: string, attributes: array{title: string, body: string}, relationships: array{author: array{data: array{type: string, id: string}}}}}>
 */
final readonly class CreatePostRequest implements SchemaInterface
{
    public function __construct(
        public string $title,
        public string $body,
        public string $authorId,
    ) {
    }

    #[\Override]
    public static function supports(array $data): bool
    {
        $type = $data['data']['type'] ?? null;

        return ResourceTypeMatcher::isPost($type);
    }

    #[\Override]
    public static function deserialize(array $attributes): static
    {
        $data = RequiredReader::nested($attributes, 'data');
        $attrs = RequiredReader::nested($data, 'attributes');
        $author = RequiredReader::nested(RequiredReader::nested($data, 'relationships'), 'author');

        return new self(
            RequiredReader::string($attrs, 'title'),
            RequiredReader::string($attrs, 'body'),
            RequiredReader::string(RequiredReader::nested($author, 'data'), 'id'),
        );
    }

    /**
     * @throws UnsupportedSerializationException always; a request schema is input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('A create post request');
    }
}
