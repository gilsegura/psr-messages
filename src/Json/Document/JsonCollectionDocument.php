<?php

declare(strict_types=1);

namespace Psr\Messages\Json\Document;

use Psr\Messages\Exception\UnsupportedDeserializationException;
use Serializer\SerializableInterface;

/**
 * A plain-JSON collection document: serializes a list of payloads directly, with
 * no JSON:API envelope. It mirrors the JSON:API collection document for the
 * plain-JSON flow, so an endpoint returning many items has the same shape of
 * output as one returning a single item. Output only.
 *
 * @implements SerializableInterface<array<array<string, mixed>>>
 */
final readonly class JsonCollectionDocument implements SerializableInterface
{
    /** @var SerializableInterface<array<string, mixed>>[] */
    private array $payloads;

    /**
     * @param SerializableInterface<array<string, mixed>>[] $payloads
     */
    public function __construct(array $payloads)
    {
        $this->payloads = $payloads;
    }

    #[\Override]
    public function serialize(): array
    {
        return array_map(
            static fn (SerializableInterface $payload): array => $payload->serialize(),
            $this->payloads,
        );
    }

    /**
     * @throws UnsupportedDeserializationException always; a response document is output only
     */
    #[\Override]
    public static function deserialize(array $attributes): static
    {
        throw UnsupportedDeserializationException::for('A json collection document');
    }
}
