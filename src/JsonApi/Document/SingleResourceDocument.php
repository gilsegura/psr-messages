<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document;

use Psr\Messages\Document\Document;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Psr\Messages\JsonApi\Document\Definition\HasIncludedInterface;
use Psr\Messages\JsonApi\Document\Definition\JsonApiVersion;
use Psr\Messages\JsonApi\Document\Definition\ResourceInterface;
use Psr\Messages\Link\Definition\HasLinksInterface;
use Psr\Messages\Link\Definition\Link;

/**
 * A JSON:API document whose primary data is a single resource object. Carries
 * optional top-level links, meta and included resources.
 */
final readonly class SingleResourceDocument extends Document implements HasLinksInterface, HasMetaInterface, HasIncludedInterface
{
    /** @var Link[] */
    public array $links;

    /** @var array<string, mixed> */
    public array $meta;

    /** @var ResourceInterface[] */
    public array $included;

    /**
     * @param Link[]               $links
     * @param array<string, mixed> $meta
     * @param ResourceInterface[]  $included
     */
    public function __construct(
        private ResourceInterface $resource,
        array $links = [],
        array $meta = [],
        array $included = [],
    ) {
        $this->links = $links;
        $this->meta = $meta;
        $this->included = $included;
    }

    #[\Override]
    public function serialize(): array
    {
        $document = ['data' => $this->resource->serialize()];

        if ([] !== $this->links) {
            $document['links'] = Link::toArray($this->links);
        }

        if ([] !== $this->meta) {
            $document['meta'] = $this->meta;
        }

        if ([] !== $this->included) {
            $document['included'] = array_map(static fn (ResourceInterface $resource): array => $resource->serialize(), $this->included);
        }

        $document['jsonapi'] = ['version' => JsonApiVersion::V1_1->value];

        return $document;
    }

    #[\Override]
    public function withLinks(Link ...$links): static
    {
        return new self($this->resource, $links, $this->meta, $this->included);
    }

    #[\Override]
    public function withMeta(array $meta): static
    {
        return new self($this->resource, $this->links, $meta, $this->included);
    }

    #[\Override]
    public function withIncluded(ResourceInterface ...$included): static
    {
        return new self($this->resource, $this->links, $this->meta, $included);
    }
}
