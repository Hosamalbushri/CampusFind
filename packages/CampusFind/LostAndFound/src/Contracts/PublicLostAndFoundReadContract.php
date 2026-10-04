<?php

namespace CampusFind\LostAndFound\Contracts;

use CampusFind\LostAndFound\DataTransferObjects\PublicCategoryData;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemData;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemSearchCriteria;
use CampusFind\LostAndFound\DataTransferObjects\PublicFoundItemSearchResult;
use CampusFind\LostAndFound\DataTransferObjects\PublicLostReportData;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchCriteria;
use CampusFind\LostAndFound\DataTransferObjects\PublicUnifiedSearchResult;

interface PublicLostAndFoundReadContract
{
    /**
     * Retrieve recent public-safe found items for public presentation.
     *
     * @param  int  $limit  Maximum number of items to return (clamped between 1 and 24)
     * @return list<PublicFoundItemData>
     */
    public function getRecentPublicFoundItems(int $limit = 6): array;

    /**
     * Search and filter public-safe found items with bounded pagination.
     */
    public function searchPublicFoundItems(PublicFoundItemSearchCriteria $criteria): PublicFoundItemSearchResult;

    /**
     * Search LOST reports and FOUND items through one public-safe query and paginator.
     */
    public function searchPublicRecords(PublicUnifiedSearchCriteria $criteria): PublicUnifiedSearchResult;

    /**
     * Find a single public-safe found item by its unique public reference.
     * Returns null if the item does not exist or is in a non-public state (draft, returned, disposed).
     */
    public function findPublicFoundItemByReference(string $reference): ?PublicFoundItemData;

    public function findPublicLostReportByReference(string $reference): ?PublicLostReportData;

    /**
     * Retrieve active categories for public search filtering.
     *
     * @return list<PublicCategoryData>
     */
    public function getPublicCategories(): array;
}
