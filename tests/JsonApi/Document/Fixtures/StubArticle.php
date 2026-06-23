<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Document\Fixtures;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * A serializable read model standing in for what a presenter receives: a query
 * bus result the consuming library maps to a JSON:API resource. Its serialized
 * form is the attributes map the presenter applies sparse fieldsets to.
 *
 * @implements SerializableInterface<array<string, mixed>>
 */
final readonly class StubArticle implements SerializableInterface
{
    public function __construct(
        public string $id,
        public string $title,
        public string $body,
        public string $authorId,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
        ];
    }

    /**
     * @throws UnsupportedDeserializationException always; a read model is output only here
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('A stub article read model');
    }
}
