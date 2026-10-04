<?php

namespace CampusFind\LostAndFound\Models;

use CampusFind\LostAndFound\Enums\FoundResponseStatus;
use Illuminate\Database\Eloquent\Model;
use LogicException;
use Webkul\User\Models\User;

class FoundReportResponseReview extends Model
{
    protected $table = 'lost_found_report_response_reviews';

    protected $fillable = ['response_id', 'reviewer_user_id', 'from_status', 'to_status', 'staff_notes', 'reviewed_at'];

    protected $hidden = ['response_id', 'reviewer_user_id', 'staff_notes'];

    protected $casts = [
        'from_status' => FoundResponseStatus::class,
        'to_status' => FoundResponseStatus::class,
        'staff_notes' => 'encrypted',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Found-response review history is append-only.');
        });
        static::deleting(static function (): never {
            throw new LogicException('Found-response review history is append-only.');
        });
    }

    public function response()
    {
        return $this->belongsTo(FoundReportResponse::class, 'response_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }
}
