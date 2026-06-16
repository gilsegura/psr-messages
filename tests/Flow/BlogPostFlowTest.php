<?php

declare(strict_types=1);

namespace Psr\Messages\Tests\Flow;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Messages\JsonApi\Document\Definition\JsonApiVersion;
use Psr\Messages\JsonApi\Document\ResourceCollectionDocument;
use Psr\Messages\JsonApi\Document\SingleResourceDocument;
use Psr\Messages\JsonApi\JsonApiParser;
use Psr\Messages\JsonApi\JsonApiResponseFactory;
use Psr\Messages\JsonApi\Query\JsonApiQuerySchema;
use Psr\Messages\Link\Definition\Href;
use Psr\Messages\Link\Definition\Link;
use Psr\Messages\Link\Definition\LinkType;
use Psr\Messages\Message\SchemaValidator;
use Psr\Messages\Middleware\ParsedBodyMiddleware;
use Psr\Messages\Middleware\ParseQueryParamsMiddleware;
use Psr\Messages\Schema\SchemaResolver;
use Psr\Messages\Tests\Flow\Fixtures\Blog\Author;
use Psr\Messages\Tests\Flow\Fixtures\Blog\CreatePostRequest;
use Psr\Messages\Tests\Flow\Fixtures\Blog\Post;
use Psr\Messages\Tests\Flow\Fixtures\Blog\PostPresenter;
use Psr\Messages\Tests\Flow\Fixtures\Blog\PostRepository;
use Psr\Messages\Tests\Flow\Fixtures\Blog\UpdatePostRequest;
use Psr\Server\Middleware\MiddlewareRunner;
use Psr\Server\RequestHandler;
use Psr\Server\ResponseFactory\ResponseFactory;
use Psr\Server\ResponseFactory\Status;
use Psr\Validator\Schema\SchemaValidator as OpisSchemaValidator;
use Psr\Validator\SchemaFactory\RawFactory;

/**
 * End-to-end JSON:API blog flow through a real PSR-15 middleware pipeline:
 *
 *  - creating a post: validate body (JSON Schema) -> parse into a typed request
 *    object (ParsedBodyMiddleware + JsonApiParser) -> handle -> render a single
 *    resource document with an author relationship, included resources, and a
 *    resource-level self link.
 *  - listing posts: parse query parameters (sort, page, filter, fields) into a
 *    JsonApiQuerySchema -> handle -> render a resource collection document with
 *    to-many comment relationships, included resources, pagination links and
 *    meta.
 *
 * These flows are the reference the README is built on.
 */
final class BlogPostFlowTest extends TestCase
{
    private const string CREATE_POST_SCHEMA = <<<'JSON'
        {
            "type": "object",
            "properties": {
                "data": {
                    "type": "object",
                    "properties": {
                        "type": { "type": "string", "enum": ["posts"] },
                        "attributes": {
                            "type": "object",
                            "properties": {
                                "title": { "type": "string", "minLength": 1 },
                                "body": { "type": "string", "minLength": 1 }
                            },
                            "required": ["title", "body"]
                        },
                        "relationships": {
                            "type": "object",
                            "properties": {
                                "author": {
                                    "type": "object",
                                    "properties": {
                                        "data": {
                                            "type": "object",
                                            "properties": {
                                                "type": { "type": "string", "enum": ["authors"] },
                                                "id": { "type": "string", "minLength": 1 }
                                            },
                                            "required": ["type", "id"]
                                        }
                                    },
                                    "required": ["data"]
                                }
                            },
                            "required": ["author"]
                        }
                    },
                    "required": ["type", "attributes", "relationships"]
                }
            },
            "required": ["data"]
        }
        JSON;

    private const string UPDATE_POST_SCHEMA = <<<'JSON'
        {
            "type": "object",
            "properties": {
                "data": {
                    "type": "object",
                    "properties": {
                        "type": { "type": "string", "enum": ["posts"] },
                        "id": { "type": "string", "minLength": 1 },
                        "attributes": {
                            "type": "object",
                            "properties": {
                                "title": { "type": "string", "minLength": 1 },
                                "body": { "type": "string", "minLength": 1 }
                            }
                        },
                        "relationships": {
                            "type": "object",
                            "properties": {
                                "author": {
                                    "type": "object",
                                    "properties": {
                                        "data": {
                                            "type": "object",
                                            "properties": {
                                                "type": { "type": "string", "enum": ["authors"] },
                                                "id": { "type": "string", "minLength": 1 }
                                            },
                                            "required": ["type", "id"]
                                        }
                                    },
                                    "required": ["data"]
                                }
                            }
                        }
                    },
                    "required": ["type", "id"]
                }
            },
            "required": ["data"]
        }
        JSON;

