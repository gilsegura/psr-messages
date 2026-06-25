<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\JsonApi\Include\Fixtures;

use Psr\Messages\JsonApi\Document\Definition\FieldsetInterface;
use Psr\Messages\JsonApi\Document\Definition\RelationshipNameInterface;
use Psr\Messages\JsonApi\Include\IncludeInterface;
use Psr\Messages\JsonApi\Include\ResolvedInclude;

/**
 * A minimal include used to test selection: it only carries a name and returns an
 * empty resolution, which is enough to assert which includes get selected.
 *
 * @implements IncludeInterface<object>
 */
final readonly class StubInclude implements IncludeInterface
{
    public function __construct(private RelationshipNameInterface $name)
    {
    }

    #[\Override]
    public function name(): RelationshipNameInterface
    {
        return $this->name;
    }

    #[\Override]
    public function resolve(array $models, FieldsetInterface $fields): ResolvedInclude
    {
        return new ResolvedInclude($this->name, [], []);
    }
}
