<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Unit\JsonApi;

use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Messages\Json\JsonParser;
use Psr\Messages\JsonApi\JsonApiParser;
use Psr\Messages\MediaType;
use Psr\Messages\Schema\SchemaResolver;
use Psr\Messages\Tests\Unit\Schema\Fixtures\UpperSchema;

final class ParserTest extends TestCase
{
    #[Test]
    public function json_api_parser_supports_only_the_json_api_media_type(): void
    {
        $parser = new JsonApiParser(new SchemaResolver(UpperSchema::class));

        self::assertSame(MediaType::JSON_API, $parser->mediaType());
        self::assertTrue($parser->supports(MediaType::JSON_API));
        self::assertFalse($parser->supports(MediaType::JSON));
    }

    #[Test]
    public function json_api_parser_resolves_the_body_into_a_typed_schema(): void
    {
        $parser = new JsonApiParser(new SchemaResolver(UpperSchema::class));

        $request = new ServerRequest(
            'POST',
            'https://api.example.com/things',
            ['Content-Type' => 'application/vnd.api+json'],
            '{"kind":"upper","value":"hello"}',
        );

        $parsed = $parser->parse($request)->getParsedBody();

        self::assertInstanceOf(UpperSchema::class, $parsed);
        self::assertSame('HELLO', $parsed->value);
    }

    #[Test]
    public function json_parser_supports_only_the_json_media_type(): void
    {
        $parser = new JsonParser(new SchemaResolver(UpperSchema::class));

        self::assertSame(MediaType::JSON, $parser->mediaType());
        self::assertTrue($parser->supports(MediaType::JSON));
        self::assertFalse($parser->supports(MediaType::JSON_API));
    }

    #[Test]
    public function json_parser_resolves_the_body_into_a_typed_schema(): void
    {
        $parser = new JsonParser(new SchemaResolver(UpperSchema::class));

        $request = new ServerRequest(
            'POST',
            'https://api.example.com/things',
            ['Content-Type' => 'application/json'],
            '{"kind":"upper","value":"hello"}',
        );

        $parsed = $parser->parse($request)->getParsedBody();

        self::assertInstanceOf(UpperSchema::class, $parsed);
        self::assertSame('HELLO', $parsed->value);
    }
}
