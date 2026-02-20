<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonationMilestoneResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'target_amount' => $this->target_amount,
            'current_amount' => $this->current_amount,
            'remaining_amount' => $this->remaining_amount,
            'progress_percentage' => $this->progress_percentage,
            'currency' => $this->currency,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'donors_count' => $this->donors_count,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
