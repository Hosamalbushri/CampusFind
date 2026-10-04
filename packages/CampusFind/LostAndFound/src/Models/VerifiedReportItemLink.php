<?php

namespace CampusFind\LostAndFound\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;
use Webkul\User\Models\UserProxy;

class VerifiedReportItemLink extends Model
{
    protected $table = 'lost_found_verified_links';

    protected $fillable = [
        'potential_match_id',
        'lost_report_id',
        'found_item_id',
        'verified_by_user_id',
        'verification_evidence',
        'verified_at',
    ];

    protected $hidden = [
        'verification_evidence',
    ];

    protected $casts = [
        'verification_evidence' => 'encrypted',
        'verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Verified report-item relationship history is immutable.');
        });

        static::deleting(static function (): never {
            throw new LogicException('Verified report-item relationship history is immutable.');
        });
    }

    public function potentialMatch()
    {
        return $this->belongsTo(PotentialReportItemMatch::class, 'potential_match_id');
    }

    public function lostReport()
    {
        return $this->belongsTo(LostReportProxy::modelClass(), 'lost_report_id');
    }

    public function foundItem()
    {
        return $this->belongsTo(FoundItemProxy::modelClass(), 'found_item_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(UserProxy::modelClass(), 'verified_by_user_id');
    }
}
