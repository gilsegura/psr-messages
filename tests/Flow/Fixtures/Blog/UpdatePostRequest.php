<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

use Psr\Messages\Exception\UnsupportedSerializationException;
use Psr\Messages\Schema\SchemaInterface;
use Psr\Messages\Support\Optional;
use Psr\Messages\Support\OptionalReader;
use Psr\Messages\Support\RequiredReader;

/**
 * The JSON:API request body for partially updating a post (PATCH). Unlike a
 * creation, the body has no fixed shape: "attributes" may carry "title",
 * "body", both or neither, and the "author" relationship is optional. The JSON
 * Schema validates that whatever is present has the right type, without
 * requiring any field.
 *
 * Each updatable field is an Optional, so the handler can tell "left untouched"
 * (absent) from "set to this value" (present). The id is always present (it
 * identifies the post being patched), so it is read with RequiredReader; the
 * updatable fields are read with OptionalReader.
 *
 * @implements SchemaInterface<array{data: array{type: string, id: string, attributes?: array{title?: string, body?: string}, relationships?: array{author?: array{data: array{type: string, id: string}}}}}>
 */
final readonly class UpdatePostRequest implements SchemaInterface
{
    /**
     * @param Optional<string> $title
     * @param Optional<string> $body
     * @param Optional<string> $authorId
     */
    public function __construct(
        public string $id,
        public Optional $title,
        public Optional $body,
        public Optional $authorId,
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
        $data = OptionalReader::nested($attributes, 'data');
        $attrs = OptionalReader::nested($data, 'attributes');
        $author = OptionalReader::nested(OptionalReader::nested($data, 'relationships'), 'author');

        return new self(
            RequiredReader::string($data, 'id'),
            OptionalReader::string($attrs, 'title'),
            OptionalReader::string($attrs, 'body'),
            self::authorId($author),
        );
    }

    /**
     * The author relationship is optional; when present, its linkage id is read.
     *
     * @param array<string, mixed> $author
     *
     * @return Optional<string>
     */
    private static function authorId(array $author): Optional
    {
        if ([] === $author) {
            return Optional::absent();
        }

        return OptionalReader::string(OptionalReader::nested($author, 'data'), 'id');
    }

    /**
     * @throws UnsupportedSerializationException always; a request schema is input only
     */
    #[\Override]
    public function serialize(): array
    {
        throw UnsupportedSerializationException::for('An update post request');
    }
}
