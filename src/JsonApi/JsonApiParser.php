<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Messages\Exception\MalformedContentException;
use Psr\Messages\MediaType;
use Psr\Messages\MediaTypeParserInterface;
use Psr\Messages\Message\BodyTrait;
use Psr\Messages\Schema\Exception\UnresolvedSchemaException;
use Psr\Messages\Schema\SchemaResolverInterface;
use Psr\Messages\SupportsMediaTypeTrait;

/**
 * Parses a JSON:API request body, resolving the schema that applies to the
 * decoded document and leaving the typed schema object in the parsed body. The
 * resolver receives the whole document, so each schema reads what it needs.
 */
final readonly class JsonApiParser implements MediaTypeParserInterface
{
    use SupportsMediaTypeTrait;
    use BodyTrait;

    public function __construct(
        private SchemaResolverInterface $resolver,
    ) {
    }

    #[\Override]
    public function mediaType(): MediaType
    {
        return MediaType::JSON_API;
    }

    /**
     * @throws MalformedContentException
     * @throws UnresolvedSchemaException
     */
    #[\Override]
    public function parse(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withParsedBody(
            $this->resolver->resolve(
                $this->decodeBody($request),
            ),
        );
    }
}
