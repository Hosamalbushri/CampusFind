<?php

namespace CampusFind\LostAndFound\Services\Matching;

use CampusFind\LostAndFound\DataTransferObjects\AssistedMatchResult;
use CampusFind\LostAndFound\Models\FoundItem;
use CampusFind\LostAndFound\Models\LostReport;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

class AssistedMatchScorer
{
    public function __construct(private MatchTextNormalizer $normalizer) {}

    public function score(LostReport $report, FoundItem $item): AssistedMatchResult
    {
        $weights = config('lost_found.matching.weights', []);
        $weightTotal = array_sum($weights);

        if ($weightTotal <= 0
            || array_diff(['category', 'description', 'location', 'temporal'], array_keys($weights)) !== []
            || min(array_map('floatval', array_values($weights))) <= 0) {
            throw new InvalidArgumentException('Assisted matching requires four positive configured signal weights.');
        }

        $reportDescription = implode(' ', array_filter([
            $report->title,
            $report->public_description,
            $report->private_description,
        ]));
        $itemDescription = implode(' ', array_filter([
            $item->title,
            $item->public_description,
            $item->privateDetail?->identifying_details,
            $item->privateDetail?->serial_fragment,
        ]));

        $factors = [
            'category' => $report->category_id !== null && (int) $report->category_id === (int) $item->category_id ? 1.0 : 0.0,
            'description' => $this->normalizer->similarity($reportDescription, $itemDescription),
            'location' => $this->normalizer->similarity($report->lost_location, $item->found_location),
            'temporal' => $this->temporalCompatibility($report->lost_at, $item->found_at),
        ];

        $signals = [];
        $score = 0;

        foreach ($factors as $name => $factor) {
            $maxPoints = (int) round(((float) $weights[$name] / $weightTotal) * 10_000);
            $points = $factor === null ? 0 : (int) round($maxPoints * $factor);
            $score += $points;
            $signals[$name] = [
                'available' => $factor !== null,
                'factor_basis_points' => $factor === null ? null : (int) round($factor * 10_000),
                'points' => $points,
                'max_points' => $maxPoints,
                'explanation' => $this->explanationKey($name, $factor),
            ];
        }

        $algorithmVersion = (string) config('lost_found.matching.algorithm_version', 'deterministic-v1');
        $fingerprintPayload = [
            'algorithm' => $algorithmVersion,
            'report' => [
                'category' => $report->category_id,
                'description' => $this->normalizer->normalize($reportDescription),
                'location' => $this->normalizer->normalize($report->lost_location),
                'date' => $report->lost_at?->toIso8601String(),
            ],
            'item' => [
                'category' => $item->category_id,
                'description' => $this->normalizer->normalize($itemDescription),
                'location' => $this->normalizer->normalize($item->found_location),
                'date' => $item->found_at?->toIso8601String(),
            ],
        ];

        return new AssistedMatchResult(
            lostReportId: (int) $report->getKey(),
            foundItemId: (int) $item->getKey(),
            scoreBasisPoints: min(10_000, $score),
            signals: $signals,
            inputFingerprint: hash('sha256', json_encode($fingerprintPayload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)),
            algorithmVersion: $algorithmVersion,
        );
    }

    private function temporalCompatibility(?Carbon $lostAt, ?Carbon $foundAt): ?float
    {
        if ($lostAt === null || $foundAt === null) {
            return null;
        }

        if ($foundAt->lessThan($lostAt)) {
            return 0.0;
        }

        $decayDays = max(1, (int) config('lost_found.matching.temporal_decay_days', 30));

        return 1 / (1 + ($lostAt->diffInSeconds($foundAt) / 86_400 / $decayDays));
    }

    private function explanationKey(string $signal, ?float $factor): string
    {
        if ($factor === null) {
            return "{$signal}_missing";
        }

        if ($signal === 'category') {
            return $factor === 1.0 ? 'category_match' : 'category_mismatch';
        }

        if ($signal === 'temporal' && $factor === 0.0) {
            return 'temporal_incompatible';
        }

        return match (true) {
            $factor >= 0.75 => "{$signal}_strong",
            $factor >= 0.4 => "{$signal}_moderate",
            default => "{$signal}_weak",
        };
    }
}
