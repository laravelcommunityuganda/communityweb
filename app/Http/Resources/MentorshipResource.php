<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company' => $this->company,
            'expertise' => $this->expertise,
            'experience_years' => $this->experience_years,
            'bio' => $this->bio,
            'availability' => $this->availability,
            'hourly_rate' => $this->hourly_rate,
            'rating' => $this->rating,
            'sessions_count' => $this->sessions_count,
            'is_available' => $this->is_available,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}