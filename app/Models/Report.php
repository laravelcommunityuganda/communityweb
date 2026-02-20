<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Report reasons
     */
    const REASON_SPAM = 'spam';
    const REASON_HARASSMENT = 'harassment';
    const REASON_INAPPROPRIATE = 'inappropriate';
    const REASON_OFF_TOPIC = 'off_topic';
    const REASON_OTHER = 'other';

    /**
     * Report statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    /**
     * Get the parent reportable model.
     */
    public function reportable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who reported.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who resolved the report.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope for pending reports.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
