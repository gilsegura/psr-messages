<?php

declare(strict_types=1);

namespace Psr\Messages\Json\Document;

use Psr\Messages\Document\Document;
use Serializer\SerializableInterface;

/**
 * A plain-JSON response document: serializes its payload directly, with no
 * JSON:API envelope.
 */
final readonly class JsonDocument extends Document
{
    /**
     * @param SerializableInterface<array<string, mixed>> $payload
     */
    public function __construct(
        private SerializableInterface $payload,
    ) {
    }

    #[\Override]
    public function serialize(): array
    {
        return $this->payload->serialize();
    }
}
