<?php

namespace CampusFind\LostAndFound\Models;

use CampusFind\LostAndFound\Enums\FoundResponseStatus;
use CampusFind\LostAndFound\Services\PublicReference;
use CampusFind\Student\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Webkul\User\Models\User;

class FoundReportResponse extends Model
{
    protected $table = 'lost_found_report_responses';

    protected $fillable = [
        'public_reference',
        'lost_report_id',
        'responder_student_id',
        'reviewer_user_id',
        'resulting_found_item_id',
        'status',
        'found_location',
        'found_at',
        'dropoff_location',
        'message',
        'submitted_at',
        'review_started_at',
        'verified_at',
        'rejected_at',
        'cancelled_at',
    ];

    protected $hidden = [
        'public_reference_key',
        'lost_report_id',
        'responder_student_id',
        'reviewer_user_id',
        'resulting_found_item_id',
        'message',
    ];

    protected $casts = [
        'status' => FoundResponseStatus::class,
        'message' => 'encrypted',
        'found_at' => 'datetime',
        'submitted_at' => 'datetime',
        'review_started_at' => 'datetime',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function setPublicReferenceAttribute(string $value): void
    {
        $reference = PublicReference::fromString($value);
        $this->attributes['public_reference'] = $reference->getValue();
        $this->attributes['public_reference_key'] = PublicReference::normalize($value);
    }

    public function lostReport()
    {
        return $this->belongsTo(LostReport::class, 'lost_report_id');
    }

    public function responder()
    {
        return $this->belongsTo(Student::class, 'responder_student_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_user_id');
    }

    public function resultingFoundItem()
    {
        return $this->belongsTo(FoundItem::class, 'resulting_found_item_id');
    }

    public function images()
    {
        return $this->hasMany(FoundReportResponseImage::class, 'response_id')->orderBy('id');
    }

    public function reviews()
    {
        return $this->hasMany(FoundReportResponseReview::class, 'response_id')->orderBy('id');
    }
}
