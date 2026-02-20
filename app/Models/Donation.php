<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'milestone_id',
        'transaction_id',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'donor_name',
        'donor_email',
        'message',
        'is_anonymous',
        'payment_details',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'payment_details' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Donation $donation) {
            if (empty($donation->transaction_id)) {
                $donation->transaction_id = 'DON-' . strtoupper(Str::random(12));
            }
        });

        static::updated(function (Donation $donation) {
            if ($donation->isDirty('payment_status') && $donation->payment_status === 'completed') {
                $donation->milestone?->updateCurrentAmount();
            }
        });
    }

    /**
     * Get the user who made the donation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the milestone this donation is for.
     */
    public function milestone(): BelongsTo
    {
        return $this->belongsTo(DonationMilestone::class, 'milestone_id');
    }

    /**
     * Get the donor's display name.
     */
    public function getDonorDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }
        return $this->donor_name ?? $this->user?->name ?? 'Anonymous';
    }

    /**
     * Scope for completed donations.
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope for pending donations.
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Check if the donation is completed.
     */
    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Mark the donation as completed.
     */
    public function markAsCompleted(array $paymentDetails = []): void
    {
        $this->update([
            'payment_status' => 'completed',
            'payment_details' => array_merge($this->payment_details ?? [], $paymentDetails),
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark the donation as failed.
     */
    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'payment_details' => array_merge($this->payment_details ?? [], ['failure_reason' => $reason]),
        ]);
    }
}
