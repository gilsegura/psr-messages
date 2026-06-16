<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Document\Definition;

/**
 * The JSON:API specification version, emitted as the top-level "jsonapi"
 * member. This library implements 1.1.
 */
enum JsonApiVersion: string
{
    case V1_1 = '1.1';
}
