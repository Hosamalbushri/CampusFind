<?php

namespace CampusFind\LostAndFound\Enums;

enum FoundResponseStatus: string
{
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
}
