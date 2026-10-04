<?php

namespace CampusFind\LostAndFound\Services;

use InvalidArgumentException;
use CampusFind\LostAndFound\Enums\ClaimStatus;
use CampusFind\LostAndFound\Enums\ItemStatus;

class SecurityInvariants
{
    private const PROHIBITED_KEYWORDS = [
        'password',
        'passcode',
        'pin',
        'unlock pattern',
        'device unlock code',
        'authentication token',
        'security answer',
        'otp',
        'recovery code',
        'secret key',
        // Arabic equivalents
        'كلمة المرور',
        'كلمة السر',
        'الرقم السري',
        'رقم سري',
        'رمز السري',
        'رمز الدخول',
        'رمز التحقق',
        'نمط القفل',
        'رمز القفل',
        'رمز الامان',
        'رمز الأمان',
        'رمز الاسترداد',
        'مفتاح سري',
    ];

    public static function assertNoAuthSecrets(string $text): void
    {
        $lowercase = mb_strtolower($text, 'UTF-8');

        foreach (self::PROHIBITED_KEYWORDS as $keyword) {
            if (str_contains($lowercase, $keyword)) {
                throw new InvalidArgumentException(
                    "Security Violation: Verification material cannot contain authentication secrets ({$keyword})."
                );
            }
        }
    }

    public static function assertHandoverEligible(ItemStatus $itemStatus, ClaimStatus $claimStatus): void
    {
        if ($itemStatus->isTerminal()) {
            throw new InvalidArgumentException("Item is in terminal status [{$itemStatus->value}] and cannot be handed over.");
        }

        if ($claimStatus !== ClaimStatus::APPROVED) {
            throw new InvalidArgumentException("Handover requires an APPROVED claim (Current claim status: {$claimStatus->value}).");
        }
    }
}
