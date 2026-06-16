<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

/**
 * What every error exposes: a machine-readable code, a short human title, and a
 * human-readable detail. Optional parts (source, links, meta) live in separate
 * interfaces, so an error only carries what it actually has (e.g. a domain
 * conflict has no source).
 */
interface ErrorInterface
{
    public function errorCode(): ErrorCodeInterface;

    /**
     * A short, human-readable summary of the error type, stable across
     * occurrences (e.g. derived from the exception class name).
     */
    public function title(): string;

    /**
     * A human-readable explanation specific to this occurrence.
     */
    public function detail(): string;
}
