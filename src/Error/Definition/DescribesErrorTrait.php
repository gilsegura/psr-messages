<?php

declare(strict_types=1);

namespace Psr\Messages\Error\Definition;

use Psr\Messages\Support\ClassName;

/**
 * Default ErrorInterface title/detail for a throwable: the title is derived from
 * the class name and the detail is the exception message.
 *
 * @mixin \Throwable
 */
trait DescribesErrorTrait
{
    public function title(): string
    {
        return ClassName::toTitle(static::class);
    }

    public function detail(): string
    {
        return $this->getMessage();
    }
}
