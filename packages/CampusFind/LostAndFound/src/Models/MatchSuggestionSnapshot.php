<?php

namespace CampusFind\LostAndFound\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;
use Webkul\User\Models\UserProxy;

class MatchSuggestionSnapshot extends Model
{
    protected $table = 'lost_found_match_snapshots';

    protected $fillable = [
        'potential_match_id',
        'generated_by_user_id',
        'algorithm_version',
        'score_basis_points',
        'signals',
        'input_fingerprint',
        'generated_at',
    ];

    protected $hidden = ['input_fingerprint'];

    protected $casts = [
        'score_basis_points' => 'integer',
        'signals' => 'array',
        'generated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Assisted-match snapshots are append-only.');
        });

        static::deleting(static function (): never {
            throw new LogicException('Assisted-match snapshots are append-only.');
        });
    }

    public function potentialMatch()
    {
        return $this->belongsTo(PotentialReportItemMatch::class, 'potential_match_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'generated_by_user_id');
    }
}
