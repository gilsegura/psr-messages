<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi\Document;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\JsonApi\Document\Definition\ResourceIdentifier;
use Psr\Messages\Tests\Unit\JsonApi\Document\Fixtures\StubType;

final class ResourceIdentifierTest extends TestCase
{
    #[Test]
    public function it_serializes_type_and_id(): void
    {
        $identifier = new ResourceIdentifier(StubType::ARTICLE, '1');

        self::assertSame(['type' => 'articles', 'id' => '1'], $identifier->serialize());
    }

    #[Test]
    public function it_includes_meta_when_present(): void
    {
        $identifier = new ResourceIdentifier(StubType::ARTICLE, '1')->withMeta(['featured' => true]);

        self::assertSame(
            ['type' => 'articles', 'id' => '1', 'meta' => ['featured' => true]],
            $identifier->serialize(),
        );
    }
}
