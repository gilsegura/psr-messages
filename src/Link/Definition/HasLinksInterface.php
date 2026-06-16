<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

/**
 * Implemented by elements that carry links (e.g. a JSON:API document or error).
 */
interface HasLinksInterface
{
    /**
     * @return Link[]
     */
    public function links(): array;

    public function withLinks(Link ...$links): static;
}
