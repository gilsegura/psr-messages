<?php

declare(strict_types=1);

namespace Psr\Messages\Exception;

/**
 * Marks an exception that signals a programming error or impossible state,
 * typically a precondition that should have been guaranteed earlier in the
 * pipeline (e.g. by schema validation). These indicate bugs, not bad input.
 */
interface LogicExceptionInterface extends \Throwable
{
}