    #[Test]
    public function it_creates_a_post_through_the_pipeline_and_renders_a_single_document(): void
    {
        $repository = new PostRepository();
        $presenter = new PostPresenter();
        $factory = new JsonApiResponseFactory($this->responseFactory());

        // --- INPUT: a JSON:API creation request ---
        $request = new ServerRequest(
            'POST',
            'https://api.example.com/posts',
            ['Content-Type' => 'application/vnd.api+json'],
            <<<'JSON'
                {
                    "data": {
                        "type": "posts",
                        "attributes": { "title": "Hello world", "body": "My first post." },
                        "relationships": {
                            "author": { "data": { "type": "authors", "id": "a-1" } }
                        }
                    }
                }
                JSON,
        );

        // --- PIPELINE: validate body, then parse it into a typed request object ---
        $bodyValidator = new SchemaValidator(new OpisSchemaValidator(), new RawFactory(self::CREATE_POST_SCHEMA));
        $parser = new JsonApiParser(new SchemaResolver(CreatePostRequest::class));

        $pipeline = new MiddlewareRunner(
            new ValidatingMiddleware($bodyValidator),
            new ParsedBodyMiddleware($parser),
        );

        // --- HANDLER: read the typed body, persist, render the document ---
        $handler = new RequestHandler(function (ServerRequestInterface $request) use ($repository, $presenter, $factory): ResponseInterface {
            $createPost = $request->getParsedBody();
            self::assertInstanceOf(CreatePostRequest::class, $createPost);

            $post = $repository->create($createPost->title, $createPost->body, $createPost->authorId);

            $resource = $presenter->resource($post)
                ->withLinks(new Link(LinkType::SELF, new Href('https://api.example.com/posts/'.$post->id)));

            $document = new SingleResourceDocument($resource)
                ->withIncluded(...$presenter->included($post))
                ->withLinks(new Link(LinkType::SELF, new Href('https://api.example.com/posts/'.$post->id)));

            return $factory->single($document, Status::CREATED);
        });

        $response = $pipeline($request, $handler);

        // --- ASSERTIONS ---
        self::assertSame(201, $response->getStatusCode());
        self::assertSame('application/vnd.api+json', $response->getHeaderLine('content-type'));

        /** @var array<string, mixed> $json */
        $json = json_decode((string) $response->getBody(), true);

        self::assertSame('posts', $json['data']['type']);
        self::assertSame('Hello world', $json['data']['attributes']['title']);
        self::assertSame(
            ['type' => 'authors', 'id' => 'a-1'],
            $json['data']['relationships']['author']['data'],
        );
        self::assertArrayHasKey('links', $json['data']['relationships']['author']);
        // resource-level self link
        self::assertSame('https://api.example.com/posts/p-new-1', $json['data']['links']['self']);
        // document-level self link
        self::assertSame('https://api.example.com/posts/p-new-1', $json['links']['self']);
        // included author
        self::assertSame('authors', $json['included'][0]['type']);
        self::assertSame(['name' => 'Ada Lovelace'], $json['included'][0]['attributes']);
        self::assertSame(JsonApiVersion::V1_1->value, $json['jsonapi']['version']);
    }

    #[Test]
    public function it_lists_posts_parsing_query_parameters_and_renders_a_collection(): void
    {
        $repository = new PostRepository();
        $presenter = new PostPresenter();
        $factory = new JsonApiResponseFactory($this->responseFactory());

        // --- INPUT: a list request with sort, pagination, filter and sparse fieldsets ---
        $request = new ServerRequest('GET', 'https://api.example.com/posts')
            ->withQueryParams([
                'sort' => '-created,title',
                'page' => ['number' => '1', 'size' => '2'],
                'filter' => ['status' => 'published'],
                'fields' => ['posts' => 'title', 'authors' => 'name'],
                'include' => 'author,comments',
            ]);

        // --- PIPELINE: parse the query parameters into a JsonApiQuerySchema ---
        $pipeline = new MiddlewareRunner(
            new ParseQueryParamsMiddleware(new SchemaResolver(JsonApiQuerySchema::class)),
        );

        $handler = new RequestHandler(function (ServerRequestInterface $request) use ($repository, $presenter, $factory): ResponseInterface {
            $query = $request->getAttribute(JsonApiQuerySchema::class);
            self::assertInstanceOf(JsonApiQuerySchema::class, $query);

            // the parsed query drives what the application fetches
            $posts = $repository->page($query->page->number, $query->page->size);

            $resources = array_map($presenter->resource(...), $posts);
            $included = array_merge(...array_map($presenter->included(...), $posts));

            $document = new ResourceCollectionDocument($resources)
                ->withIncluded(...$included)
                ->withLinks(
                    new Link(LinkType::SELF, new Href('https://api.example.com/posts?page[number]=1')),
                    new Link(LinkType::NEXT, new Href('https://api.example.com/posts?page[number]=2')),
                    new Link(LinkType::LAST, new Href('https://api.example.com/posts?page[number]=3')),
                )
                ->withMeta(['total' => $repository->total()]);

            return $factory->collection($document, Status::OK);
        });

        $response = $pipeline($request, $handler);

        // --- ASSERTIONS ---
        self::assertSame(200, $response->getStatusCode());

        /** @var array<string, mixed> $json */
        $json = json_decode((string) $response->getBody(), true);

        self::assertIsArray($json['data']);
        self::assertCount(2, $json['data']);
        self::assertSame('posts', $json['data'][0]['type']);
        self::assertSame(
            [['type' => 'comments', 'id' => 'c-1']],
            $json['data'][0]['relationships']['comments']['data'],
        );
        self::assertSame(1, $json['data'][0]['relationships']['comments']['meta']['count']);
        self::assertSame('https://api.example.com/posts?page[number]=2', $json['links']['next']);
        self::assertSame('https://api.example.com/posts?page[number]=3', $json['links']['last']);
        self::assertSame(3, $json['meta']['total']);
        self::assertNotEmpty($json['included']);
    }

