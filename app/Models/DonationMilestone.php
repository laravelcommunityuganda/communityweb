<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'current_amount',
        'currency',
        'start_date',
        'end_date',
        'is_active',
        'is_featured',
        'image',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the donations for this milestone.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'milestone_id');
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0;
        }
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    /**
     * Get the remaining amount.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->target_amount - $this->current_amount);
    }

    /**
     * Get the total donors count.
     */
    public function getDonorsCountAttribute(): int
    {
        return $this->donations()->where('payment_status', 'completed')->count();
    }

    /**
     * Scope for active milestones.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured milestones.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Update the current amount from completed donations.
     */
    public function updateCurrentAmount(): void
    {
        $this->current_amount = $this->donations()
            ->where('payment_status', 'completed')
            ->sum('amount');
        $this->save();
    }
}
