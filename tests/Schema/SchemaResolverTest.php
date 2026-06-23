<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Schema;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Schema\Exception\UnresolvedSchemaException;
use Psr\Messages\Schema\SchemaResolver;
use Psr\Messages\Tests\Schema\Fixtures\UpperSchema;

final class SchemaResolverTest extends TestCase
{
    #[Test]
    public function it_resolves_the_supporting_schema_and_deserializes(): void
    {
        $resolver = new SchemaResolver(UpperSchema::class);

        $schema = $resolver->resolve(['kind' => 'upper', 'value' => 'hello']);

        self::assertInstanceOf(UpperSchema::class, $schema);
        self::assertSame('HELLO', $schema->value);
    }

    #[Test]
    public function it_throws_when_no_schema_supports_the_data(): void
    {
        $resolver = new SchemaResolver(UpperSchema::class);

        $this->expectException(UnresolvedSchemaException::class);

        $resolver->resolve(['kind' => 'other']);
    }
}
