<?php

declare(strict_types=1);

namespace Psr\Messages;

/**
 * @phpstan-require-implements MediaTypeParserInterface
 */
trait SupportsMediaTypeTrait
{
    public function supports(MediaType $mediaType): bool
    {
        return $this->mediaType()->equals($mediaType);
    }
}
