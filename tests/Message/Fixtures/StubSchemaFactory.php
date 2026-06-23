<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Message\Fixtures;

use Psr\Validator\SchemaFactoryInterface;

/**
 * A stub schema factory standing in for the Psr\Validator implementation: it
 * returns an empty schema object, enough for the message validators under test
 * since the stub validator ignores the schema.
 */
final readonly class StubSchemaFactory implements SchemaFactoryInterface
{
    #[\Override]
    public function __invoke(): object
    {
        return new \stdClass();
    }
}
