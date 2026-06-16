<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow\Fixtures\Blog;

use Psr\Messages\Tests\Flow\Fixtures\ResourceType;

/**
 * Small helper to match raw resource type strings against the domain enum in
 * schema supports() checks.
 */
final class ResourceTypeMatcher
{
    private function __construct()
    {
    }

    public static function isPost(mixed $type): bool
    {
        return \is_string($type) && ResourceType::POST === ResourceType::tryFrom($type);
    }
}
