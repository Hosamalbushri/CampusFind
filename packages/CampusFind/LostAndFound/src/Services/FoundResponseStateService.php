<?php

namespace CampusFind\LostAndFound\Services;

use CampusFind\LostAndFound\Enums\FoundResponseStatus;
use DomainException;

class FoundResponseStateService
{
    /** @var array<string, list<FoundResponseStatus>> */
    private const TRANSITIONS = [
        'submitted' => [FoundResponseStatus::UNDER_REVIEW, FoundResponseStatus::REJECTED, FoundResponseStatus::CANCELLED],
        'under_review' => [FoundResponseStatus::VERIFIED, FoundResponseStatus::REJECTED, FoundResponseStatus::CANCELLED],
        'verified' => [],
        'rejected' => [],
        'cancelled' => [],
    ];

    public static function transition(FoundResponseStatus $from, FoundResponseStatus $to): FoundResponseStatus
    {
        if (! in_array($to, self::TRANSITIONS[$from->value] ?? [], true)) {
            throw new DomainException("Invalid found-response transition [{$from->value}] -> [{$to->value}].");
        }

        return $to;
    }
}
