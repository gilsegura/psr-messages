<?php

declare(strict_types=1);

namespace Psr\Messages\Json;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Messages\Exception\MalformedContentException;
use Psr\Messages\MediaType;
use Psr\Messages\MediaTypeParserInterface;
use Psr\Messages\Message\BodyTrait;
use Psr\Messages\Schema\Exception\UnresolvedSchemaException;
use Psr\Messages\Schema\SchemaResolverInterface;
use Psr\Messages\SupportsMediaTypeTrait;

/**
 * Parses a plain JSON request body, resolving the schema that applies to the
 * decoded body and leaving the typed schema object in the parsed body.
 */
final readonly class JsonParser implements MediaTypeParserInterface
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
        return MediaType::JSON;
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
