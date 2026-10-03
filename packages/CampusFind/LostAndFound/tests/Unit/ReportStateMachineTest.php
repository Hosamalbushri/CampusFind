<?php

namespace CampusFind\LostAndFound\Tests\Unit;

use InvalidArgumentException;
use CampusFind\LostAndFound\Tests\TestCase;
use CampusFind\LostAndFound\Enums\ReportStatus;
use CampusFind\LostAndFound\Services\ReportStateService;

class ReportStateMachineTest extends TestCase
{
    public function test_valid_report_state_transitions(): void
    {
        $this->assertTrue(ReportStateService::canTransition(ReportStatus::DRAFT, ReportStatus::ACTIVE));
        $this->assertTrue(ReportStateService::canTransition(ReportStatus::ACTIVE, ReportStatus::RESOLVED));
    }

    public function test_invalid_report_state_transitions_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ReportStateService::transition(ReportStatus::RESOLVED, ReportStatus::ACTIVE);
    }
}
