<?php

namespace CampusFind\LostAndFound\DataTransferObjects;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

readonly class PublicUnifiedSearchResult implements Arrayable, JsonSerializable
{
    /** @param list<PublicUnifiedRecordData> $items */
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $currentPage,
        public int $lastPage,
    ) {}

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function isNotEmpty(): bool
    {
        return $this->items !== [];
    }

    public function hasPages(): bool
    {
        return $this->lastPage > 1;
    }

    public function previousPage(): ?int
    {
        return $this->currentPage > 1 ? $this->currentPage - 1 : null;
    }

    public function nextPage(): ?int
    {
        return $this->currentPage < $this->lastPage ? $this->currentPage + 1 : null;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'items' => array_map(
                static fn (PublicUnifiedRecordData $item): array => $item->toArray(),
                $this->items,
            ),
            'total' => $this->total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
