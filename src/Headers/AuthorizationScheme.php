<?php

declare(strict_types=1);

namespace Psr\Messages\Headers;

enum AuthorizationScheme: string
{
    case BEARER = 'Bearer';
    case BASIC = 'Basic';

    public function equals(AuthorizationScheme $scheme): bool
    {
        return $this === $scheme;
    }
}
