<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * Implemented by documents that include related resources (a "compound
 * document"). The included resources must have full linkage to the primary
 * data; ensuring that is the caller's responsibility when building the
 * response, not this library's.
 */
interface HasIncludedInterface
{
    /**
     * @return ResourceInterface[]
     */
    public function included(): array;

    public function withIncluded(ResourceInterface ...$included): static;
}
