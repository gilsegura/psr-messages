<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

/**
 * Implemented by errors tied to a specific part of the request (body, query,
 * headers). Domain errors (e.g. a conflict) do not implement this.
 */
interface HasErrorSourceInterface
{
    public function source(): Source;

    public function hasSource(): bool;

    public function withSource(Source $source): static;
}
