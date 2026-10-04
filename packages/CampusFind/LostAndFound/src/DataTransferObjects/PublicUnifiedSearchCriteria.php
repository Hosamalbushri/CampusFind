<?php

namespace CampusFind\LostAndFound\DataTransferObjects;

use DateTimeImmutable;

readonly class PublicUnifiedSearchCriteria
{
    public const TYPES = ['all', 'lost', 'found'];

    public const STATUSES = ['active', 'reported', 'in_custody'];

    public const SORTS = ['newest', 'oldest', 'title_asc', 'title_desc'];

    public ?string $query;

    public string $type;

    public ?string $category;

    public ?string $location;

    public ?DateTimeImmutable $dateFrom;

    public ?DateTimeImmutable $dateTo;

    public ?string $status;

    public string $sort;

    public int $page;

    public int $perPage;

    public function __construct(
        ?string $query = null,
        ?string $type = null,
        ?string $category = null,
        ?string $location = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?string $status = null,
        ?string $sort = null,
        int $page = 1,
        int $perPage = 12,
    ) {
        $this->query = $this->normalizeText($query, 100);
        $this->type = in_array($type, self::TYPES, true) ? $type : 'all';
        $this->category = $this->normalizeText($category, 64);
        $this->location = $this->normalizeText($location, 100);
        $this->dateFrom = $this->normalizeDate($dateFrom);
        $this->dateTo = $this->normalizeDate($dateTo);
        $this->status = in_array($status, self::STATUSES, true) ? $status : null;
        $this->sort = in_array($sort, self::SORTS, true) ? $sort : 'newest';
        $this->page = max(1, $page);
        $this->perPage = max(1, min($perPage, 36));
    }

    private function normalizeText(?string $value, int $limit): ?string
    {
        $value = $value !== null ? trim($value) : null;

        return $value !== null && $value !== '' ? mb_substr($value, 0, $limit) : null;
    }

    private function normalizeDate(?string $value): ?DateTimeImmutable
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', trim($value));

        return $date && $date->format('Y-m-d') === trim($value) ? $date : null;
    }
}
