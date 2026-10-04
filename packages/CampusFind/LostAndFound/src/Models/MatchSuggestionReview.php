<?php

namespace CampusFind\LostAndFound\Models;

use CampusFind\LostAndFound\Enums\MatchReviewDecision;
use Illuminate\Database\Eloquent\Model;
use LogicException;
use Webkul\User\Models\UserProxy;

class MatchSuggestionReview extends Model
{
    protected $table = 'lost_found_match_reviews';

    protected $fillable = [
        'potential_match_id',
        'reviewer_user_id',
        'decision',
        'notes',
        'reviewed_at',
    ];

    protected $hidden = ['notes'];

    protected $casts = [
        'decision' => MatchReviewDecision::class,
        'notes' => 'encrypted',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Assisted-match review history is append-only.');
        });

        static::deleting(static function (): never {
            throw new LogicException('Assisted-match review history is append-only.');
        });
    }

    public function potentialMatch()
    {
        return $this->belongsTo(PotentialReportItemMatch::class, 'potential_match_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'reviewer_user_id');
    }
}
