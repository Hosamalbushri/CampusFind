<?php

namespace CampusFind\LostAndFound\Models;

use CampusFind\LostAndFound\Enums\MatchReviewDecision;
use Illuminate\Database\Eloquent\Model;
use LogicException;
use Webkul\User\Models\UserProxy;

class PotentialReportItemMatch extends Model
{
    protected $table = 'lost_found_potential_matches';

    protected $fillable = [
        'lost_report_id',
        'found_item_id',
        'proposed_by_user_id',
        'proposed_at',
    ];

    protected $casts = [
        'proposed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Potential match history is immutable.');
        });

        static::deleting(static function (): never {
            throw new LogicException('Potential match history is immutable.');
        });
    }

    public function lostReport()
    {
        return $this->belongsTo(LostReportProxy::modelClass(), 'lost_report_id');
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItemProxy::modelClass(), 'found_item_id');
    }

    public function proposedBy()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'proposed_by_user_id');
    }

    public function verifiedLink()
    {
        return $this->hasOne(VerifiedReportItemLink::class, 'potential_match_id');
    }

    public function snapshots()
    {
        return $this->hasMany(MatchSuggestionSnapshot::class, 'potential_match_id');
    }

    public function latestSnapshot()
    {
        return $this->hasOne(MatchSuggestionSnapshot::class, 'potential_match_id')->latestOfMany();
    }

    public function reviews()
    {
        return $this->hasMany(MatchSuggestionReview::class, 'potential_match_id');
    }

    public function latestReview()
    {
        return $this->hasOne(MatchSuggestionReview::class, 'potential_match_id')->latestOfMany();
    }

    public function lifecycleStatus(): string
    {
        if ($this->verifiedLink !== null) {
            return 'verified';
        }

        if ($this->latestReview?->decision === MatchReviewDecision::REJECTED) {
            return 'rejected';
        }

        if ($this->latestReview?->decision === MatchReviewDecision::REVIEWED
            && ($this->latestSnapshot?->generated_at === null
                || $this->latestSnapshot->generated_at->lessThanOrEqualTo($this->latestReview->reviewed_at))) {
            return 'reviewed';
        }

        return 'suggested';
    }
}
