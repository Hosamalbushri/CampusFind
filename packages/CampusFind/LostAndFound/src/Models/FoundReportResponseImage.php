<?php

namespace CampusFind\LostAndFound\Models;

use Illuminate\Database\Eloquent\Model;
use LogicException;

class FoundReportResponseImage extends Model
{
    protected $table = 'lost_found_report_response_images';

    protected $fillable = ['response_id', 'storage_key', 'storage_key_hash', 'mime_type', 'byte_size', 'submitted_at'];

    protected $hidden = ['response_id', 'storage_key', 'storage_key_hash', 'mime_type', 'byte_size'];

    protected $casts = [
        'storage_key' => 'encrypted',
        'byte_size' => 'integer',
        'submitted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(static function (): never {
            throw new LogicException('Found-response images are append-only.');
        });
        static::deleting(static function (): never {
            throw new LogicException('Found-response images are append-only.');
        });
    }

    public function response()
    {
        return $this->belongsTo(FoundReportResponse::class, 'response_id');
    }
}
