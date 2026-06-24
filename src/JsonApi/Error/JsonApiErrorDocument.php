<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Error;

use Psr\Messages\Error\Definition\ErrorInterface;
use Psr\Messages\Error\Definition\HasErrorSourceInterface;
use Psr\Messages\Error\ErrorDocument;
use Psr\Messages\JsonApi\Definition\HasMetaInterface;
use Psr\Messages\JsonApi\Document\Definition\JsonApiVersion;
use Psr\Messages\Link\Definition\HasLinksInterface;
use Psr\Messages\Link\Definition\Link;

/**
 * An error document rendered as JSON:API: an "errors" array where each member
 * carries code, title and detail, plus the optional source, links and meta when
 * the error provides them.
 */
final readonly class JsonApiErrorDocument extends ErrorDocument
{
    #[\Override]
    public function serialize(): array
    {
        return [
            'errors' => array_map($this->member(...), $this->errors),
            'jsonapi' => ['version' => JsonApiVersion::V1_1->value],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function member(ErrorInterface $error): array
    {
        $member = [
            'code' => $error->errorCode()->value,
            'title' => $error->title(),
            'detail' => $error->detail(),
        ];

        if ($error instanceof HasErrorSourceInterface && $error->hasSource()) {
            $source = $error->source();
            $member['source'] = [$source->type->value => $source->path];
        }

        if ($error instanceof HasLinksInterface && [] !== $error->links) {
            $member['links'] = Link::toArray($error->links);
        }

        if ($error instanceof HasMetaInterface && [] !== $error->meta) {
            $member['meta'] = $error->meta;
        }

        return $member;
    }
}
