<?php

namespace CampusFind\LostAndFound\Enums;

enum FoundItemSubmissionChannel: string
{
    case LEGACY_UNCERTAIN = 'legacy_uncertain';
    case PUBLIC_ANONYMOUS = 'public_anonymous';
    case STUDENT_SELF_SERVICE = 'student_self_service';
    case EMPLOYEE_ASSISTED = 'employee_assisted';
    case SYSTEM_AUTOMATION = 'system_automation';
}