    #[Test]
    public function it_renders_a_jsonapi_error_document_when_body_validation_fails(): void
    {
        $factory = new JsonApiResponseFactory($this->responseFactory());

        // --- INPUT: an invalid creation request (missing required title) ---
        $request = new ServerRequest(
            'POST',
            'https://api.example.com/posts',
            ['Content-Type' => 'application/vnd.api+json'],
            '{"data":{"type":"posts","attributes":{"body":"No title"},"relationships":{"author":{"data":{"type":"authors","id":"a-1"}}}}}',
        );

        $bodyValidator = new SchemaValidator(new OpisSchemaValidator(), new RawFactory(self::CREATE_POST_SCHEMA));

        // --- VALIDATION fails: the exception is rendered as an error document ---
        try {
            $bodyValidator($request);
            self::fail('Expected validation to fail.');
        } catch (\Throwable $throwable) {
            $response = $factory->error($throwable, Status::UNPROCESSABLE_CONTENT);
        }

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('application/vnd.api+json', $response->getHeaderLine('content-type'));

        /** @var array<string, mixed> $json */
        $json = json_decode((string) $response->getBody(), true);

        self::assertArrayHasKey('errors', $json);
        self::assertNotEmpty($json['errors']);
        self::assertArrayHasKey('source', $json['errors'][0]);
        self::assertSame(JsonApiVersion::V1_1->value, $json['jsonapi']['version']);
    }

    #[Test]
    public function it_partially_updates_a_post_changing_only_the_fields_present(): void
    {
        $repository = new PostRepository();
        $presenter = new PostPresenter();
        $factory = new JsonApiResponseFactory($this->responseFactory());

        // the post as it stands before the update
        $before = $repository->find('p-1');
        self::assertInstanceOf(Post::class, $before);
        self::assertSame('First', $before->title);
        self::assertSame('Body one', $before->body);

        // --- INPUT: a PATCH that touches only the title ---
        $request = new ServerRequest(
            'PATCH',
            'https://api.example.com/posts/p-1',
            ['Content-Type' => 'application/vnd.api+json'],
            '{"data":{"type":"posts","id":"p-1","attributes":{"title":"Edited title"}}}',
        );

        // --- PIPELINE: validate (no field is required), then parse ---
        $bodyValidator = new SchemaValidator(new OpisSchemaValidator(), new RawFactory(self::UPDATE_POST_SCHEMA));
        $parser = new JsonApiParser(new SchemaResolver(UpdatePostRequest::class));

        $pipeline = new MiddlewareRunner(
            new ValidatingMiddleware($bodyValidator),
            new ParsedBodyMiddleware($parser),
        );

        $handler = new RequestHandler(function (ServerRequestInterface $request) use ($repository, $presenter, $factory): ResponseInterface {
            $update = $request->getParsedBody();
            self::assertInstanceOf(UpdatePostRequest::class, $update);

            // only "title" was sent: it is present, the others are absent
            self::assertTrue($update->title->isPresent());
            self::assertFalse($update->body->isPresent());
            self::assertFalse($update->authorId->isPresent());
            self::assertSame('Edited title', $update->title->get());

            $post = $repository->update($update->id, $update->title, $update->body, $update->authorId);

            return $factory->single(new SingleResourceDocument($presenter->resource($post)), Status::OK);
        });

        $response = $pipeline($request, $handler);

        // --- ASSERTIONS: title changed, body and author left untouched ---
        self::assertSame(200, $response->getStatusCode());

        /** @var array<string, mixed> $json */
        $json = json_decode((string) $response->getBody(), true);

        self::assertSame('Edited title', $json['data']['attributes']['title']);
        self::assertSame('Body one', $json['data']['attributes']['body']);

        $after = $repository->find('p-1');
        self::assertInstanceOf(Post::class, $after);
        self::assertSame('Edited title', $after->title);
        self::assertSame('Body one', $after->body);
        self::assertSame('a-1', $after->author->id);
    }

    private function responseFactory(): ResponseFactory
    {
        $psr17 = new Psr17Factory();

        return new ResponseFactory($psr17, $psr17);
    }
}
