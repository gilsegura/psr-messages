<?php

declare(strict_types=1);

namespace Psr\Messages\Support;

use Psr\Messages\Exception\UnexpectedStateException;

/**
 * An optional value: either present (carrying a value, including null) or
 * absent. It distinguishes "a value was provided" from "nothing was provided",
 * which a nullable type cannot: in a partial update, an absent field must be
 * left untouched while a present null field must be written.
 *
 * @template TValue
 */
final readonly class Optional
{
    /**
     * @param TValue $value
     */
    private function __construct(
        private bool $present,
        private mixed $value = null,
    ) {
    }

    /**
     * @template TGiven
     *
     * @param TGiven $value
     *
     * @return self<TGiven>
     */
    public static function of(mixed $value): self
    {
        return new self(true, $value);
    }

    /**
     * @return self<never>
     */
    public static function absent(): self
    {
        /** @var self<never> $absent */
        $absent = new self(false);

        return $absent;
    }

    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * @return TValue
     *
     * @throws UnexpectedStateException when the value is absent
     */
    public function get(): mixed
    {
        if (!$this->present) {
            throw UnexpectedStateException::reason('an absent optional value was read.');
        }

        return $this->value;
    }

    /**
     * @template TDefault
     *
     * @param TDefault $default
     *
     * @return TValue|TDefault
     */
    public function orElse(mixed $default): mixed
    {
        return $this->present ? $this->value : $default;
    }
}
