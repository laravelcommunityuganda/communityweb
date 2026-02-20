<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReputationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'action',
        'source_type',
        'source_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function scopePositive($query)
    {
        return $query->where('points', '>', 0);
    }

    public function scopeNegative($query)
    {
        return $query->where('points', '<', 0);
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function isPositive(): bool
    {
        return $this->points > 0;
    }

    public function isNegative(): bool
    {
        return $this->points < 0;
    }
}
