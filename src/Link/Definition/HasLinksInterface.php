<?php

declare(strict_types=1);

namespace Psr\Messages\Link\Definition;

/**
 * Implemented by elements that carry links (e.g. a JSON:API document or error).
 */
interface HasLinksInterface
{
    /**
     * @var Link[]
     */
    public array $links { get; }

    public function withLinks(Link ...$links): static;
}
