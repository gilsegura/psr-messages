<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Message\Fixtures;

use Psr\Validator\SchemaValidatorInterface;

/**
 * A stub schema validator standing in for the Psr\Validator implementation: it
 * returns the errors it was given, so the message validators can be tested for
 * how they react to a valid or invalid payload without a real JSON Schema engine.
 */
final readonly class StubSchemaValidator implements SchemaValidatorInterface
{
    /**
     * @param array<int, array<string, mixed>> $errors
     */
    public function __construct(
        private array $errors = [],
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    #[\Override]
    public function __invoke(object $data, object $schema): array
    {
        return $this->errors;
    }
}
