<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\Message;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Exception\MalformedContentException;
use Psr\Messages\Message\Json;

final class JsonTest extends TestCase
{
    #[Test]
    public function it_decodes_a_json_object_to_an_array(): void
    {
        self::assertSame(['a' => 1, 'b' => 2], Json::toArray('{"a":1,"b":2}'));
    }

    #[Test]
    public function it_decodes_an_empty_string_to_an_empty_array(): void
    {
        self::assertSame([], Json::toArray(''));
    }

    #[Test]
    public function it_throws_on_malformed_json(): void
    {
        $this->expectException(MalformedContentException::class);

        Json::toArray('{not valid');
    }

    #[Test]
    public function it_decodes_to_a_nested_object(): void
    {
        $object = Json::toObject('{"page":{"size":10}}');

        self::assertEquals((object) ['page' => (object) ['size' => 10]], $object);
    }

    #[Test]
    public function it_decodes_an_empty_string_to_an_empty_object(): void
    {
        self::assertEquals(new \stdClass(), Json::toObject(''));
    }

    #[Test]
    public function it_converts_a_nested_array_to_a_nested_object(): void
    {
        $object = Json::objectFromArray(['filter' => ['status' => 'published']]);

        self::assertEquals((object) ['filter' => (object) ['status' => 'published']], $object);
    }
}
