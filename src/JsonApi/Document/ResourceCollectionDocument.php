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
 * A JSON:API document whose primary data is a collection of resource objects.
 * Carries optional top-level links (including pagination) and meta.
 */
final readonly class ResourceCollectionDocument extends Document implements HasLinksInterface, HasMetaInterface, HasIncludedInterface
{
    /** @var ResourceInterface[] */
    private array $resources;

    /** @var Link[] */
    private array $links;

    /** @var array<string, mixed> */
    private array $meta;

    /** @var ResourceInterface[] */
    private array $included;

    /**
     * @param ResourceInterface[]  $resources
     * @param Link[]               $links
     * @param array<string, mixed> $meta
     * @param ResourceInterface[]  $included
     */
    public function __construct(array $resources, array $links = [], array $meta = [], array $included = [])
    {
        $this->resources = $resources;
        $this->links = $links;
        $this->meta = $meta;
        $this->included = $included;
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function serialize(): array
    {
        $document = ['data' => array_map(static fn (ResourceInterface $resource): array => $resource->serialize(), $this->resources)];

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

    /**
     * @return Link[]
     */
    #[\Override]
    public function links(): array
    {
        return $this->links;
    }

    #[\Override]
    public function withLinks(Link ...$links): static
    {
        return new self($this->resources, $links, $this->meta, $this->included);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    public function meta(): array
    {
        return $this->meta;
    }

    /**
     * @param array<string, mixed> $meta
     */
    #[\Override]
    public function withMeta(array $meta): static
    {
        return new self($this->resources, $this->links, $meta, $this->included);
    }

    /**
     * @return ResourceInterface[]
     */
    #[\Override]
    public function included(): array
    {
        return $this->included;
    }

    #[\Override]
    public function withIncluded(ResourceInterface ...$included): static
    {
        return new self($this->resources, $this->links, $this->meta, $included);
    }
}
