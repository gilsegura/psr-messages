<?php

declare(strict_types=1);

namespace Psr\Messages\JsonApi\Pagination;

use Psr\Messages\JsonApi\Query\Definition\Page;
use Psr\Messages\Link\Definition\Href;
use Psr\Messages\Link\Definition\Link;

/**
 * Reusable JSON:API pagination. Given the requested page, the total number of
 * records and the request path, it produces the standard pagination links
 * (self/first/last/prev/next) and the page meta. The format is the fixed JSON:API
 * one, so it is endpoint- and application-agnostic.
 *
 * The page of records is fetched by the caller through its query handler; only
 * the total is read separately (a count on the read model) and passed in here.
 */
final readonly class Pagination
{
    /**
     * @return array<string, mixed>
     */
    public function meta(Page $page, int $total): array
    {
        return [
            'page' => [
                'number' => $page->number,
                'size' => $page->size,
                'total' => $total,
                'pages' => $this->lastPage($page, $total),
            ],
        ];
    }

    /**
     * @return Link[]
     */
    public function links(Page $page, int $total, Href $path): array
    {
        $lastPage = $this->lastPage($page, $total);

        $links = [
            Link::self($this->href($path, $page->number, $page->size)),
            Link::first($this->href($path, 1, $page->size)),
            Link::last($this->href($path, $lastPage, $page->size)),
        ];

        if ($page->number > 1) {
            $links[] = Link::prev($this->href($path, $page->number - 1, $page->size));
        }

        if ($page->number < $lastPage) {
            $links[] = Link::next($this->href($path, $page->number + 1, $page->size));
        }

        return $links;
    }

    private function lastPage(Page $page, int $total): int
    {
        return max(1, (int) ceil($total / $page->size));
    }

    private function href(Href $path, int $number, int $size): Href
    {
        return $path->withQueryParams(['page' => ['number' => $number, 'size' => $size]]);
    }
}
