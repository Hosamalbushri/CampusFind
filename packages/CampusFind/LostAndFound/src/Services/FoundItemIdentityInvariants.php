<?php

namespace CampusFind\LostAndFound\Services;

use CampusFind\LostAndFound\Enums\FoundItemSubmissionChannel;
use CampusFind\LostAndFound\Models\FoundItem;
use LogicException;

class FoundItemIdentityInvariants
{
    public const FIELDS = [
        'logged_by_user_id',
        'submission_channel',
        'reporter_student_id',
        'submitted_by_student_id',
        'intake_employee_user_id',
    ];

    public static function prepareAndAssert(FoundItem $item): void
    {
        $channel = $item->submission_channel ?? FoundItemSubmissionChannel::LEGACY_UNCERTAIN;
        $channel = $channel instanceof FoundItemSubmissionChannel
            ? $channel
            : FoundItemSubmissionChannel::from((string) $channel);
        $item->submission_channel = $channel;

        $valid = match ($channel) {
            FoundItemSubmissionChannel::LEGACY_UNCERTAIN => true,
            FoundItemSubmissionChannel::PUBLIC_ANONYMOUS,
            FoundItemSubmissionChannel::SYSTEM_AUTOMATION => self::allHumanActorsAreNull($item),
            FoundItemSubmissionChannel::STUDENT_SELF_SERVICE => $item->reporter_student_id !== null
                && (int) $item->reporter_student_id === (int) $item->submitted_by_student_id
                && $item->logged_by_user_id === null
                && $item->intake_employee_user_id === null,
            FoundItemSubmissionChannel::EMPLOYEE_ASSISTED => $item->intake_employee_user_id !== null
                && (int) $item->intake_employee_user_id === (int) $item->logged_by_user_id
                && $item->submitted_by_student_id === null,
        };

        if (! $valid) {
            throw new LogicException("Found-item identity is inconsistent with submission channel [{$channel->value}].");
        }
    }

    private static function allHumanActorsAreNull(FoundItem $item): bool
    {
        return $item->logged_by_user_id === null
            && $item->reporter_student_id === null
            && $item->submitted_by_student_id === null
            && $item->intake_employee_user_id === null;
    }
}
