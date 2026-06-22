<?php

namespace App\Dtos;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class PaginatedData
{
    /**
     * @param  Collection<int, mixed>  $items
     */
    public function __construct(
        public Collection $items,
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
        public ?string $nextPageUrl,
        public ?string $prevPageUrl,
        public string $path,
    ) {}

    /**
     * @param  callable(mixed): mixed  $mapper
     */
    public static function fromLengthAwarePaginator(LengthAwarePaginator $paginator, callable $mapper): self
    {
        return new self(
            items: $paginator->getCollection()->map($mapper),
            total: $paginator->total(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            lastPage: $paginator->lastPage(),
            nextPageUrl: $paginator->nextPageUrl(),
            prevPageUrl: $paginator->previousPageUrl(),
            path: $paginator->path(),
        );
    }

    public function toPaginator(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $this->items,
            $this->total,
            $this->perPage,
            $this->currentPage,
            ['path' => $this->path],
        );
    }
}
