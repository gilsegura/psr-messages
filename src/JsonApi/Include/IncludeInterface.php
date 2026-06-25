<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Include;

use Psr\Messages\JsonApi\Document\Definition\FieldsetInterface;
use Psr\Messages\JsonApi\Document\Definition\RelationshipNameInterface;

/**
 * A composable JSON:API include: one relationship that can be embedded in a
 * document (e.g. "tags"). Each include is a self-contained piece that knows its
 * name and how to resolve itself for a set of primary models in a single load —
 * loading the related resources (through a query handler, never the repository),
 * presenting them, and computing the linkage per primary model. Adding a new
 * relationship is adding a new implementation; nothing else changes.
 *
 * @template TPrimary of object
 */
interface IncludeInterface
{
    /**
     * The relationship this include embeds, as it appears in ?include= and in the
     * resource's relationships (e.g. the one backing "tags").
     */
    public function name(): RelationshipNameInterface;

    /**
     * Resolves this include for the given primary models in a single load.
     *
     * @param TPrimary[] $models
     *
     * @return ResolvedInclude<TPrimary>
     */
    public function resolve(array $models, FieldsetInterface $fields): ResolvedInclude;
}
