<?php

namespace CampusFind\LostAndFound\DataTransferObjects;

final readonly class AssistedMatchResult
{
    /** @param array<string, array<string, int|string|bool|null>> $signals */
    public function __construct(
        public int $lostReportId,
        public int $foundItemId,
        public int $scoreBasisPoints,
        public array $signals,
        public string $inputFingerprint,
        public string $algorithmVersion,
    ) {}
}
