<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Definition;

/**
 * Implemented by elements that carry arbitrary meta information.
 */
interface HasMetaInterface
{
    /**
     * @var array<string, mixed>
     */
    public array $meta { get; }

    /**
     * @param array<string, mixed> $meta
     */
    public function withMeta(array $meta): static;
}
