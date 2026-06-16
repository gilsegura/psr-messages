<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Definition;

/**
 * Implemented by elements that carry arbitrary meta information.
 */
interface HasMetaInterface
{
    /**
     * @return array<string, mixed>
     */
    public function meta(): array;

    /**
     * @param array<string, mixed> $meta
     */
    public function withMeta(array $meta): static;
}
