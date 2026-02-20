<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'donor_name' => $this->donor_display_name,
            'donor_email' => $this->is_anonymous ? null : $this->donor_email,
            'message' => $this->message,
            'is_anonymous' => $this->is_anonymous,
            'milestone' => new DonationMilestoneResource($this->whenLoaded('milestone')),
            'paid_at' => $this->paid_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
