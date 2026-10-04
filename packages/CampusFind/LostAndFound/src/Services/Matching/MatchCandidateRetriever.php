<?php

namespace CampusFind\LostAndFound\Services\Matching;

use CampusFind\LostAndFound\Enums\ItemStatus;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostReport;
use Illuminate\Database\Eloquent\Collection;

class MatchCandidateRetriever
{
    /** @return Collection<int, FoundItem> */
    public function forLostReport(LostReport $report): Collection
    {
        if ($report->category_id === null) {
            return new Collection;
        }

        return FoundItem::query()
            ->with('privateDetail')
            ->where('category_id', $report->category_id)
            ->whereIn('status', [ItemStatus::REPORTED->value, ItemStatus::IN_CUSTODY->value])
            ->whereDoesntHave('verifiedReportLink')
            ->orderByDesc('status')
            ->orderByDesc('found_at')
            ->orderByDesc('id')
            ->limit($this->limit())
            ->get();
    }

    /** @return Collection<int, LostReport> */
    public function forFoundItem(FoundItem $item): Collection
    {
        if ($item->category_id === null) {
            return new Collection;
        }

        return LostReport::query()
            ->where('category_id', $item->category_id)
            ->where('status', ReportStatus::ACTIVE->value)
            ->whereNull('resolved_found_item_id')
            ->whereDoesntHave('verifiedItemLink')
            ->orderByDesc('lost_at')
            ->orderByDesc('id')
            ->limit($this->limit())
            ->get();
    }

    private function limit(): int
    {
        return max(1, min(500, (int) config('lost_found.matching.candidate_limit', 100)));
    }
}
